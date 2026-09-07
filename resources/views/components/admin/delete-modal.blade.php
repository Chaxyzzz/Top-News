<!-- TopNews Reusable Permanent Delete Confirmation Modal -->
<div id="topnews-delete-modal" 
     class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="tn-delete-modal-title"
     aria-describedby="tn-delete-modal-desc"
     tabindex="-1">
    
    <!-- Modal Card Surface -->
    <div class="relative w-full max-w-md bg-white rounded-[8px] shadow-2xl border border-[#E8E8E8] overflow-hidden transform transition-transform duration-200 scale-95" 
         id="tn-delete-modal-card">
        
        <!-- Top Accent Bar -->
        <div class="h-1.5 bg-[#E50914] w-full"></div>

        <div class="p-6">
            <!-- Header Icon & Title -->
            <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-[#E50914]/10 text-[#E50914] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 id="tn-delete-modal-title" class="text-lg font-headline font-black text-[#111111] tracking-tight">
                        Hapus Permanen?
                    </h3>
                    <p id="tn-delete-modal-desc" class="text-xs text-[#666666] mt-0.5 leading-relaxed">
                        Tindakan ini bersifat permanen dan data tidak dapat dipulihkan kembali.
                    </p>
                </div>
            </div>

            <!-- Target Item Name Box -->
            <div class="bg-[#F8F9FA] border border-[#E8E8E8] rounded-[6px] p-3 mb-5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#888888] block mb-1">Item yang dipilih:</span>
                <p id="tn-delete-modal-item-name" class="text-xs font-bold text-[#111111] truncate break-all">
                    -
                </p>
            </div>

            <!-- Actions Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-[#E8E8E8]">
                <button type="button" 
                        id="tn-delete-modal-cancel" 
                        class="px-4 py-2 bg-[#F1F3F4] hover:bg-[#E8EAED] text-[#333333] font-bold text-xs rounded-[4px] transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-slate-400">
                    Batal
                </button>
                <button type="button" 
                        id="tn-delete-modal-confirm" 
                        class="px-4 py-2 bg-[#E50914] hover:bg-[#C8102E] text-white font-bold text-xs rounded-[4px] transition-colors shadow-sm cursor-pointer flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Hapus Permanen</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('topnews-delete-modal');
    const modalCard = document.getElementById('tn-delete-modal-card');
    const modalTitle = document.getElementById('tn-delete-modal-title');
    const modalItemName = document.getElementById('tn-delete-modal-item-name');
    const btnCancel = document.getElementById('tn-delete-modal-cancel');
    const btnConfirm = document.getElementById('tn-delete-modal-confirm');
    
    let activeForm = null;
    let previousActiveElement = null;

    window.openTopNewsDeleteModal = function(options) {
        activeForm = options.form;
        previousActiveElement = document.activeElement;

        modalTitle.textContent = options.title || 'Hapus Permanen?';
        modalItemName.textContent = options.itemName || 'Item terlampir';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modalCard.classList.remove('scale-95');
            modalCard.classList.add('scale-100');
            btnCancel.focus();
        }, 10);

        document.body.style.overflow = 'hidden';
    };

    function closeModal() {
        modalCard.classList.remove('scale-100');
        modalCard.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            if (previousActiveElement) {
                previousActiveElement.focus();
            }
        }, 150);
    }

    btnCancel.addEventListener('click', closeModal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    btnConfirm.addEventListener('click', function() {
        if (activeForm) {
            btnConfirm.disabled = true;
            btnConfirm.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Memproses...';
            activeForm.submit();
        }
    });

    // Intercept forms marked with data-confirm-delete
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form && form.matches('[data-confirm-delete]')) {
            e.preventDefault();
            const title = form.getAttribute('data-delete-title') || 'Hapus Permanen?';
            const itemName = form.getAttribute('data-delete-name') || 'Item ini';

            window.openTopNewsDeleteModal({
                form: form,
                title: title,
                itemName: itemName
            });
        }
    });
});
</script>
