<?php

namespace App\Filament\Pages;

use App\Models\Pengaturan;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class PengaturanTampilan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Pengaturan Tampilan';

    protected static ?string $title = 'Pengaturan Tampilan';

    protected string $view = 'filament.pages.pengaturan-tampilan';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Pengaturan::current()->only(['hero_media']));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Media Hero')
                    ->description('Gambar atau video yang tampil di hero beranda publik dan portal warga.')
                    ->schema([
                        // Format eksplisit: `image/*` juga menerima HEIC, yang tidak bisa dirender browser.
                        FileUpload::make('hero_media')
                            ->label('Gambar / Video')
                            ->image()
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'video/mp4',
                                'video/webm',
                            ])
                            ->maxSize(20480)
                            ->directory('hero')
                            ->disk('public')
                            ->helperText('Hanya JPG, PNG, WebP, MP4, atau WebM. Maks 20 MB. Format lain (mis. HEIC dari iPhone) ditolak.'),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Simpan')->submit('save'),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        Pengaturan::current()->update($this->form->getState());

        Notification::make()->title('Pengaturan tampilan disimpan')->success()->send();
    }
}
