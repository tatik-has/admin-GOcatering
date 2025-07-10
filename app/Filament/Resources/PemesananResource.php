<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemesananResource\Pages;
use App\Models\Pemesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Carbon\Carbon;

class PemesananResource extends Resource
{
    protected static ?string $model = Pemesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Pemesanan';
    protected static ?string $navigationGroup = 'Manajemen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User')
                    ->required(),

                Select::make('menu_id')
                    ->relationship('menu', 'nama')
                    ->label('Menu')
                    ->required(),

                TextInput::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->required(),

                TextInput::make('pesanan')
                    ->label('Pesanan')
                    ->required(),

                Textarea::make('request')
                    ->label('Request'),

                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->required(),

                TextInput::make('total_harga')
                    ->label('Total Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                DatePicker::make('tanggal_pesan')
                    ->label('Tanggal Pesan')
                    ->default(Carbon::now('Asia/Jakarta')->format('Y-m-d'))
                    ->timezone('Asia/Jakarta')
                    ->required(),

                TextInput::make('alamat')
                    ->label('Alamat')
                    ->required(),

                TextInput::make('telepon')
                    ->label('Telepon')
                    ->tel()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('menu.nama')
                    ->label('Menu')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->searchable(),

                TextColumn::make('pesanan')
                    ->label('Pesanan')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('request')
                    ->label('Request')
                    ->default('-')
                    ->searchable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah'),

                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR', 'id_ID'),

                TextColumn::make('tanggal_pesan')
                    ->label('Tanggal Pesan')
                    ->date('d F Y')
                    ->timezone('Asia/Jakarta'),

                TextColumn::make('alamat')
                    ->label('Alamat'),

                TextColumn::make('telepon')
                    ->label('Telepon'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('tanggal_pesan', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemesanan::route('/'),
            'create' => Pages\CreatePemesanan::route('/create'),
            'edit' => Pages\EditPemesanan::route('/{record}/edit'),
        ];
    }
}