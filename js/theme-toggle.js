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

    const currentHref = themeLink.getAttribute("href");
    let newTheme;

    if (currentHref.includes("light.css")) {
        // ダークへ
        themeLink.href = "../css_theme/dark.css";
        body.classList.remove("light-mode");
        body.classList.add("dark-mode");
        icon.classList.remove("bi-moon");
        icon.classList.add("bi-sun");
        newTheme = "dark";
    } else {
        // ライトへ
        themeLink.href = "../css_theme/light.css";
        body.classList.remove("dark-mode");
        body.classList.add("light-mode");
        icon.classList.remove("bi-sun");
        icon.classList.add("bi-moon");
        newTheme = "light";
    }

    updateServerTheme(newTheme);
}

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("theme-toggle-btn");
    if (btn) {
        btn.addEventListener("click", toggleTheme);
    }
});
