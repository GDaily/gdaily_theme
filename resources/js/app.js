window.addEventListener('load', () => {
    document.querySelectorAll('code').forEach(code => {
        const lineHeight = parseFloat(getComputedStyle(code).lineHeight);
        const height = code.clientHeight;
        if (height > lineHeight * 1.5) {
            code.classList.add('multiline');
        }

        // 🌟 雙擊複製（桌面端）
        code.addEventListener('dblclick', () => copyCode(code));

        // 🌟 長按複製（行動端）
        let pressTimer;
        code.addEventListener('touchstart', () => {
            pressTimer = setTimeout(() => {
                copyCode(code);
            }, 600); // 長按 600ms 觸發
        });

        code.addEventListener('touchend', () => {
            clearTimeout(pressTimer);
        });
        code.addEventListener('touchmove', () => {
            clearTimeout(pressTimer); // 滑動時取消
        });
    });

    // 複製功能
    function copyCode(code) {
        navigator.clipboard.writeText(code.innerText).then(() => {
            const originalBg = code.style.backgroundColor;
            code.style.backgroundColor = '#DDAA00'; // 成功提示
            setTimeout(() => {
                code.style.backgroundColor = originalBg;
            }, 500);
        });
    }

    // 初始化圖片載入優化
    optimizeImageLoading();
});

// 圖片懶載入優化
function optimizeImageLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;

                    if (img.dataset.src && !img.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}
