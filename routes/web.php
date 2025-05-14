<?php

use App\Exports\TemplateExport;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel; // ✅ Tambahkan ini

Route::get('/', function () {
    return view('welcome');
});

Route::get('/download-template', function () {
    return Excel::download(new TemplateExport, 'template.xlsx');
})->name('download.template');
