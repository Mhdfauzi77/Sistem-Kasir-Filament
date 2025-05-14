<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProductFavorite extends BaseWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Menu Best Seller';

    public function table(Table $table): Table
    {
        $productQuery = Product::query()
            ->withCount('orderProducts') // relasi harus bernama orderProducts (jamak)
            ->orderByDesc('order_products_count')
            ->take(10);

        return $table
            ->query($productQuery)
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular() // buat gambar bulat (opsional)
                    ->height(50) // atur tinggi gambar
                    ->width(50)  // atur lebar gambar
                    ->extraImgAttributes(['style' => 'object-fit: cover']),// supaya proporsional
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_products_count')
                    ->label('Dipesan')
                    ->numeric()
                    ->sortable(),
            ])
             ->defaultPaginationPageOption(5);
    }
}
