<?php

namespace App\Filament\Pages;

use App\Models\Menu;
use App\Models\Pemesanan;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class DashboardAdmin extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Dashboard';

    public function render(): View
    {
        return view('filament.pages.dashboard-admin', [
            'totalMenu' => Menu::count(),
            'totalPemesanan' => Pemesanan::count(),
        ]);
    }
}
