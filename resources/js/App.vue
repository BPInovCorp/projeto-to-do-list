<script setup>
import { onMounted, ref } from 'vue';
import { useI18n } from './i18n';
import { useAuth } from './composables/useAuth';
import LanguageSwitcher from './components/LanguageSwitcher.vue';
import LoginForm from './components/auth/LoginForm.vue';
import RegisterForm from './components/auth/RegisterForm.vue';
import TarefaForm from './components/tasks/TarefaForm.vue';
import TarefaLista from './components/tasks/TarefaLista.vue';

const { t } = useI18n();
const { user, isAuthenticated, checkAuth, logout } = useAuth();

const authView = ref('login');

onMounted(() => {
    checkAuth();
});
</script>

<template>
    <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800">
        <header class="border-b border-slate-200 bg-white shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="h-9 w-9 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-xs">
                        I
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-slate-900 leading-tight">
                            {{ t('app.brand') }}
                        </h1>
                        <p class="text-xs text-slate-500">
                            {{ t('app.tagline') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3 sm:space-x-4">
                    <LanguageSwitcher />

                    <template v-if="isAuthenticated">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-xs text-slate-400">{{ t('auth.authenticated_as') }}</span>
                            <span class="text-sm font-semibold text-slate-800">{{ user?.nome }}</span>
                        </div>
                        <button
                            type="button"
                            @click="logout"
                            class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            {{ t('auth.logout') }}
                        </button>
                    </template>
                    <template v-else>
                        <button
                            type="button"
                            @click="authView = authView === 'login' ? 'register' : 'login'"
                            class="px-3 py-1.5 rounded-lg bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            {{ authView === 'login' ? t('auth.register') : t('auth.login') }}
                        </button>
                    </template>
                </div>
            </div>
        </header>

        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <template v-if="isAuthenticated">
                <div class="max-w-4xl mx-auto">
                    <TarefaForm />
                    <TarefaLista />
                </div>
            </template>
            <template v-else>
                <LoginForm
                    v-if="authView === 'login'"
                    @switch-to-register="authView = 'register'"
                />
                <RegisterForm
                    v-else
                    @switch-to-login="authView = 'login'"
                />
            </template>
        </main>

        <footer class="border-t border-slate-200 bg-white py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
                {{ t('app.copyright') }}
            </div>
        </footer>
    </div>
</template>