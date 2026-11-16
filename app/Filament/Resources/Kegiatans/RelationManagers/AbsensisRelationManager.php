<?php

namespace App\Filament\Resources\Kegiatans\RelationManagers;

use App\Models\Absensi;
use App\Models\AnggotaKeluarga;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class AbsensisRelationManager extends RelationManager
{
    protected static string $relationship = 'absensis';

    protected static ?string $title = 'Absensi Warga';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('anggota_keluarga_id')
                    ->label('Anggota Keluarga')
                    ->relationship(
                        name: 'anggotaKeluarga',
                        titleAttribute: 'nama',
                        modifyQueryUsing: fn (Builder $query) => $query->with([
                            'kartuKeluarga.rumah.blok',
                        ]),
                    )
                    ->getOptionLabelFromRecordUsing(
                        function (AnggotaKeluarga $record): string {
                            $rumah = $record->kartuKeluarga?->rumah;
                            $blok = $rumah?->blok;

                            $label = $record->nama;

                            if ($rumah) {
                                $label .= " - Rumah {$rumah->nomor_rumah}";
                            }

                            if ($blok) {
                                $label .= " - {$blok->nama_blok}";
                            }

                            return $label;
                        }
                    )
                    ->searchable(['nama', 'nik'])
                    ->preload()
                    ->required()
                    ->unique(
                        table: Absensi::class,
                        column: 'anggota_keluarga_id',
                        modifyRuleUsing: fn (Unique $rule) => $rule->where(
                            'kegiatan_id',
                            $this->getOwnerRecord()->getKey()
                        ),
                    )
                    ->validationMessages([
                        'unique' => 'Anggota keluarga ini sudah tercatat dalam absensi kegiatan.',
                    ]),

                Select::make('status_hadir')
                    ->label('Status Kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'tidak_hadir' => 'Tidak Hadir',
                    ])
                    ->required()
                    ->default('hadir'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status_hadir')
            ->columns([
                TextColumn::make('anggotaKeluarga.nama')
                    ->label('Nama Anggota')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('anggotaKeluarga.kartuKeluarga.rumah.nomor_rumah')
                    ->label('Nomor Rumah'),

                TextColumn::make('status_hadir')
                    ->label('Status Kehadiran')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'hadir' => 'Hadir',
                            'izin' => 'Izin',
                            'tidak_hadir' => 'Tidak Hadir',
                            default => ucfirst(str_replace('_', ' ', $state)),
                        }
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                            'hadir' => 'success',
                            'izin' => 'warning',
                            'tidak_hadir' => 'danger',
                            default => 'gray',
                        }
                    ),
            ])
            ->defaultSort('created_at')
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Peserta'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah'),

                DeleteAction::make()
                    ->label('Hapus'),
            ]);
    }
}