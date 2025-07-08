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
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

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
                    ->required(),

                TextInput::make('pesanan')
                    ->required(),

                TextInput::make('request'),

                TextInput::make('jumlah')
                    ->required(),

                TextInput::make('total_harga')
                    ->numeric()
                    ->required(),

                TextInput::make('tanggal_pesan')
                    ->type('date')
                    ->required(),

                TextInput::make('alamat')
                    ->required(),

                TextInput::make('telepon')
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
                    ->label('Nama Pelanggan'),

                TextColumn::make('pesanan')
                    ->label('Pesanan'),

                TextColumn::make('request')
                    ->label('Request'),

                TextColumn::make('jumlah')
                    ->label('Jumlah'),

                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR'), // Pastikan format mata uang yang benar untuk Indonesia, atau hapus jika tidak perlu.

                TextColumn::make('tanggal_pesan')
                    ->label('Tanggal Pesan')
                    ->date(), // Atau date('Y-m-d') jika ingin format spesifik

                TextColumn::make('alamat')
                    ->label('Alamat'),

                TextColumn::make('telepon')
                    ->label('Telepon'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            // BARIS INI YANG HARUS DIPERBAIKI:
            'index' => Pages\ListPemesanan::route('/'), // GANTI 'ListPemesanans' menjadi 'ListPemesanan'
            'create' => Pages\CreatePemesanan::route('/create'),
            'edit' => Pages\EditPemesanan::route('/{record}/edit'),
        ];
    }
}