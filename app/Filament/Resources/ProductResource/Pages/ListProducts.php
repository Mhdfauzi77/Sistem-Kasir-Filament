<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProductResource;
use App\Imports\ProductImport;
use Filament\Notifications\Notification;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ImportProducts')
                ->label('Import Product')
                ->icon('heroicon-s-arrow-down-tray')
                ->color('danger')
                ->form([
                    FileUpload::make('attachment')
                        ->label('Upload Template Product'),
                ])
                ->action(function (array $data) {
                    $file = public_path('storage/' . $data['attachment']);

                    try {
                        Excel::import(new \App\Imports\ProductImport(), $file);
                        Notification::make()
                            ->title('Product Imported Successfully')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Product Failed To Import')
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('download-template')
                ->label('Download Template')
                ->url(route('download.template'))
                ->color('success'),
                
            CreateAction::make(),
        ];
    }
}  
