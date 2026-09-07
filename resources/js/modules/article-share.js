/**
 * TopNews — Article Share Module
 *
 * Handles clipboard copy link with toast notification and print triggers.
 */
export function initArticleShare() {
    // Copy Link Buttons
    document.querySelectorAll('[data-action="copy-article-link"]').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const url = button.getAttribute('data-url') || window.location.href.split('?')[0];

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(url);
                } else {
                    // Fallback for older browsers / unsecure context
                    const tempInput = document.createElement('input');
                    tempInput.value = url;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                }

                showToast('Tautan artikel berhasil disalin.');
            } catch (err) {
                console.error('Gagal menyalin tautan:', err);
                showToast('Gagal menyalin tautan secara otomatis.');
            }
        });
    });

    // Native Web Share API (Mobile enhancement)
    document.querySelectorAll('[data-action="native-web-share"]').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const title = button.getAttribute('data-title') || document.title;
            const url = button.getAttribute('data-url') || window.location.href.split('?')[0];

            if (navigator.share) {
                try {
                    await navigator.share({
                        title: title,
                        url: url
                    });
                } catch (err) {
                    // User aborted or unallowed; ignore silently
                }
            }
        });
    });

    // Print Buttons
    document.querySelectorAll('[data-action="print-article"]').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            window.print();
        });
    });
}

function showToast(message) {
    const existingToast = document.getElementById('topnews-share-toast');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.id = 'topnews-share-toast';
    toast.className = 'fixed bottom-6 right-6 z-50 bg-[#111111] text-white text-xs font-semibold px-4 py-3 rounded-[6px] shadow-elevated flex items-center gap-2 border border-[#333333] transition-all transform translate-y-0 opacity-100';
    toast.innerHTML = `
        <svg class="w-4 h-4 text-[#0F9D58]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>${message}</span>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
