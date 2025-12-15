const hamburger = document.getElementById("hamburger");
const menu = document.getElementById("menu");

// ハンバーガー開閉
hamburger.addEventListener("click", () => {
    hamburger.classList.toggle("active");
    menu.classList.toggle("active");
});

// サブメニュー開閉
const submenuParents = menu.querySelectorAll("li > .arrow");
submenuParents.forEach((arrow) => {
    arrow.addEventListener("click", (e) => {
        e.stopPropagation(); // 親メニューへの影響を防ぐ
        const li = e.target.parentElement;
        li.classList.toggle("active");
    });
});

document.body.style.overflow = isActive ? "hidden" : "";
