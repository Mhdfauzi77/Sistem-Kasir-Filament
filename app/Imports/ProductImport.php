<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;

class ProductImport implements 
    ToModel, 
    WithGroupedHeadingRow, 
    WithMultipleSheets, 
    SkipsEmptyRows, 
    WithValidation
{
    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    /**
     * Map the row from the Excel file to the Product model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Validasi dasar bisa dilakukan di rules(), ini hanya pemetaan
        return new Product([
            'name' => $row['name'],
            'slug' => Product::generateUniqueSlug($row['name']),
            'category_id' => $row['category_id'],
            'stock' => $row['stock'],
            'price' => $row['price'],
            'is_active' => $row['is_active'] ?? 1,
            'barcode' => $row['barcode'] ?? null,
            'image' => $row['image'] ?? null,
        ]);
    }

    /**
     * Validation rules for each row.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string', 'max:255'],
            '*.category_id' => ['required', 'integer', 'exists:categories,id'],
            '*.stock' => ['required', 'integer', 'min:0'],
            '*.price' => ['required', 'numeric', 'min:0'],
            '*.is_active' => ['nullable', 'in:0,1'],
            '*.barcode' => ['nullable', 'string'],
            '*.image' => ['nullable', 'string'],
        ];
    }
}
