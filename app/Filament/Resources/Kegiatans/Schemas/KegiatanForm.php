<?php

namespace App\Filament\Resources\Kegiatans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class KegiatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->placeholder('Contoh: Kerja Bakti Lingkungan')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(
                        function (
                            Get $get,
                            Set $set,
                            ?string $old,
                            ?string $state
                        ): void {
                            if (($get('slug') ?? '') !== Str::slug($old ?? '')) {
                                return;
                            }

                            $set('slug', Str::slug($state ?? ''));
                        }
                    ),

                TextInput::make('slug')
                    ->label('Slug URL')
                    ->placeholder('kerja-bakti-lingkungan')
                    ->required()
                    ->alphaDash()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Dibuat otomatis dari nama kegiatan. Bisa disesuaikan jika diperlukan.'),

                DatePicker::make('tanggal')
                    ->label('Tanggal Kegiatan')
                    ->required()
                    ->default(today('Asia/Makassar'))
                    ->native(false)
                    ->displayFormat('d/m/Y'),

                Textarea::make('deskripsi')
                    ->label('Deskripsi Kegiatan')
                    ->placeholder('Tuliskan informasi kegiatan secara lengkap...')
                    ->rows(5)
                    ->maxLength(5000)
                    ->columnSpanFull(),

                FileUpload::make('foto_dokumentasi')
                    ->label('Foto Dokumentasi')
                    ->image()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->maxSize(5120)
                    ->disk('public')
                    ->directory('kegiatan')
                    ->visibility('public')
                    ->preventFilePathTampering()
                    ->imageEditor()
                    ->helperText('Format JPG, PNG, atau WebP. Maksimal 5 MB.'),
            ]);
    }
}