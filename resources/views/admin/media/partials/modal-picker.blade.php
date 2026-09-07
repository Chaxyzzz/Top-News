<div id="global-media-picker-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-neutral-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full flex flex-col max-h-[90vh] border border-neutral-200">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h3 class="text-base font-bold text-neutral-900">Pilih Aset Media</h3>
                <div class="flex items-center gap-2">
                    <button type="button" id="picker-tab-browse" onclick="switchPickerTab('browse')" class="px-3 py-1 text-xs font-semibold rounded-sm bg-neutral-900 text-white">
                        Jelajahi Pustaka
                    </button>
                    <button type="button" id="picker-tab-upload" onclick="switchPickerTab('upload')" class="px-3 py-1 text-xs font-semibold rounded-sm bg-neutral-100 text-neutral-700 hover:bg-neutral-200">
                        Unggah Baru
                    </button>
                </div>
            </div>
            <button type="button" onclick="closeMediaPicker()" class="text-neutral-400 hover:text-neutral-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Modal Body: Browse Tab --}}
        <div id="picker-content-browse" class="p-6 flex-1 overflow-y-auto space-y-4">
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    id="picker-search-input"
                    placeholder="Cari gambar berdasarkan nama file, alt text, caption..."
                    class="flex-1 text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
                >
                <button type="button" onclick="loadPickerMedia(1)" class="px-4 py-2 bg-neutral-900 text-white text-xs font-semibold rounded-sm hover:bg-neutral-800">
                    Cari
                </button>
            </div>

            <div id="picker-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 min-h-[200px]">
                {{-- Media items populated via JavaScript --}}
            </div>

            <div id="picker-pagination" class="flex items-center justify-between pt-2 border-t border-neutral-100 text-xs text-neutral-500">
                {{-- Pagination controls --}}
            </div>
        </div>

        {{-- Modal Body: Upload Tab --}}
        <div id="picker-content-upload" class="hidden p-6 flex-1 overflow-y-auto space-y-4">
            <div class="border-2 border-dashed border-neutral-300 rounded-lg p-8 text-center hover:border-red-500 transition-colors">
                <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"/></svg>
                <div class="mt-4 text-xs text-neutral-600">
                    <label for="picker-file-upload" class="cursor-pointer font-bold text-red-600 hover:text-red-700">
                        Klik untuk memilih berkas gambar
                    </label>
                    <input id="picker-file-upload" type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="sr-only" onchange="uploadPickerFile(this)">
                    <p class="mt-1 text-neutral-500">JPG, PNG, WEBP hingga 10MB.</p>
                </div>
            </div>
            <div id="picker-upload-status" class="hidden text-xs p-3 rounded-sm"></div>
        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-3 border-t border-neutral-200 bg-neutral-50 flex items-center justify-between">
            <div id="picker-selection-info" class="text-xs text-neutral-600 font-medium">
                Belum ada media yang dipilih
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="closeMediaPicker()" class="px-4 py-2 bg-white hover:bg-neutral-100 text-neutral-700 text-xs font-semibold rounded-sm border border-neutral-300">
                    Batal
                </button>
                <button type="button" id="picker-confirm-btn" onclick="confirmMediaPickerSelection()" disabled class="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white text-xs font-semibold rounded-sm shadow-xs">
                    Gunakan Media
                </button>
            </div>
        </div>
    </div>
</div>
