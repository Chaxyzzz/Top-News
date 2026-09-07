import { initReadingProgress } from './modules/reading-progress.js';
import { initArticleShare } from './modules/article-share.js';
import { initMediaPicker } from './modules/media-selector.js';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Modules
    initReadingProgress();
    initArticleShare();
    initMediaPicker();

    // 1. Mobile Menu Drawer Toggle
    const mobileMenuToggle = document.getElementById('tn-mobile-toggle');
    const mobileDrawer = document.getElementById('tn-mobile-drawer');
    const mobileBackdrop = document.getElementById('tn-mobile-backdrop');
    const mobileClose = document.getElementById('tn-mobile-close');

    function openMobileMenu() {
        if (mobileDrawer && mobileBackdrop) {
            mobileDrawer.classList.remove('-translate-x-full');
            mobileBackdrop.classList.remove('hidden', 'opacity-0');
            mobileBackdrop.classList.add('opacity-100');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMobileMenu() {
        if (mobileDrawer && mobileBackdrop) {
            mobileDrawer.classList.add('-translate-x-full');
            mobileBackdrop.classList.remove('opacity-100');
            mobileBackdrop.classList.add('opacity-0');
            setTimeout(() => {
                mobileBackdrop.classList.add('hidden');
                document.body.style.overflow = '';
            }, 250);
        }
    }

    if (mobileMenuToggle) mobileMenuToggle.addEventListener('click', openMobileMenu);
    if (mobileClose) mobileClose.addEventListener('click', closeMobileMenu);
    if (mobileBackdrop) mobileBackdrop.addEventListener('click', closeMobileMenu);

    // 2. Global Search Modal
    const searchTrigger = document.getElementById('tn-search-trigger');
    const searchModal = document.getElementById('tn-search-modal');
    const searchClose = document.getElementById('tn-search-close');
    const searchInput = document.getElementById('tn-search-input');

    function openSearchModal() {
        if (searchModal) {
            searchModal.classList.remove('hidden');
            setTimeout(() => {
                searchModal.classList.remove('opacity-0');
                if (searchInput) searchInput.focus();
            }, 50);
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSearchModal() {
        if (searchModal) {
            searchModal.classList.add('opacity-0');
            setTimeout(() => {
                searchModal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 200);
        }
    }

    if (searchTrigger) searchTrigger.addEventListener('click', openSearchModal);
    if (searchClose) searchClose.addEventListener('click', closeSearchModal);

    // Keyboard shortcut: Ctrl+K or Cmd+K or Slash to search
    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            if (searchModal && searchModal.classList.contains('hidden')) {
                openSearchModal();
            } else {
                closeSearchModal();
            }
        }
        if (e.key === 'Escape') {
            closeSearchModal();
            closeMobileMenu();
            closeAdminMobileMenu();
        }
    });

    // 2b. Admin Mobile Drawer Toggle
    const adminMobileToggle = document.getElementById('admin-mobile-toggle');
    const adminMobileDrawer = document.getElementById('admin-mobile-drawer');
    const adminMobileBackdrop = document.getElementById('admin-mobile-backdrop');
    const adminMobileClose = document.getElementById('admin-mobile-close');

    function openAdminMobileMenu() {
        if (adminMobileDrawer && adminMobileBackdrop) {
            adminMobileDrawer.classList.remove('-translate-x-full');
            adminMobileBackdrop.classList.remove('hidden', 'opacity-0');
            adminMobileBackdrop.classList.add('opacity-100');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeAdminMobileMenu() {
        if (adminMobileDrawer && adminMobileBackdrop) {
            adminMobileDrawer.classList.add('-translate-x-full');
            adminMobileBackdrop.classList.remove('opacity-100');
            adminMobileBackdrop.classList.add('opacity-0');
            setTimeout(() => {
                adminMobileBackdrop.classList.add('hidden');
                document.body.style.overflow = '';
            }, 250);
        }
    }

    if (adminMobileToggle) adminMobileToggle.addEventListener('click', openAdminMobileMenu);
    if (adminMobileClose) adminMobileClose.addEventListener('click', closeAdminMobileMenu);
    if (adminMobileBackdrop) adminMobileBackdrop.addEventListener('click', closeAdminMobileMenu);

    // 3. Toast Notification auto-dismiss
    document.querySelectorAll('.tn-toast').forEach((toast) => {
        const dismissBtn = toast.querySelector('.tn-toast-dismiss');
        const autoDismissTime = toast.dataset.timeout || 4000;

        const dismiss = () => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        };

        if (dismissBtn) dismissBtn.addEventListener('click', dismiss);
        if (autoDismissTime > 0) setTimeout(dismiss, autoDismissTime);
    });

    // 4. Reading Progress Bar (if present on article pages)
    const progressBar = document.getElementById('tn-reading-progress');
    if (progressBar) {
        window.addEventListener('scroll', () => {
            const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            progressBar.style.width = scrolled + '%';
        }, { passive: true });
    }
});
