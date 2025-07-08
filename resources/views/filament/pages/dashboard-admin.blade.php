<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-filament::card>
            <h2 class="text-xl font-bold">Total Menu</h2>
            <p class="text-3xl">{{ $totalMenu }}</p>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-xl font-bold">Total Pesanan</h2>
            <p class="text-3xl">{{ $totalPemesanan }}</p>
        </x-filament::card>
    </div>
</x-filament::page>
