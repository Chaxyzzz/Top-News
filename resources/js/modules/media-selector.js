/**
 * TopNews Admin Media Selector Module
 */

let pickerCallback = null;
let selectedMedia = null;
let currentPickerPage = 1;

export function initMediaPicker() {
    window.openMediaPicker = function (callback) {
        pickerCallback = callback;
        selectedMedia = null;
        updateSelectionUI();
        const modal = document.getElementById('global-media-picker-modal');
        if (modal) {
            modal.classList.remove('hidden');
            loadPickerMedia(1);
        }
    };

    window.closeMediaPicker = function () {
        const modal = document.getElementById('global-media-picker-modal');
        if (modal) modal.classList.add('hidden');
        pickerCallback = null;
        selectedMedia = null;
    };

    window.switchPickerTab = function (tab) {
        const browseTab = document.getElementById('picker-tab-browse');
        const uploadTab = document.getElementById('picker-tab-upload');
        const browseContent = document.getElementById('picker-content-browse');
        const uploadContent = document.getElementById('picker-content-upload');

        if (tab === 'browse') {
            browseTab.className = 'px-3 py-1 text-xs font-semibold rounded-sm bg-neutral-900 text-white';
            uploadTab.className = 'px-3 py-1 text-xs font-semibold rounded-sm bg-neutral-100 text-neutral-700 hover:bg-neutral-200';
            browseContent.classList.remove('hidden');
            uploadContent.classList.add('hidden');
            loadPickerMedia(currentPickerPage);
        } else {
            uploadTab.className = 'px-3 py-1 text-xs font-semibold rounded-sm bg-neutral-900 text-white';
            browseTab.className = 'px-3 py-1 text-xs font-semibold rounded-sm bg-neutral-100 text-neutral-700 hover:bg-neutral-200';
            uploadContent.classList.remove('hidden');
            browseContent.classList.add('hidden');
        }
    };

    window.loadPickerMedia = async function (page = 1) {
        currentPickerPage = page;
        const grid = document.getElementById('picker-grid');
        const searchInput = document.getElementById('picker-search-input');
        const searchTerm = searchInput ? encodeURIComponent(searchInput.value) : '';

        if (!grid) return;

        grid.innerHTML = '<div class="col-span-full py-8 text-center text-xs text-neutral-400">Memuat berkas media...</div>';

        try {
            const res = await fetch(`/admin/media/modal?page=${page}&search=${searchTerm}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const json = await res.json();

            if (!json.data || json.data.length === 0) {
                grid.innerHTML = '<div class="col-span-full py-8 text-center text-xs text-neutral-400">Tidak ada media ditemukan.</div>';
                return;
            }

            grid.innerHTML = '';
            json.data.forEach(item => {
                const card = document.createElement('div');
                card.className = 'relative aspect-square bg-neutral-100 rounded-sm overflow-hidden border-2 cursor-pointer transition-all ' +
                    (selectedMedia && selectedMedia.id === item.id ? 'border-red-600 ring-2 ring-red-600/30' : 'border-neutral-200 hover:border-neutral-400');
                
                const thumb = item.variants && item.variants.thumbnail ? `/storage/${item.variants.thumbnail}` : (item.path ? `/storage/${item.path}` : '');

                card.innerHTML = `
                    <img src="${thumb}" alt="${item.original_filename}" class="w-full h-full object-cover">
                    <div class="absolute bottom-0 inset-x-0 bg-black/70 text-white p-1 text-[9px] truncate">
                        ${item.original_filename}
                    </div>
                `;

                card.addEventListener('click', () => {
                    selectedMedia = {
                        id: item.id,
                        uuid: item.uuid,
                        filename: item.original_filename,
                        url: `/storage/${item.path}`,
                        thumbnail_url: thumb,
                        alt_text: item.alt_text,
                        caption: item.caption,
                        credit: item.credit
                    };
                    // Update border
                    grid.querySelectorAll('div').forEach(el => el.classList.remove('border-red-600', 'ring-2', 'ring-red-600/30'));
                    card.classList.add('border-red-600', 'ring-2', 'ring-red-600/30');
                    updateSelectionUI();
                });

                grid.appendChild(card);
            });

            renderPagination(json);
        } catch (e) {
            console.error(e);
            grid.innerHTML = '<div class="col-span-full py-8 text-center text-xs text-red-500">Gagal memuat media.</div>';
        }
    };

    window.uploadPickerFile = async function (input) {
        if (!input.files || input.files.length === 0) return;
        const file = input.files[0];
        const statusDiv = document.getElementById('picker-upload-status');
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (statusDiv) {
            statusDiv.className = 'text-xs p-3 rounded-sm bg-neutral-100 text-neutral-700 block';
            statusDiv.innerText = `Mengunggah ${file.name}...`;
        }

        const formData = new FormData();
        formData.append('file', file);

        try {
            const res = await fetch('/admin/media', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await res.json();

            if (data.success && data.media && data.media.length > 0) {
                if (statusDiv) {
                    statusDiv.className = 'text-xs p-3 rounded-sm bg-emerald-100 text-emerald-800 block';
                    statusDiv.innerText = 'Unggah berhasil!';
                }
                selectedMedia = data.media[0];
                setTimeout(() => {
                    switchPickerTab('browse');
                    loadPickerMedia(1);
                }, 400);
            } else {
                throw new Error(data.message || 'Gagal mengunggah berkas.');
            }
        } catch (err) {
            if (statusDiv) {
                statusDiv.className = 'text-xs p-3 rounded-sm bg-red-100 text-red-800 block';
                statusDiv.innerText = err.message || 'Gagal mengunggah berkas.';
            }
        }
    };

    window.confirmMediaPickerSelection = function () {
        if (selectedMedia && typeof pickerCallback === 'function') {
            pickerCallback(selectedMedia);
            closeMediaPicker();
        }
    };

    function updateSelectionUI() {
        const info = document.getElementById('picker-selection-info');
        const btn = document.getElementById('picker-confirm-btn');
        if (!info || !btn) return;

        if (selectedMedia) {
            info.innerHTML = `Dipilih: <strong class="text-neutral-900">${selectedMedia.filename}</strong>`;
            btn.disabled = false;
        } else {
            info.innerText = 'Belum ada media yang dipilih';
            btn.disabled = true;
        }
    }

    function renderPagination(json) {
        const pagDiv = document.getElementById('picker-pagination');
        if (!pagDiv) return;

        pagDiv.innerHTML = `
            <span>Halaman ${json.current_page} dari ${json.last_page} (${json.total} total)</span>
            <div class="space-x-1">
                <button type="button" ${json.current_page <= 1 ? 'disabled' : ''} onclick="loadPickerMedia(${json.current_page - 1})" class="px-2.5 py-1 bg-white border border-neutral-300 rounded-xs disabled:opacity-40">Sebelumnya</button>
                <button type="button" ${json.current_page >= json.last_page ? 'disabled' : ''} onclick="loadPickerMedia(${json.current_page + 1})" class="px-2.5 py-1 bg-white border border-neutral-300 rounded-xs disabled:opacity-40">Berikutnya</button>
            </div>
        `;
    }
}
