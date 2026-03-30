document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("themeToggle");

    if (!toggle) return;

    const setTheme = (theme) => {
        if (theme === "dark") {
            document.body.classList.add("dark");
            toggle.textContent = "☀️";
            localStorage.setItem("theme", "dark");
        } else {
            document.body.classList.remove("dark");
            toggle.textContent = "🌙";
            localStorage.setItem("theme", "light");
        }
    };

    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        setTheme("dark");
    } else if (savedTheme === "light") {
        setTheme("light");
    } else {
        const prefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
        setTheme(prefersDark ? "dark" : "light");
    }

    toggle.addEventListener("click", function () {
        setTheme(document.body.classList.contains("dark") ? "light" : "dark");
    });
});