/**
 * TopNews — Reading Progress Indicator Module
 *
 * Tracks reading progress through the article body and updates
 * a subtle red indicator bar below the sticky navigation header.
 */
export function initReadingProgress() {
    const progressBar = document.getElementById('reading-progress-indicator');
    const articleBody = document.getElementById('article-content-body');

    if (!progressBar || !articleBody) {
        return;
    }

    let ticking = false;

    function updateProgress() {
        const rect = articleBody.getBoundingClientRect();
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;
        const totalHeight = rect.height;
        const currentTop = rect.top;

        // When top of article body hits top of viewport (offset for header)
        const startOffset = 100;
        const scrolledDistance = Math.max(0, startOffset - currentTop);
        const scrollableDistance = totalHeight - (windowHeight / 2);

        let percentage = 0;
        if (scrollableDistance > 0) {
            percentage = Math.min(100, Math.max(0, (scrolledDistance / scrollableDistance) * 100));
        } else if (currentTop <= startOffset) {
            percentage = 100;
        }

        progressBar.style.width = `${percentage}%`;
        progressBar.setAttribute('aria-valuenow', Math.round(percentage));
        ticking = false;
    }

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(updateProgress);
            ticking = true;
        }
    }, { passive: true });

    // Initial update
    updateProgress();
}
