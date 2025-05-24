<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\OrderProduct;
use FFI;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Forms;

class Pos extends Component implements HasForms
{
    use InteractsWithForms;

    public $search = '';
    public $name_customer = '';
    public $payment_methods;
    public $payment_method_id;
    public $order_items = []; 
    public $total_price = 0;
    public $gender = '';
    public $payment_method_id_temp = '0';
    public $cash_received = 0;
    public $change = 0;

    protected $listeners = [
        'scanResult' => 'handleScanResult',
    ];



    public function mount(): void
    {
        $this->payment_methods = PaymentMethod::all();
        $this->order_items = session('order_items', []);
        $this->calculateTotal();

        $this->form->fill([
            'name_customer' => $this->name_customer,
            'payment_method_id' => $this->payment_method_id,
            'total_price' => $this->total_price,
        ]);
    }

    public function render()
    {
        return view('livewire.pos', [
            'products' => Product::where('stock', '>', 0)
                ->search($this->search)
                ->paginate(12),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Checkout')
                    ->schema([
                        Forms\Components\TextInput::make('name_customer')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ]),
                        Forms\Components\TextInput::make('total_price')
                            ->readOnly() //✅ Biar user tidak bisa input manual
                            ->numeric(),
                            // ->disabled(), // ✅ Biar user tidak bisa input manual dan tidak bisa diubah
                        Forms\Components\Select::make('payment_method_id')
                            ->required()
                            ->label('Payment Method')
                            ->options($this->payment_methods?->pluck('name', 'id')->toArray() ?? []),
                    ])
            ]);
        }

    public function addToOrder($productId)
    {
        $product = Product::find($productId);

        if (!$product || $product->stock <= 0) {
            Notification::make()
                ->title('Produk tidak ditemukan atau stok habis')
                ->danger()
                ->send();
            return;
        }

        $existingKey = collect($this->order_items)->search(fn ($item) => $item['product_id'] === $productId);

        if ($existingKey !== false) {
            $this->order_items[$existingKey]['quantity']++;
        } else {
            $this->order_items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image_url,
                'quantity' => 1,
            ];
        }

        session()->put('order_items', $this->order_items);
        $this->calculateTotal();

        Notification::make()
            ->title('Produk Ditambahkan ke Keranjang')
            ->success()
            ->send();
    }

    public function calculateTotal()
    {
        $this->total_price = collect($this->order_items)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $this->form->fill([
            'total_price' => $this->total_price,
        ]);
    }

    public function loadOrderItems()
    {
        $this->order_items = session('order_items', []);
        $this->calculateTotal();
    }

   public function increaseQuantity($product_id)
    {
    $product = Product::find($product_id); // Perbaiki typo di sini juga

    if (!$product) {
        Notification::make()
            ->title('Produk tidak ditemukan atau stok habis')
            ->danger()
            ->send();
        return;
    }

    foreach ($this->order_items as $key => $item) {
        if ($item['product_id'] == $product_id) {
            if ($item['quantity'] + 1 <= $product->stock) {
                $this->order_items[$key]['quantity']++;
            } else {
                Notification::make()
                    ->title('Stok barang tidak cukup')
                    ->danger()
                    ->send();
            }
            break;
        }
    }

    session()->put('order_items', $this->order_items);
    $this->calculateTotal(); // Tambahkan ini agar total diperbarui
    }

    public function decreaseQuantity($product_id)
    {
        foreach ($this->order_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                if ($this->order_items[$key]['quantity'] > 1) {
                    $this->order_items[$key]['quantity']--;
                } else {
                    unset($this->order_items[$key]);
                    $this->order_items = array_values($this->order_items);
                }
                break;
            }
        }

        session()->put('order_items', $this->order_items);
        $this->calculateTotal();
    }

    public function checkout()
    {
        $this->validate([
            'name_customer' => 'required|string|max:255',
            'gender' => 'required|in:male,female', 
            'payment_method_id' => 'required',
        ]);

        $order = Order::create([
        'name' => $this->name_customer,
        'gender' => $this->gender,
        'payment_method_id' => $this->payment_method_id,
        'total_price' => $this->total_price, 
    ]);

        foreach ($this->order_items as $item) {
            OrderProduct::create([ 
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
            ]);
        }

        $this->order_items = [];
        session()->forget('order_items');

        return redirect()->to('admin/orders');
    }

    public function handleScanResult($decodeText)
    {
        $product = Product::where('barcode', $decodeText)->first();

        if ($product) {
            $this->addToOrder($product->id);
        } else {
            Notification::make()
                ->title('Produk not found', $decodeText)
                ->danger()
                ->send();
        }
    }

    public function applyBarcode($code)
    {
        $product = Product::where('barcode', $code)->first();

        if ($product) {
            $this->addToOrder($product->id);
        } else {
            $this->dispatch('alert', ['message' => 'Produk tidak ditemukan']);
        }
    }

    

    
}