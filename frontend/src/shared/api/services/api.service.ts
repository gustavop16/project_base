import axios from 'axios';

const apiUrl = import.meta.env.VITE_API_URL;

const api = axios.create({
  baseURL:  apiUrl,
  withCredentials: true,
  timeout: 30000,
  headers: {
    //"Content-Type": "application/json",
    "Accept" : "application/json",
  }
});


// Adiciona token de autenticação automaticamente
api.interceptors.request.use(config => {
const token = localStorage.getItem("token");
if (token) {
  config.headers["Authorization"] = `Bearer ${token}`;
}
return config;
});

export { api };