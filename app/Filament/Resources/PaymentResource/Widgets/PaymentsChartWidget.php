<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class PaymentsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Payment';
    protected int | string | array $columnSpan = 12;
    protected static ?string $maxHeight = '250px';

    public ?string $filter = 'all';


    // public function getColumnSpan(): int | string | array
    // {
    //     return 12;
    // }

    public function getDescription(): ?string
    {
        return 'The number of payments per month.';
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

        $query = Payment::query();

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

        $paymentsByMonth = $query->selectRaw('COUNT(id) as count, SUM(amount) as total_amount, MONTH(created_at) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->get(['count', 'month', 'total_amount']);

        $labels = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];

        $paymentsData = array_fill(0, 12, 0);
        $amountsData = array_fill(0, 12, 0);

        foreach ($paymentsByMonth as $payment) {
            $paymentsData[$payment->month - 1] = $payment->count;
            $amountsData[$payment->month - 1] = $payment->total_amount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Number of Payments',
                    'data' => $paymentsData,
                    'borderColor' => '#00B496',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Total Amount',
                    'data' => $amountsData,
                    'borderColor' => '#FFA500',
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
