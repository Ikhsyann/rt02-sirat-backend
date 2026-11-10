<?php

namespace App\Filament\Resources\AnggotaKeluargas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnggotaKeluargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kartu_keluarga_id')
                    ->label('Kartu Keluarga')
                    ->relationship('kartuKeluarga', 'nomor_kk')
                    ->required(),
                TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->maxLength(100),
                TextInput::make('nik')
                    ->label('NIK')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->length(16),
                TextInput::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->maxLength(50),
                DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->maxDate(now()),
                Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan']),
                TextInput::make('pekerjaan')
                    ->label('Pekerjaan')
                    ->maxLength(50),
                Select::make('golongan_darah')
                    ->label('Golongan Darah')
                    ->options(['-' => 'Tidak tahu', 'A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O']),
                Select::make('status_perkawinan')
                    ->label('Status Perkawinan')
                    ->options(['Belum Kawin' => 'Belum Kawin', 'Kawin' => 'Kawin', 'Cerai Hidup' => 'Cerai Hidup', 'Cerai Mati' => 'Cerai Mati']),
                DatePicker::make('tanggal_perkawinan_perceraian')
                    ->label('Tanggal Perkawinan/Perceraian'),
                Select::make('status_hubungan_keluarga')
                    ->label('Status Hubungan Keluarga')
                    ->options(['Kepala Keluarga' => 'Kepala Keluarga', 'Pasangan' => 'Pasangan', 'Anak' => 'Anak', 'Cucu' => 'Cucu', 'Orang Tua' => 'Orang Tua', 'Famili Lain' => 'Famili Lain']),
                Select::make('user_id')
                    ->label('Akun Warga')
                    ->placeholder('— tanpa akun —')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'email',
                        modifyQueryUsing: fn ($query) => $query->where('role', 'warga')->whereDoesntHave('anggotaKeluarga'),
                        ignoreRecord: true,
                    ),
            ]);
    }
}
