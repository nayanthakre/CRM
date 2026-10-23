(function () {
    var buttons = document.querySelectorAll("[data-toggle-password]");

    buttons.forEach(function (button) {
        button.addEventListener("click", function () {
            var input = document.getElementById(button.getAttribute("aria-controls"));
            if (!input) {
                return;
            }

            var reveal = input.type === "password";
            input.type = reveal ? "text" : "password";
            button.setAttribute("aria-pressed", reveal ? "true" : "false");
            button.textContent = reveal ? "Hide" : "Show";
        });
    });
})();
