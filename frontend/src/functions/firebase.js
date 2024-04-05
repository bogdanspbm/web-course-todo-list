import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.10.0/firebase-app.js'
import { getAnalytics } from 'https://www.gstatic.com/firebasejs/10.10.0/firebase-analytics.js'
import { getAuth } from 'https://www.gstatic.com/firebasejs/10.10.0/firebase-auth.js'


const firebaseConfig = {

    apiKey: "AIzaSyACeoZr635AKOHPh5_imf5FkC8bqHL4Olo",

    authDomain: "madzhuga-todo.firebaseapp.com",

    projectId: "madzhuga-todo",

    storageBucket: "madzhuga-todo.appspot.com",

    messagingSenderId: "792461273317",

    appId: "1:792461273317:web:c8c0a983b1b27ae46ccc0c",

    measurementId: "G-9K3S0SR544"

};

export function initFirebase() {
    document.firebaseApp = initializeApp(firebaseConfig);
    document.firebaseAnalytics = getAnalytics(document.firebaseApp);
    document.firebaseAuth = getAuth(document.firebaseApp);
}

export function getFirebaseAuth() {
    if (!document.firebaseAuth) {
        initFirebase();
    }

    return document.firebaseAuth;
}

