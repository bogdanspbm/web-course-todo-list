export function bindAuthForm() {
    document.addEventListener('DOMContentLoaded', function () {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const registrationForm = document.getElementById('registrationForm');
        const submit = document.getElementById("submit")

        emailInput.addEventListener('blur', checkEmailAvailability);
        emailInput.addEventListener("focus", () => {
            emailInput.style.outline = "none";
            emailInput.style.color = "#242424";
            document.getElementById("email-sup").style.color = "#cf132200";
        });

        passwordInput.addEventListener('blur', checkPasswordSize);
        passwordInput.addEventListener("focus", () => {
            passwordInput.style.outline = "none";
            passwordInput.style.color = "#242424";
            document.getElementById("pass-sup").style.color = "#cf132200";
        });


        function checkPasswordSize() {
            const pass = passwordInput.value;
            if (!pass) {
                passwordInput.style.outline = "none";
                passwordInput.style.color = "#242424";
                document.getElementById("pass-sup").style.color = "#cf132200";
                return;
            }

            if (!pass || pass.length < 8) {
                passwordInput.style.outline = "1px solid #f5222d";
                passwordInput.style.color = "#cf1322";
                document.getElementById("pass-sup").style.color = "#cf1322";
            } else {
                passwordInput.style.outline = "1px solid #52c41a";
                passwordInput.style.color = "#389e0d";
                document.getElementById("pass-sup").style.color = "#cf132200";
            }
        }


        function checkEmailAvailability() {
            const email = emailInput.value;

            if (!email) {
                emailInput.style.outline = "none";
                emailInput.style.color = "#242424";
                document.getElementById("email-sup").style.color = "#cf132200";
                return;
            }

            if (!email || !email.includes("@") || !email.split("@")[0] || !email.split("@")[1].includes(".")) {
                emailInput.style.outline = "1px solid #f5222d";
                emailInput.style.color = "#cf1322";
                document.getElementById("email-sup").innerText = "Неверный формат почты"
                document.getElementById("email-sup").style.color = "#cf1322";
                return;
            }

        }

    });

}