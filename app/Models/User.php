<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'whatsapp_instance_uuid',
        'whatsapp_instance_status',
        'subscription_customer_id',
        'subscription_id',
        'subscription_plan_id',
        'subscription_plan_name',
        'subscription_plan_slug',
        'subscription_status',
        'subscription_trial_ends_at',
        'subscription_next_renewal_date',
        'subscription_price',
        'subscription_metadata',
        'subscription_last_synced_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_trial_ends_at' => 'date',
            'subscription_next_renewal_date' => 'date',
            'subscription_last_synced_at' => 'datetime',
            'subscription_metadata' => 'array',
            'subscription_price' => 'decimal:2',
        ];
    }

    public function hasActiveSubscription(): bool
    {
        if (! $this->subscription_status) {
            return false;
        }

        $validStatuses = ['active', 'trial'];

        if (! in_array($this->subscription_status, $validStatuses, true)) {
            return false;
        }

        if ($this->subscription_status === 'trial'
            && $this->subscription_trial_ends_at
            && $this->subscription_trial_ends_at->lt(now()->startOfDay())) {
            return false;
        }

        if ($this->subscription_next_renewal_date
            && $this->subscription_next_renewal_date->lt(now()->startOfDay())) {
            return false;
        }

        return true;
    }

    public function pacientes(): HasMany
    {
        return $this->hasMany(Paciente::class);
    }
}
