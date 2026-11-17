<?php

namespace App\Http\Controllers\Admin;

use App\Exports\KeuanganExport;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportKeuanganController
{
    public function __invoke(Request $request)
    {
        // Pastikan pengguna memiliki izin mengakses panel admin.
        abort_unless(
            $request->user()?->canAccessPanel(
                Filament::getPanel('admin')
            ),
            403,
            'Anda tidak memiliki izin mengunduh laporan keuangan.'
        );

        $filename = 'Laporan-Keuangan-RT-02.xlsx';

        return Excel::download(
            new KeuanganExport,
            $filename
        );
    }
}
