import { ref, computed } from 'vue';
import axios from 'axios';

const user = ref(null);
const isLoading = ref(false);
const isAuthenticated = computed(() => !!user.value);

function atualizarCsrfToken(novoToken) {
    if (novoToken) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = novoToken;
        if (typeof document !== 'undefined') {
            const meta = document.head.querySelector('meta[name="csrf-token"]');
            if (meta) {
                meta.content = novoToken;
            }
        }
    }
}

export function useAuth() {
    async function checkAuth() {
        isLoading.value = true;
        try {
            const response = await axios.get('/utilizador');
            user.value = response.data.utilizador;
        } catch (error) {
            user.value = null;
        } finally {
            isLoading.value = false;
        }
    }

    async function login(credentials) {
        isLoading.value = true;
        try {
            const response = await axios.post('/entrar', credentials);
            user.value = response.data.utilizador;
            atualizarCsrfToken(response.data.csrf_token);
            return response.data;
        } finally {
            isLoading.value = false;
        }
    }

    async function register(data) {
        isLoading.value = true;
        try {
            const response = await axios.post('/registo', data);
            user.value = response.data.utilizador;
            atualizarCsrfToken(response.data.csrf_token);
            return response.data;
        } finally {
            isLoading.value = false;
        }
    }

    async function logout() {
        isLoading.value = true;
        try {
            const response = await axios.post('/sair');
            user.value = null;
            atualizarCsrfToken(response.data.csrf_token);
        } finally {
            isLoading.value = false;
        }
    }

    return {
        user,
        isLoading,
        isAuthenticated,
        checkAuth,
        login,
        register,
        logout,
    };
}