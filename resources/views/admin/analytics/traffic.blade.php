@extends('admin.analytics.layout')

@section('analytics_content')
<div class="space-y-8">
    <!-- Channel Breakdown & Devices (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Traffic Channels -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-900">Saluran Trafik Masuk</h3>
                <p class="text-xs text-gray-500 mt-0.5">Klasifikasi rujukan pengunjung yang membaca artikel TopNews</p>
            </div>

            <div class="p-6 space-y-5">
                @forelse($traffic['channels'] as $ch)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-semibold text-gray-800">{{ $ch['label'] }}</span>
                            <span class="text-gray-500 font-medium">
                                {{ number_format($ch['views']) }} tayangan ({{ $ch['percentage'] }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 rounded-full bg-red-600 transition-all duration-300"
                                 style="width: {{ $ch['percentage'] }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-500">
                        Belum ada data kanal trafik pada periode ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Devices Distribution -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-900">Distribusi Perangkat Pembaca</h3>
                <p class="text-xs text-gray-500 mt-0.5">Proporsi pembaca melalui Komputer, Ponsel Pintar, atau Tablet</p>
            </div>

            <div class="p-6 space-y-5">
                @forelse($devices as $dev)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <div class="flex items-center gap-2">
                                @if($dev['type'] === 'mobile')
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @elseif($dev['type'] === 'tablet')
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                @endif
                                <span class="font-semibold text-gray-800">{{ $dev['label'] }}</span>
                            </div>
                            <span class="text-gray-500 font-medium">
                                {{ number_format($dev['views']) }} tayangan ({{ $dev['percentage'] }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 rounded-full {{ $dev['type'] === 'mobile' ? 'bg-blue-600' : ($dev['type'] === 'tablet' ? 'bg-purple-600' : 'bg-gray-800') }} transition-all duration-300"
                                 style="width: {{ $dev['percentage'] }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-500">
                        Belum ada data perangkat pada periode ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Referral Domains (External) -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-bold text-gray-900">Domain Rujukan Eksternal Teratas</h3>
            <p class="text-xs text-gray-500 mt-0.5">Situs pihak ketiga, agregator, atau media sosial pengirim pembaca terbesar (domain saja, tanpa query params)</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Nama Domain / Sumber Host</th>
                        <th class="px-4 py-3">Kategori Rujukan</th>
                        <th class="px-4 py-3 text-right">Total Tayangan</th>
                        <th class="px-4 py-3 text-right">Pembaca Unik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($traffic['top_referrers'] as $ref)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-6 py-3.5 font-semibold text-gray-900 font-mono text-xs">
                                {{ $ref->source_domain }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($ref->source_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right font-bold text-gray-950">
                                {{ number_format($ref->views) }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right text-xs text-gray-600">
                                {{ number_format($ref->unique_views) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                Belum ada domain rujukan eksternal yang tercatat. Sebagian besar pengunjung masuk secara langsung (Direct).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
