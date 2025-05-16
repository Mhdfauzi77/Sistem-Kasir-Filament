<?php

namespace App\Filament\Pages;

use App\Models\Product;
use Filament\Pages\Page;
use Livewire\Attributes\On;

class PosPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.pos-page';
    protected static ?int $navigationSort = 105;

    public $products;
    public $search = '';
    public $order_items = [];
    public $total_price = 0;

    #[On('barcodeScanned')]
    public function applyBarcode($code)
    {
        $product = Product::where('barcode', $code)->first();

        if ($product) {
            $this->addToOrder($product->id);
        } else {
            $this->dispatch('alert', ['message' => 'Produk tidak ditemukan']);
        }
    }

    public function mount()
    {
        $this->loadProducts();
    }

    public function updatedSearch()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $query = Product::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $this->products = $query->get();
    }

    public function addToOrder($productId)
    {
        $product = Product::find($productId);

        if (!$product) return;

        $existing = collect($this->order_items)->firstWhere('product_id', $product->id);

        if ($existing) {
            foreach ($this->order_items as &$item) {
                if ($item['product_id'] == $product->id) {
                    $item['quantity'] += 1;
                    break;
                }
            }
        } else {
            $this->order_items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image_url,
                'quantity' => 1,
            ];
        }

        $this->updateTotal();
    }

    public function increaseQuantity($productId)
    {
        foreach ($this->order_items as &$item) {
            if ($item['product_id'] == $productId) {
                $item['quantity']++;
                break;
            }
        }

        $this->updateTotal();
    }

    public function decreaseQuantity($productId)
    {
        foreach ($this->order_items as $index => $item) {
            if ($item['product_id'] == $productId) {
                $this->order_items[$index]['quantity']--;

                if ($this->order_items[$index]['quantity'] <= 0) {
                    unset($this->order_items[$index]);
                }

                break;
            }
        }

        $this->updateTotal();
    }

    public function updateTotal()
    {
        $this->total_price = collect($this->order_items)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function checkout()
    {
        $this->reset(['order_items', 'total_price']);
        $this->dispatch('alert', ['message' => 'Transaksi berhasil!']);
    }
}
