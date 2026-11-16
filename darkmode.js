function applySavedTheme() {
    const saved = localStorage.getItem("theme");
    if (saved === "dark") document.body.classList.add("dark-mode");
    else document.body.classList.remove("dark-mode");
    updateThemeToggle();
}

function updateThemeToggle() {
    const isDark = document.body.classList.contains("dark-mode");
    document.querySelectorAll(".theme-toggle").forEach(function (button) {
        const label = button.querySelector(".theme-toggle-label");
        const icon = button.querySelector(".theme-toggle-icon");
        if (label) label.textContent = isDark ? "Light mode" : "Dark mode";
        if (icon) icon.textContent = isDark ? "☀" : "◐";
        button.setAttribute("aria-pressed", isDark ? "true" : "false");
    });
}

function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
    updateThemeToggle();
}

document.addEventListener("DOMContentLoaded", applySavedTheme);
