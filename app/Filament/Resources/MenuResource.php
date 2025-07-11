<?php
namespace App\Filament\Resources;

use App\Models\Menu;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\MenuResource\Pages;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Menu Management';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Dasar')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('nama')
                            ->label('Nama Menu')
                            ->required()
                            ->maxLength(255),
                        
                        Select::make('kategori_utama')
                            ->label('Kategori Utama')
                            ->options([
                                'kuliner' => 'Kuliner',
                                'paket_bulanan' => 'Paket Bulanan',
                                'katering' => 'Katering',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('sub_kategori', null)),
                    ]),


                    FileUpload::make('gambar')
                        ->label('Gambar Menu')
                        ->image()
                        ->directory('menu-images')
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            '1:1',
                            '16:9',
                            '4:3',
                        ])
                        ->maxSize(2048),
                ]),

            Section::make('Detail Menu')
                ->schema([
                    Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->rows(3)
                        ->columnSpanFull(),

                    Grid::make(3)->schema([
                        TextInput::make('harga')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'tersedia' => 'Tersedia',
                                'habis' => 'Habis',
                            ])
                            ->required(),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('porsi')
                            ->label('Porsi/Jumlah')
                            ->placeholder('Contoh: 1 porsi, 10-15 orang')
                            ->visible(fn (callable $get) => in_array($get('kategori_utama'), ['kuliner', 'katering'])),

                        TextInput::make('durasi')
                            ->label('Durasi')
                            ->placeholder('Contoh: 1 bulan, per hari')
                            ->visible(fn (callable $get) => $get('kategori_utama') === 'paket_bulanan'),
                    ]),
                ]),

            Section::make('Detail Khusus Paket Bulanan')
                ->schema([
                    Repeater::make('menu_items')
                        ->label('Item Menu dalam Paket')
                        ->schema([
                            TextInput::make('nama_item')
                                ->label('Nama Item')
                                ->required(),
                            TextInput::make('hari')
                                ->label('Hari')
                                ->placeholder('Senin, Selasa, dst.'),
                        ])
                        ->columns(2)
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['nama_item'] ?? null),
                ])
                ->visible(fn (callable $get) => $get('kategori_utama') === 'paket_bulanan'),

            Section::make('Informasi Tambahan')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('rating')
                            ->label('Rating')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->step(0.1)
                            ->suffix('/5'),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->circular()
                    ->size(60),

                TextColumn::make('nama')
                    ->label('Nama Menu')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                BadgeColumn::make('kategori_utama')
                    ->label('Kategori')
                    ->colors([
                        'success' => 'kuliner',
                        'warning' => 'paket_bulanan',
                        'info' => 'katering',
                    ])
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'kuliner' => 'Kuliner',
                            'paket_bulanan' => 'Paket Bulanan',
                            'katering' => 'Katering',
                            default => $state,
                        };
                    }),

                TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'tersedia',
                        'danger' => 'habis',
                    ]),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->suffix('/5')
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kategori_utama')
                    ->label('Kategori Utama')
                    ->options([
                        'kuliner' => 'Kuliner',
                        'paket_bulanan' => 'Paket Bulanan',
                        'katering' => 'Katering',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'habis' => 'Habis',
                    ]),

                SelectFilter::make('is_featured')
                    ->label('Featured')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
