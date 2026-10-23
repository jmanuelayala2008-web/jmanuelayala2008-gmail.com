// Host declarado para el backend en la configuración local de Apache.
// Puede reemplazarse antes de main.js definiendo window.SISTEMA_MENU_API_URL.
//const API_URL = new URL(window.SISTEMA_MENU_API_URL || 'http://api.sistemamenu/');
const API_URL ='https://jmanuelayala2008-gmail-com-2.onrender.com';

function sessionHeaders(headers = {}) {
    const sessionId = sessionStorage.getItem('sistemamenuSessionId')
        || localStorage.getItem('sistemamenuSessionId');
    if (sessionId) headers['X-Session-Id'] = sessionId;
    return headers;
}

export const api = {
    post: async (endpoint, payload) => {
        const url = new URL(endpoint.replace(/^\/+/, ''), API_URL);
        let response;
        try {
            response = await fetch(url, {
                method: 'POST',
                headers: sessionHeaders({
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                }),
                body: JSON.stringify(payload),
            });
        } catch (error) {
            throw new Error(`No se pudo conectar con la API (${url.href}): ${error.message}`);
        }

        const body = await response.text();
        let data;
        try {
            data = JSON.parse(body);
        } catch {
            throw new Error(`The API returned an invalid response (HTTP ${response.status}).`);
        }
        if (!response.ok) {
            const detail = data?.message || data?.error || response.statusText;
            throw new Error(`HTTP ${response.status}: ${detail}`);
        }
        return data;
    },

    getSession: async () => {
        const url = new URL('session', API_URL);
        let response;
        try {
            response = await fetch(url, {
                method: 'GET',
                headers: sessionHeaders({ Accept: 'application/json' }),
            });
        } catch (error) {
            throw new Error(`No se pudo verificar la sesion (${url.href}): ${error.message}`);
        }

        const body = await response.text();
        let data;
        try {
            data = JSON.parse(body);
        } catch {
            throw new Error(`Respuesta JSON invalida (HTTP ${response.status}).`);
        }
        if (!response.ok) {
            const detail = data?.message || data?.error || response.statusText;
            throw new Error(`HTTP ${response.status}: ${detail}`);
        }
        return data;
    },

    logout: async () => api.post('logout', {}),

    get: async (endpoint) => {
        const url = new URL(endpoint.replace(/^\/+/, ''), API_URL);
        console.info(`[SISTEMAMENU API] GET ${url.href}`);

        let response;
        try {
            response = await fetch(url, {
                headers: sessionHeaders({ Accept: 'application/json' }),
            });
        } catch (error) {
            console.error(`[SISTEMAMENU API] Falló la petición a ${url.href}`, error);
            throw new Error(`No se pudo conectar con la API (${url.href}): ${error.message}`);
        }

        console.info(`[SISTEMAMENU API] HTTP ${response.status} ${response.statusText} — ${url.href}`);
        const body = await response.text();
        if (!response.ok) {
            let detail = body || response.statusText;
            try {
                const errorData = JSON.parse(body);
                detail = errorData?.error || errorData?.message || detail;
            } catch {
                // Conserva el cuerpo HTTP original como detalle de diagnóstico.
            }
            console.error(`[SISTEMAMENU API] HTTP ${response.status} en ${url.href}`, detail);
            throw new Error(`HTTP ${response.status}: ${detail}`);
        }

        let data;
        try {
            data = JSON.parse(body);
        } catch (error) {
            console.error('[SISTEMAMENU API] Respuesta JSON inválida', {
                url: url.href,
                status: response.status,
                contentType: response.headers.get('content-type'),
                body,
                error,
            });
            throw new Error(`La API respondió HTTP ${response.status}, pero el cuerpo no es JSON válido.`);
        }

        if (!Array.isArray(data)) {
            console.error('[SISTEMAMENU API] Se esperaba una lista JSON', { url: url.href, status: response.status, data });
            throw new Error('La API respondió JSON válido, pero no devolvió una lista de usuarios.');
        }

        return data;
    },
};
