<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;


class TotalAppointmentsChart extends ApexChartWidget
{
    use HasWidgetShield;
    protected static ?string $chartId = 'totalAppointmentsChart';
    protected static ?string $heading = 'Total de Citas Reservadas';
    protected static ?string $subheading = 'Todos los datos';

    protected function getOptions(): array
    {
        $appointments = Appointment::select(DB::raw('DATE(start_time) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = $appointments->pluck('date')->toArray();
        $totals = $appointments->pluck('total')->toArray();

        return [
            'chart' => [
                'type' => 'line',
                'height' => 290,
            ],
            'series' => [
                [
                    'name' => 'Citas',
                    'data' => $totals,
                ],
            ],
            'xaxis' => [
                'categories' => $dates,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#1E90FF'],
        ];
    }
}
