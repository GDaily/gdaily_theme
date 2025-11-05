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
});
