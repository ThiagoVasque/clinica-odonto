<?php

namespace App\Filament\Widgets;

use App\Models\Agendamento;
use Filament\Widgets\ChartWidget;

class PacientesChart extends ChartWidget
{
    protected static ?string $heading = 'Consultas na Semana';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1; 

    protected static ?array $options = [
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'ticks' => ['stepSize' => 1, 'precision' => 0],
                'grid' => ['display' => false],
            ],
            'x' => [
                'grid' => ['display' => false],
            ],
        ],
        'plugins' => [
            'legend' => ['display' => false],
        ],
    ];

    protected function getData(): array
    {
        $agendamentosPorDia = Agendamento::query()
            ->selectRaw('DATE(data_hora) as date, count(*) as total')
            ->where('data_hora', '>=', now()->subDays(6))
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        $values = [];
        $labels = [];

        $diasSemana = [
            'Sunday' => 'Dom', 'Monday' => 'Seg', 'Tuesday' => 'Ter',
            'Wednesday' => 'Qua', 'Thursday' => 'Qui', 'Friday' => 'Sex', 'Saturday' => 'Sáb'
        ];

        for ($i = 6; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $date = $dateObj->format('Y-m-d');
            $labels[] = $diasSemana[$dateObj->format('l')] . ' (' . $dateObj->format('d/m') . ')';
            $values[] = $agendamentosPorDia[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Consultas',
                    'data' => $values,
                    'backgroundColor' => '#4f46e5',
                    'borderRadius' => 4,
                    'barPercentage' => 0.6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}