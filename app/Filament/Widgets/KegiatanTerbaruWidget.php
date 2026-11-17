<?php

namespace App\Filament\Widgets;

use App\Models\Kegiatan;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class KegiatanTerbaruWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Kegiatan Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Kegiatan::query()
                    ->withCount('absensis')
                    ->orderByDesc('tanggal')
                    ->orderByDesc('id')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal Kegiatan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('absensis_count')
                    ->label('Jumlah Peserta')
                    ->counts('absensis')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('foto_dokumentasi')
                    ->label('Dokumentasi')
                    ->state(
                        fn (Kegiatan $record): string => filled($record->foto_dokumentasi)
                                ? 'Tersedia'
                                : 'Belum tersedia'
                    )
                    ->badge()
                    ->color(
                        fn (string $state): string => $state === 'Tersedia' ? 'success' : 'gray'
                    ),
            ])
            ->paginated(false)
            ->emptyStateHeading('Belum Ada Kegiatan')
            ->emptyStateDescription(
                'Kegiatan RT yang ditambahkan akan muncul di sini.'
            );
    }
}
