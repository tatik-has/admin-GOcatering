<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn; // Import TextColumn
use Filament\Tables\Columns\BadgeColumn; // Import BadgeColumn
use Filament\Tables\Actions\Action; // Import Action
use Filament\Tables\Filters\SelectFilter; // Import SelectFilter
use Filament\Forms\Components\Select; // Import Select for forms (used in actions)
use Carbon\Carbon; // Import Carbon for date formatting

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart'; // Atau ikon lain yang sesuai
    protected static ?string $navigationGroup = 'Manajemen Pesanan'; // Opsional: kelompokkan di navigasi
    protected static ?string $navigationLabel = 'Pesanan Masuk'; // Teks navigasi
    protected static ?string $pluralModelLabel = 'Pesanan Masuk'; // Label untuk tampilan daftar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Anda mungkin tidak ingin form ini digunakan untuk membuat/mengedit pesanan secara manual.
                // Jika ingin hanya melihat, Anda bisa membiarkannya kosong atau menambahkan field read-only.
                // Atau, Anda bisa membuat form terpisah untuk detail pesanan jika diperlukan.
                // Contoh field yang bisa dilihat/diedit:
                Forms\Components\Fieldset::make('Informasi Pelanggan')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Pelanggan')
                            ->disabled() // Biasakan read-only untuk pesanan yang masuk
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('customer_phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('delivery_address')
                            ->label('Alamat Pengiriman')
                            ->columnSpan(2)
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Repeater::make('items')
                    ->label('Daftar Pesanan')
                    ->schema([
                        Forms\Components\TextInput::make('menu_name')->label('Nama Menu')->disabled(),
                        Forms\Components\TextInput::make('quantity')->label('Jumlah')->numeric()->disabled(),
                        Forms\Components\TextInput::make('unit')->label('Satuan')->disabled(),
                        Forms\Components\TextInput::make('price')->label('Harga Satuan')->numeric()->disabled(),
                    ])
                    ->columns(4)
                    ->disabled() // Penting: Jangan biarkan admin mengedit item pesanan langsung dari sini
                    ->defaultItems(0)
                    ->addActionLabel('Tambah Item (Tidak Disarankan)'), // Jangan biarkan menambah item
                //->disableItemCreation() // Uncomment ini jika tidak mau ada tombol add
                //->disableItemDeletion() // Uncomment ini jika tidak mau ada tombol delete
                //->disableItemMovement(), // Uncomment ini jika tidak mau bisa diatur ulang posisinya

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Harga')
                            ->numeric()
                            ->prefix('Rp.')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Status Pesanan')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Diproses',
                                'completed' => 'Selesai',
                                'delivered' => 'Diantar',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->native(false), // Untuk tampilan dropdown yang lebih rapi
                    ]),
                Forms\Components\Textarea::make('request_note')
                    ->label('Catatan Request')
                    ->rows(3)
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable() // Bisa dicari
                    // Jika Anda menggunakan relasi user, bisa begini:
                    ->getStateUsing(fn(Order $record): string => $record->customer_name ?? ($record->user->name ?? 'N/A')),

                TextColumn::make('items')
                    ->label('Pesanan')
                    ->formatStateUsing(function (string $state, Order $record) {
                        $items = $record->items; // Karena sudah di-cast ke array
                        $menuNames = [];
                        foreach ($items as $item) {
                            $menuNames[] = $item['menu_name'] ?? 'Menu Tidak Dikenal';
                        }
                        return implode(' + ', $menuNames);
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        // Pencarian dalam JSON column 'items' berdasarkan 'menu_name'
                        return $query->whereJsonContains('items', ['menu_name' => $search]);
                    }),

                TextColumn::make('request_note')
                    ->label('Request')
                    ->default('-')
                    ->color('secondary'), // Untuk warna abu-abu

                TextColumn::make('items')
                    ->label('Jumlah')
                    ->formatStateUsing(function (string $state, Order $record) {
                        $items = $record->items;
                        $quantities = [];
                        foreach ($items as $item) {
                            $quantities[] = ($item['quantity'] ?? 0) . ' ' . ($item['unit'] ?? 'porsi');
                        }
                        return implode(', ', $quantities);
                    }),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR', 0) // Format mata uang IDR, tanpa desimal
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Pesanan')
                    ->date('d F Y') // Format tanggal
                    ->sortable(),

                TextColumn::make('delivery_address')
                    ->label('Alamat')
                    ->searchable(),

                TextColumn::make('customer_phone')
                    ->label('No Hp')
                    ->searchable()
                    // Jika Anda menggunakan relasi user, bisa begini:
                    ->getStateUsing(fn(Order $record): string => $record->customer_phone ?? ($record->user->phone_number ?? 'N/A')),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'success' => 'delivered', // Hijau juga untuk 'delivered'
                        'danger' => 'cancelled',
                    ])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Diproses',
                        'completed' => 'Selesai',
                        'delivered' => 'Diantar',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->label('Filter Status'),
            ])
            ->actions([
                Action::make('toggle_delivery')
                    ->label(fn(Order $record): string => $record->status === 'delivered' ? 'Sudah Diantar' : 'Belum Diantar')
                    ->button()
                    ->color(fn(Order $record): string => $record->status === 'delivered' ? 'success' : 'primary')
                    ->disabled(fn(Order $record): bool => in_array($record->status, ['delivered', 'cancelled']))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengantaran')
                    ->modalSubheading('Apakah kamu yakin ingin menandai pesanan ini sebagai sudah diantar?')
                    ->modalButton('Ya, Antar Sekarang')
                    ->action(function (Order $record): void {
                        if (in_array($record->status, ['pending', 'processing', 'completed'])) {
                            $record->status = 'delivered';
                            $record->save();

                            \Filament\Notifications\Notification::make()
                                ->title('Status pesanan berhasil diperbarui!')
                                ->success()
                                ->send();
                        }
                    }),
            ])

            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(), // Jika ingin bisa bulk delete
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'), // Anda bisa menghapus ini jika tidak ingin admin membuat order manual
            'edit' => Pages\EditOrder::route('/{record}/edit'), // Ini untuk melihat/mengedit detail pesanan
        ];
    }

    // Optional: Nonaktifkan tombol "Buat Order" di halaman daftar jika tidak ingin admin membuat order manual
    public static function canCreate(): bool
    {
        return false;
    }
}