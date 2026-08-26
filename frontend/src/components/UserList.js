import { api } from '../utils/api.js';

export const getUserList = async () => {
    const container = document.getElementById('userTableList');
    container.innerHTML = '<tr><td class="px-6 py-4 text-slate-500" colspan="3">Cargando...</td></tr>';
    try {

        const users =await api.get('users');
        container.innerHTML = users.map(user => `
            <tr>
                <td class="px-6 py-4">${user.id}</td>
                <td class="px-6 py-4">${user.name}</td>
                <td class="px-6 py-4">${user.email}</td>
            </tr>
        `).join('');
    } catch (error) {
        container.innerHTML = '<tr><td class="px-6 py-4 text-red-600" colspan="3">Error al cargar usuarios.</td></tr>';
    }
};