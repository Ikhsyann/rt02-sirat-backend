<?php

namespace App\Filament\Resources\Wargas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WargaForm
{
    public static function configure(Schema $schema): Schema
    {
        // Role tidak ada di form: penentuannya dipaksa 'warga' di server
        // (lihat CreateWarga) sehingga tidak bisa dibajak lewat input.
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Warga')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText(
                        fn (string $operation): string => $operation === 'create'
                            ? 'Masukkan kata sandi minimal 8 karakter.'
                            : 'Kosongkan jika tidak ingin mengubah kata sandi.'
                    ),
            ]);
    }
}
