<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Order;
use App\Models\Expense;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Mengambil jumlah data dari model
        $product_count = Product::count();
        $order_count = Order::count();
        $omset_total = Order::sum('total_price'); // pastikan kolom ini benar
        $expense_total = Expense::sum('amount');  // pastikan kolom ini benar

        return [
            Stat::make('Produk', number_format($product_count, 0, ",", ".")),
            Stat::make('Order', number_format($order_count, 0, ",", ".")),
            Stat::make('Omset', 'Rp ' . number_format($omset_total, 0, ",", ".")),
            Stat::make('Expense', 'Rp ' . number_format($expense_total, 0, ",", ".")),
        ];
    }
}
