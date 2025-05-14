<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;

class ProductImport implements ToModel, WithGroupedHeadingRow, WithMultipleSheets, SkipsEmptyRows 
{
    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Validasi: jika name kosong, jangan simpan
        if (empty($row['name'])) {
            return null;
        }

        return new Product([
            'name'        => $row['name'],
            'slug'        => Product::generateUniqueSlug($row['name']),
            'category_id' => $row['category_id'] ?? null,
            'stock'       => $row['stock'] ?? 0,
            'price'       => $row['price'] ?? 0,
            'is_active'   => $row['is_active'] ?? 1,
            'barcode'     => $row['barcode'] ?? null,
            'image'       => !empty($row['image']) ? 'products/' . $row['image'] : null,
        ]);
    }
}
