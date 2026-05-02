<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">📊 لوحة المؤشرات</x-slot>

        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px;">
            @foreach ($stats as $stat)
                @php
                    $palette = [
                        'emerald' => ['#ecfdf5', '#047857'],
                        'red'     => ['#fef2f2', '#b91c1c'],
                        'blue'    => ['#eff6ff', '#1d4ed8'],
                        'gray'    => ['#f9fafb', '#374151'],
                    ];
                    [$bg, $fg] = $palette[$stat['color']] ?? $palette['gray'];
                @endphp
                <div style="background:{{ $bg }}; border-radius: 12px; padding: 14px 16px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 12px; color:#6b7280; margin-bottom: 6px;">{{ $stat['label'] }}</div>
                    <div style="font-size: 18px; font-weight: 700; color: {{ $fg }};">{{ $stat['value'] }}</div>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    @foreach ($reports as $group => $items)
        <x-filament::section collapsible>
            <x-slot name="heading">📁 {{ $group }}</x-slot>
            <x-slot name="description">{{ count($items) }} تقرير متاح</x-slot>

            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
                @foreach ($items as $item)
                    <div style="display:flex; flex-direction:column; gap:10px; padding:14px; border:1px solid #e5e7eb; border-radius:12px; background:#fff;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="background:#eff6ff; width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#1d4ed8;">
                                <x-filament::icon :icon="$item['icon']" style="width: 20px; height: 20px;" />
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:600; font-size: 14px;">{{ $item['title'] }}</div>
                                <div style="font-size: 12px; color:#6b7280;">{{ $item['desc'] }}</div>
                            </div>
                            <span style="font-size:10px; padding:2px 8px; border-radius:99px; background:{{ $item['format'] === 'Word' ? '#ecfdf5' : '#eff6ff' }}; color:{{ $item['format'] === 'Word' ? '#047857' : '#1d4ed8' }};">
                                {{ $item['format'] }}
                            </span>
                        </div>
                        <x-filament::button
                            wire:click="{{ $item['method'] }}"
                            :color="$item['format'] === 'Word' ? 'success' : 'primary'"
                            icon="heroicon-o-arrow-down-tray"
                            size="sm"
                            style="width:100%;"
                        >
                            تحميل
                        </x-filament::button>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endforeach
</x-filament-panels::page>
