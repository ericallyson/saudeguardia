<?php

namespace Tests\Unit;

use App\Services\PacienteDashboardService;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PacienteDashboardServiceTest extends TestCase
{
    public function test_it_normalizes_swapped_blood_pressure_values_for_chart(): void
    {
        $service = new PacienteDashboardService();

        $resposta = (object) [
            'valor' => '85x125',
            'respondido_em' => Carbon::create(2024, 1, 10, 8, 30),
        ];

        $method = new \ReflectionMethod(PacienteDashboardService::class, 'buildBloodPressureChart');
        $method->setAccessible(true);

        $chart = $method->invoke($service, collect([$resposta]));
        $points = $chart['chart']['points']->toArray();
        $point = $points[0];

        $this->assertSame(125, $point['x']);
        $this->assertSame(85, $point['y']);
        $this->assertSame('125 x 85', $point['valueLabel']);
    }
}
