// テーマ切り替え関数
function toggleTheme() {
    const themeLink = document.getElementById("theme-css");
    const icon = document.getElementById("theme-icon");

    if (themeLink.href.includes("theme-light.css")) {
        // ダークモード
        themeLink.href = "css/theme-dark.css";
        icon.classList.remove("bi-moon");
        icon.classList.add("bi-sun");
        localStorage.setItem("theme", "dark"); // 保存
    } else {
        // ライトモード
        themeLink.href = "css/theme-light.css";
        icon.classList.remove("bi-sun");
        icon.classList.add("bi-moon");
        localStorage.setItem("theme", "light"); // 保存
    }
}

// ページ読み込み時にテーマ復元
document.addEventListener("DOMContentLoaded", () => {
    const savedTheme = localStorage.getItem("theme");
    const themeLink = document.getElementById("theme-css");
    const icon = document.getElementById("theme-icon");

    if (savedTheme === "dark") {
        themeLink.href = "css/theme-dark.css";
        icon.classList.remove("bi-moon");
        icon.classList.add("bi-sun");
    } else {
        themeLink.href = "css/theme-light.css";
        icon.classList.remove("bi-sun");
        icon.classList.add("bi-moon");
    }

    // ボタンクリックでテーマ切り替え
    document.getElementById("theme-toggle-btn").addEventListener("click", toggleTheme);
});
