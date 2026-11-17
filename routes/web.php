<?php

use App\Http\Controllers\Admin\ExportKeuanganController;
use Illuminate\Support\Facades\Route;

// Halaman informasi backend
Route::get('/', function () {
    return view('welcome');
});

// Alias login untuk redirect middleware auth.
// Login admin sebenarnya tetap menggunakan Filament.
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Export laporan keuangan khusus admin.
Route::get('/admin/export/keuangan', ExportKeuanganController::class)
    ->middleware('auth')
    ->name('admin.keuangan.export');
