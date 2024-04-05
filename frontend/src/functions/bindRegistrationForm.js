import {getFirebaseAuth} from "./firebase.js";
import { createUserWithEmailAndPassword } from 'https://www.gstatic.com/firebasejs/10.10.0/firebase-auth.js'


export function bindRegistrationForm() {
    const registrationForm = document.getElementById('registrationForm');
    const auth = getFirebaseAuth();

    registrationForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = registrationForm['email'].value;
        const password = registrationForm['password'].value;

        createUserWithEmailAndPassword(auth, email, password)
            .then((userCredential) => {
                // Registered successfully
                const user = userCredential.user;
                console.log(user);
                // You can redirect to another page or perform any other actions upon successful registration
            })
            .catch((error) => {
                const errorCode = error.code;
                const errorMessage = error.message;
                console.error(errorMessage);
                // Handle errors here, e.g., display error message to the user
            });
    });
}