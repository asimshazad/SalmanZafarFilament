<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class ProductsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Products Added';

    public ?string $filter = 'all';

    public function getDescription(): ?string
    {
        return 'The number of products registered per month.';
    }

    protected function getFilters(): ?array
    {
        return [
            'all' => 'All',
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $query = Product::query();

        if ($activeFilter === 'today') {
            $query->whereDate('created_at', now()->toDateString());
        } elseif ($activeFilter === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($activeFilter === 'month') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        } elseif ($activeFilter === 'year') {
            $query->whereYear('created_at', now()->year);
        }

        $productsByMonth = $query->selectRaw('COUNT(id) as count, MONTH(created_at) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $labels = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];

        $data = array_fill(0, 12, 0);

        foreach ($productsByMonth as $month => $count) {
            $data[$month - 1] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Products',
                    'data' => $data,
                    'borderColor' => '#2196F3',
                    'tension' => 0.3,
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
