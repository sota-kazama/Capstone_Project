document.addEventListener("DOMContentLoaded", () => {
    // スクロール時にアンカー位置を補正（ヘッダー被り防止など）
    if (window.location.hash) {
        const el = document.querySelector(window.location.hash);
        if (el) {
            setTimeout(() => {
                el.scrollIntoView({ behavior: "smooth", block: "start" });
            }, 200);
        }
    }
});
