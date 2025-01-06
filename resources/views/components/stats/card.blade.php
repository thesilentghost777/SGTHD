@props(['title', 'value', 'trend' => null, 'trendLabel' => null, 'valueColor' => 'text-blue-600'])

<div class="bg-white rounded-lg shadow-lg p-6 stat-card">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold text-gray-800">{{ $title }}</h3>
        <span class="text-2xl {{ $valueColor }}">{{ $value }}</span>
    </div>
    @if($trend !== null)
    <div class="text-sm">
        @if($trend > 0)
            <p class="text-green-600">↑ +{{ number_format($trend, 1) }}% {{ $trendLabel }}</p>
        @else
            <p class="text-red-600">↓ {{ number_format($trend, 1) }}% {{ $trendLabel }}</p>
        @endif
    </div>
    @endif
</div>