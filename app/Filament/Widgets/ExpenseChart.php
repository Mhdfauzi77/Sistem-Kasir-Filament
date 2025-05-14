<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Expense;
use Carbon\Carbon;

class ExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Expense';
    protected static ?int $sort = 2;
    protected static string $color = 'danger';
    public ?string $filter = 'today';
    

    protected function getData(): array
    {
    $activeFilter = $this->filter;

    // Range berdasarkan filter yang dipilih
    $dateRange = match ($activeFilter) {
        'today' => [
            'start' => now()->startOfDay(),
            'end' => now()->endOfDay(),
            'period' => 'perHour',
        ],
        'week' => [
            'start' => now()->startOfWeek(),
            'end' => now()->endOfWeek(),
            'period' => 'perDay',
        ],
        'month' => [
            'start' => now()->startOfMonth(),
            'end' => now()->endOfMonth(),
            'period' => 'perDay',
        ],
        'year' => [
            'start' => now()->startOfYear(),
            'end' => now()->endOfYear(),
            'period' => 'perMonth',
        ],
        default => [
            'start' => now()->startOfDay(),
            'end' => now()->endOfDay(),
            'period' => 'perHour',
        ],
    };

    // Bangun query Trend
    $query = Trend::model(Expense::class)
        ->between(
            start: $dateRange['start'],
            end: $dateRange['end'],
        );

    if ($dateRange['period'] === 'perHour') {
        $data = $query->perHour();
    } elseif ($dateRange['period'] === 'perDay') {
        $data = $query->perDay();
    } else {
        $data = $query->perMonth();
    }


    $data = $data->sum('amount');

    // Buat label dan data points
    $labels = $data->map(function (TrendValue $value) use ($dateRange) {
         $date = Carbon::parse($value->date)->timezone('Asia/Jakarta');
        
        if ($dateRange['period'] === 'perHour') {
            return $date->format('H:i');
        } elseif ($dateRange['period'] === 'perDay') {
            return $date->format('d M');
        } else {
            return $date->format('M Y');
        }
    });

    return [
    'datasets' => [
        [
            'label' => 'Expense' . ($this->getFilters()[$activeFilter] ?? ''),
            'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            'borderColor' => 'rgb(255, 99, 132)', // merah
            'backgroundColor' => 'rgba(255, 99, 132, 0.5)', // merah transparan
            'fill' => false, // jika tidak mau area di bawah garis terisi
            'tension' => 0.3, // opsional: bikin garis agak melengkung
        ],
    ],
    'labels' => $labels,
];
}

    protected function getFilters(): ?array
{
    return [
        'today' => 'Today',
        'week' => 'Last week',
        'month' => 'Last month',
        'year' => 'This year',
    ];
}

    protected function getType(): string
    {
        return 'line';
    }
}
