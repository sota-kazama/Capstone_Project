function updateServerTheme(theme) {
    fetch("../helpers/theme_set.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "theme=" + theme,
    })
        .then((res) => res.json())
        .then((data) => console.log("テーマ更新:", data))
        .catch((err) => console.error(err));
}

function toggleTheme() {
    const themeLink = document.getElementById("theme-css");
    const icon = document.getElementById("theme-icon");
    const body = document.body;

    // 現在のテーマを body のクラスから判定
    const isDark = body.classList.contains("dark-mode");
    let newTheme;

    if (isDark) {
        // ダーク → ライト
        themeLink.href = "../css_theme/light.css";
        body.classList.remove("dark-mode");
        body.classList.add("light-mode");
        icon.classList.remove("bi-sun");
        icon.classList.add("bi-moon");
        newTheme = "light";
    } else {
        // ライト → ダーク
        themeLink.href = "../css_theme/dark.css";
        body.classList.remove("light-mode");
        body.classList.add("dark-mode");
        icon.classList.remove("bi-moon");
        icon.classList.add("bi-sun");
        newTheme = "dark";
    }

    updateServerTheme(newTheme);
}

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("theme-toggle-btn");
    if (btn) {
        btn.addEventListener("click", toggleTheme);
    }
});
