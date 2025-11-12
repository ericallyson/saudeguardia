<?php

namespace App\Http\Controllers;

use App\Exceptions\SubscriptionException;
use App\Http\Requests\HomeRegistrationRequest;
use App\Models\User;
use App\Services\Subscriptions\SubscriptionClient;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class HomeController extends Controller
{
    public function __construct(private readonly SubscriptionClient $subscriptions)
    {
    }

    public function index(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $plans = [];
        $plansError = null;

        try {
            $plans = $this->subscriptions->listPlans();
        } catch (SubscriptionException $exception) {
            Log::warning('Unable to fetch subscription plans for landing page', $exception->context());
            $plansError = 'Não foi possível carregar os planos no momento. Tente novamente mais tarde.';
        }

        return view('home.index', [
            'plans' => $plans,
            'plansError' => $plansError,
        ]);
    }

    public function register(HomeRegistrationRequest $request): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $data = $request->validated();

        try {
            DB::beginTransaction();

            $user = User::withoutEvents(function () use ($data) {
                return User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
            });

            $plans = $this->subscriptions->listPlans();
            $plan = collect($plans)->firstWhere('id', (int) $data['plan_id']);

            if (! $plan) {
                throw ValidationException::withMessages([
                    'plan_id' => __('Plano selecionado é inválido ou não está mais disponível.'),
                ]);
            }

            [$firstName, $lastName] = $this->splitName($data['name']);

            $customerPayload = array_filter([
                'first_name' => $firstName ?: $data['email'],
                'last_name' => $lastName,
                'email' => $data['email'],
                'document_type' => $data['document_type'] ?? null,
                'document_number' => $data['document_number'] ?? null,
                'phone' => $data['phone'] ?? null,
                'status' => 'active',
                'notes' => 'Cadastro realizado pela landing page da Saúde Guardiã',
            ], fn ($value) => $value !== null && $value !== '');

            $customer = $this->subscriptions->createCustomer($customerPayload);
            $customerId = Arr::get($customer, 'id');

            if (! $customerId) {
                throw new SubscriptionException('Subscription API did not return a customer identifier.', [
                    'customer_response' => $customer,
                ]);
            }
            $planPrice = $this->resolvePlanPrice($plan);
            $today = Carbon::now();
            $startsAt = $today->toDateString();
            $trialDays = (int) ($plan['trial_days'] ?? 0);
            $trialEndsAt = $trialDays > 0 ? $today->copy()->addDays($trialDays)->toDateString() : null;
            $nextRenewalDate = $this->resolveNextRenewalDate($plan, $trialEndsAt ?? $startsAt);

            $subscriptionPayload = array_filter([
                'plan_id' => (int) $plan['id'],
                'status' => 'active',
                'starts_at' => $startsAt,
                'trial_ends_at' => $trialEndsAt,
                'next_renewal_date' => $nextRenewalDate,
                'price' => $planPrice,
                'metadata' => [
                    'source' => 'landing-page',
                    'plan_slug' => $plan['slug'] ?? null,
                    'plan_billing_period' => $plan['billing_period'] ?? null,
                    'user_email' => $data['email'],
                ],
            ], fn ($value) => $value !== null);

            $subscription = $this->subscriptions->createSubscription($customerId, $subscriptionPayload);

            $planData = Arr::get($subscription, 'plan', []);

            $user->forceFill([
                'subscription_customer_id' => Arr::get($customer, 'id'),
                'subscription_id' => Arr::get($subscription, 'id'),
                'subscription_plan_id' => Arr::get($planData, 'id'),
                'subscription_plan_name' => Arr::get($planData, 'name'),
                'subscription_plan_slug' => Arr::get($planData, 'slug'),
                'subscription_status' => Arr::get($subscription, 'status', 'active'),
                'subscription_trial_ends_at' => Arr::get($subscription, 'trial_ends_at', $trialEndsAt),
                'subscription_next_renewal_date' => Arr::get($subscription, 'next_renewal_date', $nextRenewalDate),
                'subscription_price' => Arr::get($subscription, 'price', $planPrice),
                'subscription_metadata' => [
                    'subscription' => Arr::get($subscription, 'metadata'),
                    'plan_features' => Arr::get($planData, 'features', $plan['features'] ?? []),
                ],
                'subscription_last_synced_at' => Carbon::now(),
            ])->save();

            DB::commit();

            $paymentUrl = Arr::get($subscription, 'payment_url')
                ?? Arr::get($subscription, 'checkout_url');

            if ($paymentUrl) {
                return redirect()->away($paymentUrl);
            }

            return redirect()->route('login')->with([
                'status' => __('Cadastro realizado com sucesso! Verifique seu e-mail para as instruções de pagamento.'),
            ]);
        } catch (ValidationException $exception) {
            DB::rollBack();

            throw $exception;
        } catch (SubscriptionException $exception) {
            DB::rollBack();
            Log::error('Falha ao cadastrar usuário via landing page', $exception->context() + [
                'email' => $data['email'],
                'plan_id' => $data['plan_id'],
            ]);

            return back()
                ->withErrors([
                    'registration' => __('Não foi possível concluir o cadastro agora. Tente novamente em instantes.'),
                ])
                ->withInput($request->safe()->except(['password', 'password_confirmation']));
        } catch (Throwable $exception) {
            DB::rollBack();
            Log::error('Erro inesperado ao cadastrar usuário via landing page', [
                'exception' => $exception,
                'email' => $data['email'],
                'plan_id' => $data['plan_id'],
            ]);

            return back()
                ->withErrors([
                    'registration' => __('Ocorreu um erro inesperado ao processar seu cadastro. Tente novamente.'),
                ])
                ->withInput($request->safe()->except(['password', 'password_confirmation']));
        }
    }

    /**
     * @param  array<string, mixed>  $plan
     */
    protected function resolvePlanPrice(array $plan): ?float
    {
        $period = $plan['billing_period'] ?? 'monthly';
        $prices = $plan['prices'] ?? [];

        $price = $prices[$period] ?? null;

        return $price !== null ? (float) $price : null;
    }

    /**
     * @param  array<string, mixed>  $plan
     */
    protected function resolveNextRenewalDate(array $plan, string $referenceDate): ?string
    {
        $period = $plan['billing_period'] ?? 'monthly';
        $start = Carbon::parse($referenceDate);

        return match ($period) {
            'yearly' => $start->copy()->addYear()->toDateString(),
            'quarterly' => $start->copy()->addMonths(3)->toDateString(),
            'semiannual', 'semi-annually' => $start->copy()->addMonths(6)->toDateString(),
            default => $start->copy()->addMonth()->toDateString(),
        };
    }

    /**
     * @return array{0: string|null, 1: string|null}
     */
    protected function splitName(string $name): array
    {
        $name = trim($name);

        if ($name === '') {
            return [null, null];
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $first = array_shift($parts);
        $last = $parts ? implode(' ', $parts) : null;

        return [$first, $last];
    }
}
