@extends('admin.analytics.layout')

@section('analytics_content')
<div class="space-y-6">
    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Views -->
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pembaca Artikel</span>
                <span class="p-2 bg-red-50 text-red-600 rounded-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold font-serif text-gray-950">{{ number_format($metrics['total_views']) }}</div>
                <div class="text-xs text-gray-500 mt-1">Tayangan halaman artikel terverifikasi</div>
            </div>
        </div>

        <!-- Unique Readers (Approx) -->
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Perkiraan Pengunjung Unik</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold font-serif text-gray-950">{{ number_format($metrics['total_unique']) }}</div>
                <div class="text-xs text-gray-500 mt-1">Sesi unik harian pembaca</div>
            </div>
        </div>

        <!-- Published Articles -->
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Artikel Diterbitkan</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold font-serif text-gray-950">{{ number_format($metrics['published_articles']) }}</div>
                <div class="text-xs text-gray-500 mt-1">Produktivitas redaksi pada periode ini</div>
            </div>
        </div>

        <!-- Average Views per Article -->
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata Tayang / Berita</span>
                <span class="p-2 bg-purple-50 text-purple-600 rounded-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold font-serif text-gray-950">{{ number_format($metrics['avg_views_per_article']) }}</div>
                <div class="text-xs text-gray-500 mt-1">Efisiensi keterbacaan per publikasi</div>
            </div>
        </div>
    </div>

    <!-- Chart: Traffic Trend Over Selected Period -->
    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-2xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Tren Pertumbuhan Pembaca Harian</h3>
                <p class="text-xs text-gray-500 mt-0.5">Volume tayangan artikel per hari dalam rentang waktu yang dipilih</p>
            </div>
            <span class="text-xs font-medium text-gray-500">Satuan: Views</span>
        </div>

        <div class="relative h-64 w-full">
            <canvas id="trafficTrendChart"></canvas>
        </div>

        <!-- Accessible textual table fallback -->
        <details class="mt-4 text-xs text-gray-500">
            <summary class="cursor-pointer font-medium hover:text-gray-700">Tampilkan Rincian Tabel Angka</summary>
            <div class="mt-2 overflow-x-auto">
                <table class="w-full text-left border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            @foreach($metrics['trend_labels'] as $label)
                                <th class="border border-gray-200 px-2 py-1 text-center font-medium">{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach($metrics['trend_data'] as $val)
                                <td class="border border-gray-200 px-2 py-1 text-center">{{ number_format($val) }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </details>
    </div>

    <!-- Two Columns: Top Articles & Category Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top 10 Articles Table (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Artikel Terpopuler Periode Ini</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Artikel dengan jumlah pembaca terbanyak</p>
                </div>
                <a href="{{ route('admin.analytics.content', request()->query()) }}" class="text-xs text-red-600 font-medium hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($metrics['top_articles'] as $index => $art)
                    <div class="p-4 flex items-start gap-3 hover:bg-gray-50 transition-colors">
                        <span class="w-6 text-center text-sm font-bold {{ $index < 3 ? 'text-red-600' : 'text-gray-400' }}">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('news.show', $art->slug) }}" target="_blank" class="text-sm font-semibold text-gray-900 hover:text-red-600 line-clamp-1">
                                {{ $art->title }}
                            </a>
                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                @if($art->category)
                                    <span class="text-[11px] font-medium px-1.5 py-0.5 rounded bg-gray-100 text-gray-700">
                                        {{ $art->category->name }}
                                    </span>
                                @endif
                                <span>Oleh {{ $art->author?->name ?? 'Redaksi' }}</span>
                                <span>&bull;</span>
                                <span>{{ $art->published_at?->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-950">{{ number_format($art->period_views ?? $art->views_count) }}</div>
                            <div class="text-[11px] text-gray-400">tayangan</div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-gray-500">
                        Belum ada aktivitas pembaca artikel tercatat pada rentang waktu ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Category Distribution (1 col) -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-900">Distribusi Kategori</h3>
                <p class="text-xs text-gray-500 mt-0.5">Pangsa pembaca berdasarkan kanal berita</p>
            </div>

            <div class="p-5 space-y-4">
                @forelse($metrics['category_breakdown'] as $cat)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-gray-800">{{ $cat['name'] }}</span>
                            <span class="text-gray-500">{{ number_format($cat['views']) }} ({{ $cat['percentage'] }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-300"
                                 style="width: {{ $cat['percentage'] }}%; background-color: {{ $cat['accent_color'] ?: '#E50914' }};">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-gray-500">
                        Belum ada data kategori.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script Loaded Exclusively on Admin Analytics Page -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('trafficTrendChart');
        if (!ctx) return;

        const labels = @json($metrics['trend_labels']);
        const data = @json($metrics['trend_data']);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tayangan Pembaca',
                    data: data,
                    borderColor: '#E50914',
                    backgroundColor: 'rgba(229, 9, 20, 0.06)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#E50914',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 6,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y.toLocaleString('id-ID') + ' tayangan';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#6B7280' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F3F4F6' },
                        ticks: {
                            font: { size: 11 },
                            color: '#6B7280',
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
