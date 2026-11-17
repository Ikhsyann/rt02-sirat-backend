<?php

namespace App\Filament\Pages;

use App\Models\Blok;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ExportData extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static ?string $navigationLabel = 'Export Data';

    protected static ?string $title = 'Export Data';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan Sistem';

    protected static ?int $navigationSort = 97;

    protected string $view = 'filament.pages.export-data';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'jenis_data' => 'keuangan',
            'format' => 'xlsx',
            'jenis_keuangan' => 'semua',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Pilihan Ekspor')
                    ->description('Pilih jenis data dan filter yang diinginkan, lalu unduh berkasnya.')
                    ->schema([
                        Select::make('jenis_data')
                            ->label('Jenis Data')
                            ->options([
                                'keuangan' => 'Rekap Keuangan',
                                'penduduk' => 'Data Penduduk',
                            ])
                            ->default('keuangan')
                            ->live()
                            ->required(),

                        Select::make('format')
                            ->label('Format')
                            ->options([
                                'xlsx' => 'Excel (.xlsx)',
                                'csv' => 'CSV (.csv)',
                            ])
                            ->default('xlsx')
                            ->required(),

                        // --- Filter Rekap Keuangan ---
                        DatePicker::make('tanggal_dari')
                            ->label('Tanggal Dari')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->visible(fn ($get): bool => $get('jenis_data') === 'keuangan'),

                        DatePicker::make('tanggal_sampai')
                            ->label('Tanggal Sampai')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->visible(fn ($get): bool => $get('jenis_data') === 'keuangan'),

                        Select::make('jenis_keuangan')
                            ->label('Jenis Transaksi')
                            ->options([
                                'semua' => 'Semua',
                                'income' => 'Pemasukan',
                                'expense' => 'Pengeluaran',
                            ])
                            ->default('semua')
                            ->visible(fn ($get): bool => $get('jenis_data') === 'keuangan'),

                        TextInput::make('kategori')
                            ->label('Kategori')
                            ->maxLength(255)
                            ->helperText('Kosongkan untuk semua kategori.')
                            ->visible(fn ($get): bool => $get('jenis_data') === 'keuangan'),

                        // --- Filter Data Penduduk ---
                        Select::make('blok_id')
                            ->label('Blok')
                            ->options(fn (): array => Blok::query()->orderBy('nama_blok')->pluck('nama_blok', 'id')->all())
                            ->placeholder('Semua')
                            ->visible(fn ($get): bool => $get('jenis_data') === 'penduduk'),

                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options(['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan'])
                            ->placeholder('Semua')
                            ->visible(fn ($get): bool => $get('jenis_data') === 'penduduk'),

                        Select::make('status_perkawinan')
                            ->label('Status Perkawinan')
                            ->options([
                                'Belum Kawin' => 'Belum Kawin',
                                'Kawin' => 'Kawin',
                                'Cerai Hidup' => 'Cerai Hidup',
                                'Cerai Mati' => 'Cerai Mati',
                            ])
                            ->placeholder('Semua')
                            ->visible(fn ($get): bool => $get('jenis_data') === 'penduduk'),

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
                            ->placeholder('Semua')
                            ->visible(fn ($get): bool => $get('jenis_data') === 'penduduk'),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('unduh')
                ->footer([
                    Actions::make([
                        Action::make('unduh')->label('Unduh')->submit('unduh'),
                    ]),
                ]),
        ]);
    }

    public function unduh(): void
    {
        // Unduhan lewat route GET ber-auth: browser mengikuti redirect dan
        // menerima berkas sebagai unduhan.
        $this->redirect(route('admin.export.data', $this->form->getState()));
    }
}
