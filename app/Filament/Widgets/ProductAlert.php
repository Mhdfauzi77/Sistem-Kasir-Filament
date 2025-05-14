<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Product;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProductAlert extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Stock Hampir Habis';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('stock', '<=', 50)
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular() // buat gambar bulat (opsional)
                    ->height(50) // atur tinggi gambar
                    ->width(50)  // atur lebar gambar
                    ->extraImgAttributes(['style' => 'object-fit: cover']),// supaya proporsional,
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->searchable()
                    ->color(static function ($state): string {
                        if ($state < 5) {
                            return 'danger';   // Merah
                        } elseif ($state < 50) {
                            return 'warning';  // Kuning
                        } else {
                            return 'success';  // Hijau
                        }
                    })
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(5);
    }
}
