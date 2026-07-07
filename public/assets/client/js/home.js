document.addEventListener("contextmenu", function (e) {
    e.preventDefault();
});
document.addEventListener("keydown", function (e) {
    if (e.keyCode === 123) {
        e.preventDefault();
        return false;
    }
    if (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) {
        e.preventDefault();
        return false;
    }
    if (e.ctrlKey && e.keyCode === 85) {
        e.preventDefault();
        return false;
    }
    if (e.ctrlKey && e.keyCode === 83) {
        e.preventDefault();
        return false;
    }
});

document.addEventListener("contextmenu", (e) => e.preventDefault());
document.addEventListener("keydown", function (e) {
    if (
        e.keyCode === 123 ||
        (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
        (e.ctrlKey && e.keyCode === 85)
    ) {
        e.preventDefault();
        return false;
    }
});

setInterval(function () {
    debugger;
}, 50);

document.addEventListener("DOMContentLoaded", function () {
    function attachLoadingEffect(formId, btnId, loadingText) {
        const form = document.getElementById(formId);
        const btn = document.getElementById(btnId);

        if (form && btn) {
            form.addEventListener("submit", function () {
                const textSpan = btn.querySelector(".btn-text");
                btn.classList.add("is-loading");
                if (textSpan) {
                    textSpan.innerText = loadingText;
                }
            });
        }
    }

    attachLoadingEffect("formLogin", "btnLogin", "Authenticating...");
    attachLoadingEffect("formRegister", "btnRegister", "Creating...");

    const pricingForms = document.querySelectorAll(".form-pricing");
    pricingForms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            const userConfirmed = confirm("Are you sure you want to switch to this plan? Any unused time on your current plan will be automatically credited to your account.");
            
            if (!userConfirmed) {
                event.preventDefault();
                return;
            }
            const btn = form.querySelector(".btn-pricing-neon");
            if (btn) {
                btn.classList.add("is-loading");
                const textSpan = btn.querySelector(".btn-text");
                if (textSpan) {
                    textSpan.innerText = "PROCESSING...";
                }
            }
        });
    });
});
