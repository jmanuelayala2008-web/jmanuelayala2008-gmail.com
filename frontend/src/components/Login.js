import { api } from '../utils/api.js';

const form = document.getElementById('loginForm');
const notice = document.getElementById('loginNotice');
const submitButton = form.querySelector('button[type="submit"]');

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    notice.hidden = true;
    submitButton.disabled = true;
    submitButton.textContent = 'Verificando...';
    const keepSession = form.elements.remember.checked;
    sessionStorage.removeItem('sistemamenuSessionId');
    localStorage.removeItem('sistemamenuSessionId');
    sessionStorage.removeItem('sistemamenuUser');
    localStorage.removeItem('sistemamenuUser');

    try {
        const result = await api.post('login', {
            username: form.elements.username.value,
            password: form.elements.password.value,
        });
        if (!result.authenticated || !result.sessionId || !result.user?.username) {
            throw new Error('El servidor no devolvió una sesión válida.');
        }
        const sessionStore = keepSession ? localStorage : sessionStorage;
        sessionStore.setItem('sistemamenuSessionId', result.sessionId);
        window.location.href = '../../index.html';
    } catch (error) {
        notice.textContent = error.message.includes('HTTP 401')
            ? 'Usuario o contraseña incorrectos.'
            : error.message;
        notice.hidden = false;
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Ingresar al sistema';
    }
});
