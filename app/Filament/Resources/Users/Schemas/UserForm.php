<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Pengelola')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Select::make('role')
                    ->label('Hak Akses')
                    ->options([
                        'admin' => 'Admin',
                        'superadmin' => 'Superadmin',
                    ])
                    ->default('admin')
                    ->required(),

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
