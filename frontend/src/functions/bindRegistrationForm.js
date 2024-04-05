import {getFirebaseAuth} from "./firebase.js";
import { createUserWithEmailAndPassword } from 'https://www.gstatic.com/firebasejs/10.10.0/firebase-auth.js'

export function bindRegistrationForm() {
    const registrationForm = document.getElementById('registrationForm');

    registrationForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = registrationForm['email'].value;
        const password = registrationForm['password'].value;
        const confirmPassword = registrationForm['confirmPassword'].value;

        if (password !== confirmPassword) {
            // Пароли не совпадают
            console.error('Passwords do not match');
            return;
        }

        // Отправляем запрос на регистрацию пользователя
        fetch('http://185.47.54.162/redis/redis_auth_control.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                password: password
            })
        })
            .then(response => response.json())
            .then(data => {
                // Обработка ответа сервера
                console.log(data);
                // Вы можете выполнить дополнительные действия в зависимости от ответа сервера
            })
            .catch(error => {
                console.error('Registration failed:', error);
            });
    });

    const emailInput = registrationForm.querySelector('#email');
    const emailError = registrationForm.querySelector('#email-error');

    emailInput.addEventListener('input', () => {
        const email = emailInput.value;

        // Проверка занятости email
        fetch('http://185.47.54.162/redis/redis_auth_control.php?email=' + encodeURIComponent(email))
            .then(response => response.json())
            .then(data => {
                if (data.message === "Email уже занят.") {
                    emailError.textContent = 'Email уже занят.';
                } else {
                    emailError.textContent = '';
                }
            })
            .catch(error => {
                console.error('Error checking email:', error);
            });
    });
}
