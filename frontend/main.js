import { getUserList } from './src/components/UserList.js';
import { getClientesList } from './src/components/ClientesList.js';
import { getProductosList } from './src/components/ProductosList.js';
import { api } from './src/utils/api.js';

const app = document.getElementById('app');
const links = document.querySelectorAll('[data-view]');
const authButton = document.getElementById('authButton');
const authUserName = document.getElementById('authUserName');
const authNotice = document.getElementById('authNotice');
let authenticatedUser = null;

const views = {
    home: async () => {
        await loadView('home.html');
    },
    users: async () => {
        await loadView('user.html');
        await getUserList();
    },
    clientes: async () => {
        await loadView('clientes.html');
        await getClientesList();
    },
    productos: async () => {
        await loadView('productos.html');
        await getProductosList();
    },
};

async function loadView(filename) {
    const response = await fetch(new URL(`./src/views/${filename}`, document.baseURI));
    if (!response.ok) throw new Error(`No se pudo cargar la vista (${response.status})`);
    app.innerHTML = await response.text();
}

function renderAuthentication(user) {
    authenticatedUser = user && typeof user.username === 'string' ? user : null;
    if (authenticatedUser) {
        authUserName.textContent = authenticatedUser.username;
        authUserName.hidden = false;
        authUserName.classList.remove('hidden');
        authButton.textContent = 'Cerrar sesión';
    } else {
        authUserName.textContent = '';
        authUserName.hidden = true;
        authUserName.classList.add('hidden');
        authButton.textContent = 'Iniciar sesión';
    }
}

function clearStoredSession() {
    sessionStorage.removeItem('sistemamenuSessionId');
    localStorage.removeItem('sistemamenuSessionId');
    sessionStorage.removeItem('sistemamenuUser');
    localStorage.removeItem('sistemamenuUser');
}

async function restoreAuthentication() {
    // A username saved by older versions is not proof of an active session.
    sessionStorage.removeItem('sistemamenuUser');
    localStorage.removeItem('sistemamenuUser');
    const sessionId = sessionStorage.getItem('sistemamenuSessionId')
        || localStorage.getItem('sistemamenuSessionId');
    if (!sessionId) {
        renderAuthentication(null);
        return;
    }

    try {
        const result = await api.getSession();
        if (!result.authenticated || !result.user?.username) throw new Error('No hay una sesión válida.');
        renderAuthentication(result.user);
    } catch (error) {
        renderAuthentication(null);
        if (error.message.includes('HTTP 401')) {
            clearStoredSession();
        } else {
            console.error('No se pudo verificar la sesión:', error);
            authNotice.textContent = error.message;
            authNotice.hidden = false;
        }
    }
}

authButton.addEventListener('click', async () => {
    authNotice.hidden = true;
    if (!authenticatedUser) {
        window.location.href = './src/views/login.html';
        return;
    }

    authButton.disabled = true;
    try {
        await api.logout();
        clearStoredSession();
        renderAuthentication(null);
    } catch (error) {
        console.error('No se pudo cerrar la sesión:', error);
        authNotice.textContent = error.message;
        authNotice.hidden = false;
    } finally {
        authButton.disabled = false;
    }
});

const setActiveLink = (view) => {
    links.forEach(link => {
        link.classList.toggle('bg-white/10', link.dataset.view === view);
        link.classList.toggle('text-white', link.dataset.view === view);
    });
};

links.forEach(link => {
    link.addEventListener('click', async (event) => {
        event.preventDefault();
        const view = link.dataset.view;
        if (views[view]) {
            try {
                await views[view]();
                setActiveLink(view);
            } catch (error) {
                console.error('Error al abrir la sección:', error);
                app.innerHTML = '<section class="rounded-lg border border-red-200 bg-white p-6 text-red-700">No se pudo cargar esta sección. Revisa que Apache esté sirviendo los archivos de frontend.</section>';
            }
        }
    });
});

views.home().catch(error => {
    console.error('Error al cargar Inicio:', error);
    app.textContent = 'No se pudo cargar la página principal.';
});
restoreAuthentication();
setActiveLink('home');
