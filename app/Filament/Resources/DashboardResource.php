<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Widgets\PaymentsChartWidget;
use App\Filament\Resources\ProductResource\Widgets\ProductsChartWidget;
use App\Filament\Resources\ProductResource\Widgets\ProductsStatWidget;
use App\Filament\Resources\UserResource\Widgets\UsersChartWidget;
use Filament\Pages\Dashboard;

class DashboardResource extends Dashboard
{
    public function getWidgets(): array
    {
        return [
            // ProductsStatWidget::class,
            PaymentsChartWidget::class,
            UsersChartWidget::class,
            ProductsChartWidget::class,
        ];
    }
}
