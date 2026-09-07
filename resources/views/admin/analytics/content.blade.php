@extends('admin.analytics.layout')

@section('analytics_content')
<div class="space-y-8">
    <!-- Content Performance Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-gray-900">Performa Artikel Individual</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar artikel terpublikasi diurutkan berdasarkan interaksi pembaca</p>
            </div>

            <!-- Sort Controls -->
            <div class="flex items-center gap-2 text-xs">
                <span class="text-gray-500">Urutkan:</span>
                @php
                    $routeQuery = request()->except('sort', 'page');
                @endphp
                <a href="{{ route('admin.analytics.content', array_merge($routeQuery, ['sort' => 'views'])) }}"
                   class="px-2.5 py-1 rounded border {{ $sort === 'views' ? 'bg-red-50 text-red-700 border-red-200 font-semibold' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    Tayangan
                </a>
                <a href="{{ route('admin.analytics.content', array_merge($routeQuery, ['sort' => 'unique'])) }}"
                   class="px-2.5 py-1 rounded border {{ $sort === 'unique' ? 'bg-red-50 text-red-700 border-red-200 font-semibold' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    Pembaca Unik
                </a>
                <a href="{{ route('admin.analytics.content', array_merge($routeQuery, ['sort' => 'comments'])) }}"
                   class="px-2.5 py-1 rounded border {{ $sort === 'comments' ? 'bg-red-50 text-red-700 border-red-200 font-semibold' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    Komentar
                </a>
                <a href="{{ route('admin.analytics.content', array_merge($routeQuery, ['sort' => 'published'])) }}"
                   class="px-2.5 py-1 rounded border {{ $sort === 'published' ? 'bg-red-50 text-red-700 border-red-200 font-semibold' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    Terbaru
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Artikel</th>
                        <th class="px-4 py-3">Kanal</th>
                        <th class="px-4 py-3">Penulis</th>
                        <th class="px-4 py-3 text-right">Tayangan Periode</th>
                        <th class="px-4 py-3 text-right">Pembaca Unik</th>
                        <th class="px-4 py-3 text-center">Komentar</th>
                        <th class="px-4 py-3 text-right">Tanggal Tayang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($articles as $art)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-6 py-3.5 font-medium text-gray-900 max-w-sm">
                                <a href="{{ route('news.show', $art->slug) }}" target="_blank" class="hover:text-red-600 line-clamp-2">
                                    {{ $art->title }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($art->category)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $art->category->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-700">
                                {{ $art->author?->name ?? 'Redaksi' }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right font-bold text-gray-950">
                                {{ number_format($art->period_views ?? 0) }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right text-xs text-gray-600">
                                {{ number_format($art->period_unique ?? 0) }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-center text-xs">
                                <span class="px-2 py-0.5 rounded-full {{ $art->comments_count > 0 ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $art->comments_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right text-xs text-gray-500">
                                {{ $art->published_at?->translatedFormat('d M Y, H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                Tidak ada artikel yang memenuhi filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

    <!-- Author Performance Ranking (Shown to Editors & Admins) -->
    @if($authorRankings->isNotEmpty())
        <div class="bg-white rounded-lg border border-gray-200 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-900">Performa Dewan Redaksi & Jurnalis</h3>
                <p class="text-xs text-gray-500 mt-0.5">Produktivitas dan rata-rata tayangan per jurnalis pada periode ini</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">Jurnalis / Redaktur</th>
                            <th class="px-4 py-3 text-center">Artikel Terbit</th>
                            <th class="px-4 py-3 text-right">Total Tayangan</th>
                            <th class="px-4 py-3 text-right">Rata-rata / Artikel</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($authorRankings as $author)
                            <tr class="hover:bg-gray-50/75 transition-colors">
                                <td class="px-6 py-3.5 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-700 overflow-hidden">
                                        @if($author['avatar'])
                                            <img src="{{ $author['avatar'] }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($author['name'], 0, 2)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $author['name'] }}</div>
                                        <div class="text-xs text-gray-400">{{ '@' . $author['username'] }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center font-medium text-gray-800">
                                    {{ number_format($author['articles_count']) }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold text-gray-950">
                                    {{ number_format($author['views']) }}
                                </td>
                                <td class="px-4 py-3.5 text-right text-xs font-medium text-gray-600">
                                    {{ number_format($author['avg_views']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
