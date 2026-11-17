<?php

namespace App\Filament\Resources\Keuangans\Tables;

use App\Models\Keuangan;
use App\Services\KeuanganService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KeuangansTable
{
    public static function configure(Table $table): Table
    {
        // Formatter Rupiah tanpa konversi floating point.
        $rupiah = static function (mixed $value): string {
            $amount = (string) ($value ?? '0.00');

            $negative = str_starts_with($amount, '-');
            $amount = ltrim($amount, '-');

            [$whole, $fraction] = array_pad(
                explode('.', $amount, 2),
                2,
                '00'
            );

            $whole = number_format((int) $whole, 0, ',', '.');
            $fraction = str_pad(substr($fraction, 0, 2), 2, '0');

            return ($negative ? '-Rp ' : 'Rp ')
                .$whole.','.$fraction;
        };

        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'income' => 'Pemasukan',
                            'expense' => 'Pengeluaran',
                            default => $state,
                        }
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                            'income' => 'success',
                            'expense' => 'danger',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_barang')
                    ->label('Nama Barang / Keperluan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->formatStateUsing(
                        fn (mixed $state): string => $rupiah($state)
                    )
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(
                        fn (mixed $state): string => $rupiah($state)
                    )
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('saldo')
                    ->label('Saldo Berjalan')
                    ->formatStateUsing(
                        fn (mixed $state): string => $rupiah($state)
                    )
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('created_at')
                    ->label('Tanggal Dicatat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis')
                    ->label('Jenis Transaksi')
                    ->options([
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                    ]),

                Filter::make('rentang_tanggal')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal'),

                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (
                        Builder $query,
                        array $data
                    ): Builder {
                        return $query
                            ->when(
                                $data['dari'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date)
                            )
                            ->when(
                                $data['sampai'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit'),

                Action::make('delete')
                    ->label('Hapus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Transaksi Keuangan?')
                    ->modalDescription(
                        'Transaksi akan dihapus dan seluruh saldo setelahnya akan dihitung ulang.'
                    )
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->action(function (Keuangan $record): void {
                        app(KeuanganService::class)->delete($record);

                        Notification::make()
                            ->success()
                            ->title('Transaksi berhasil dihapus')
                            ->body(
                                'Saldo transaksi berikutnya telah dihitung ulang.'
                            )
                            ->send();
                    }),
            ])
            ->defaultSort('tanggal', 'desc');
    }
}
