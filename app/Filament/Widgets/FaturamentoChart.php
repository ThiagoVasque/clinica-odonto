<?php

namespace App\Filament\Widgets;

use App\Models\Prontuario;
use Filament\Widgets\ChartWidget;

class FaturamentoChart extends ChartWidget
{
    protected static ?string $heading = 'Faturamento (Ganhos)';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;

    // 1. Aqui você define as opções que aparecerão no seletor
    protected function getFilters(): ?array
    {
        return [
            '7' => 'Últimos 7 dias',
            '30' => 'Últimos 30 dias',
            '90' => 'Últimos 90 dias',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? '30';
        $periodo = (int) $activeFilter;

        // Buscamos os orçamentos pagos e somamos o valor_total que você já tem no model
        $dadosFinanceiros = \App\Models\Orcamento::query()
            ->where('status', 'pago')
            ->where('updated_at', '>=', now()->subDays($periodo - 1))
            ->selectRaw('DATE(updated_at) as date, SUM(valor_total) as total')
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        $values = [];
        $labels = [];

        for ($i = $periodo - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d/m');
            // Garantimos que o valor seja numérico para o gráfico não quebrar
            $values[] = (float) ($dadosFinanceiros[$date] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ganhos (R$)',
                    'data' => $values,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
