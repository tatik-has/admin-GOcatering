<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaketanResource\Pages;
use App\Models\Paketan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PaketanResource extends Resource
{
    protected static ?string $model = Paketan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Data Paketan';
    protected static ?string $pluralModelLabel = 'Paketans';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->required()
                    ->maxLength(100)
                    ->label('Nama'),

                TextInput::make('alamat')
                    ->required()
                    ->maxLength(200)
                    ->label('Alamat'),

                TextInput::make('harga')
                    ->numeric()
                    ->required()
                    ->label('Harga'),

                Textarea::make('deskripsi')
                    ->required()
                    ->label('Deskripsi'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('nama')->searchable(),
                TextColumn::make('alamat')->limit(20)->searchable(),
                TextColumn::make('harga')->sortable(),
                TextColumn::make('deskripsi')->limit(30),
                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Dibuat'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaketans::route('/'),
            'create' => Pages\CreatePaketan::route('/create'),
            'edit' => Pages\EditPaketan::route('/{record}/edit'),
        ];
    }
}