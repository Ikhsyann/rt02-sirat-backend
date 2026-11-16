<?php

namespace App\Filament\Resources\AnggotaKeluargas\Schemas;

use App\Models\AnggotaKeluarga;
use App\Models\KartuKeluarga;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AnggotaKeluargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Anggota Keluarga')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('nik')
                            ->label('NIK')
                            ->placeholder('Masukkan 16 digit NIK')
                            ->helperText('NIK harus terdiri dari 16 digit angka.')
                            ->required()
                            ->minLength(16)
                            ->maxLength(16)
                            ->regex('/^[0-9]{16}$/')
                            ->unique(ignoreRecord: true),

                        TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir')
                            ->maxLength(255),

                        DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->maxDate(today('Asia/Makassar')),

                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'Laki-laki' => 'Laki-laki',
                                'Perempuan' => 'Perempuan',
                            ])
                            ->native(false),

                    ])
                    ->columns(2),

                Section::make('Data Keluarga')
                    ->schema([
                        Select::make('kartu_keluarga_id')
                            ->label('Kartu Keluarga')
                            ->relationship(
                                name: 'kartuKeluarga',
                                titleAttribute: 'nomor_kk',
                                modifyQueryUsing: fn (Builder $query) =>
                                    $query->with('rumah.blok'),
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (KartuKeluarga $record): string =>
                                    'KK ••••' . substr($record->nomor_kk, -4)
                                    . ' - Rumah '
                                    . $record->rumah->nomor_rumah
                                    . ' / Blok '
                                    . $record->rumah->blok->nama_blok
                            )
                            ->searchable(['nomor_kk'])
                            ->required(),

                        Select::make('status_hubungan_keluarga')
                            ->label('Hubungan Keluarga')
                            ->options([
                                'Kepala Keluarga' => 'Kepala Keluarga',
                                'Suami' => 'Suami',
                                'Istri' => 'Istri',
                                'Anak' => 'Anak',
                                'Menantu' => 'Menantu',
                                'Cucu' => 'Cucu',
                                'Orang Tua' => 'Orang Tua',
                                'Mertua' => 'Mertua',
                                'Famili Lain' => 'Famili Lain',
                            ])
                            ->native(false)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Informasi Tambahan')
                    ->schema([
                        TextInput::make('pekerjaan')
                            ->label('Pekerjaan')
                            ->maxLength(255),

                        Select::make('golongan_darah')
                            ->label('Golongan Darah')
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'AB' => 'AB',
                                'O' => 'O',
                                'Tidak diketahui' => 'Tidak diketahui',
                            ])
                            ->native(false),

                        Select::make('status_perkawinan')
                            ->label('Status Perkawinan')
                            ->options([
                                'Belum Kawin' => 'Belum Kawin',
                                'Kawin' => 'Kawin',
                                'Cerai Hidup' => 'Cerai Hidup',
                                'Cerai Mati' => 'Cerai Mati',
                            ])
                            ->native(false),

                        DatePicker::make('tanggal_perkawinan_perceraian')
                            ->label('Tanggal Perkawinan / Perceraian')
                            ->maxDate(today('Asia/Makassar')),
                    ])
                    ->columns(2),

                Section::make('Akun Warga')
                    ->description(
                        'Hubungkan anggota keluarga dengan akun untuk akses portal warga. Bagian ini boleh dikosongkan.'
                    )
                    ->schema([
                        Select::make('user_id')
                            ->label('Akun Warga')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (
                                    Builder $query,
                                    ?AnggotaKeluarga $record
                                ): Builder {
                                    return $query
                                        ->where('role', 'warga')
                                        ->where(function (Builder $query) use ($record) {
                                            $query->whereDoesntHave('anggotaKeluarga');
                                            if ($record?->user_id !== null) {
                                                $query->orWhere('users.id', $record->user_id);
                                            }
                                        });
                                },
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (User $record): string =>
                                    "{$record->name} ({$record->email})"
                            )
                            ->searchable(['name', 'email'])
                            ->nullable()
                            ->helperText(
                                'Hanya akun warga yang belum terhubung dengan anggota keluarga lain yang tersedia.'
                            ),
                    ]),
            ]);
    }
}