<?php

namespace App\Filament\Widgets;

use App\Models\Agendamento;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PacientesChart extends ChartWidget
{
    protected static ?string $heading = 'Consultas por Dia';

    protected static ?int $sort = 3; // Fica depois dos Stats e da Tabela

    protected function getData(): array
    {
        // Buscamos os agendamentos dos últimos 7 dias
        $agendamentosPorDia = \App\Models\Agendamento::query()
            ->selectRaw('DATE(data_hora) as date, count(*) as total')
            ->where('data_hora', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Preparamos as labels (datas) e os valores
        $values = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d/m'); // Ex: 27/01
            $values[] = $agendamentosPorDia[$date] ?? 0; // Se não houver nada, coloca 0
        }

        return [
            'datasets' => [
                [
                    'label' => 'Consultas Reais',
                    'data' => $values,
                    'backgroundColor' => '#22d3ee',
                    'borderColor' => '#0891b2',
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
