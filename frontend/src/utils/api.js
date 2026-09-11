const API_URL = 'http://api.sistemamenu.com/';

export const api={
    //funcion para obtener data de la api
    get: async (endpoint) =>{
        try{
            const responce = await fetch(`${API_URL}${endpoint}`);
            if(!Response.ok) {
                throw new Error(`error! status: ${response.status}` );
            }
            return await Response.json();
        }catch (error) {
            console.error('Error al obtener data:',error);
            throw error;
        }
    },

};