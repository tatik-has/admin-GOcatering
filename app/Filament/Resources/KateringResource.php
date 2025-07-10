<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KateringResource\Pages;
use App\Models\Katering;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class KateringResource extends Resource
{
    protected static ?string $model = Katering::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Katering';
    protected static ?string $navigationGroup = 'Manajemen Menu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->required()
                    ->label('Nama Katering'),

                Textarea::make('alamat')
                    ->required()
                    ->label('Alamat'),

                FileUpload::make('foto')
                    ->image()
                    ->directory('katering')
                    ->label('Foto')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->label('Nama')->searchable()->sortable(),
                TextColumn::make('alamat')->label('Alamat')->limit(30),
                ImageColumn::make('foto')->label('Foto')->disk('public')->height(60),
            ])
            ->filters([
                // jika ada filter bisa ditambahkan
            ])
            ->actions([
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
            'index' => Pages\ListKaterings::route('/'),
            'create' => Pages\CreateKatering::route('/create'),
            'edit' => Pages\EditKatering::route('/{record}/edit'),
        ];
    }
}
