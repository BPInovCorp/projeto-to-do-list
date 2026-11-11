<script setup>
import { reactive, ref } from 'vue';
import { useAuth } from '../../composables/useAuth';
import { useI18n } from '../../i18n';

const emit = defineEmits(['success', 'switch-to-register']);

const { login, isLoading } = useAuth();
const { t } = useI18n();

const form = reactive({
    email: '',
    password: '',
});

const errors = ref({});
const generalError = ref('');

async function handleSubmit() {
    errors.value = {};
    generalError.value = '';

    try {
        await login(form);
        emit('success');
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors || {};
            generalError.value = error.response.data.message || '';
        } else if (error.response && error.response.status === 429) {
            generalError.value = error.response.data.message || '';
        } else {
            generalError.value = 'Ocorreu um erro inesperado. Por favor, tente novamente.';
        }
    }
}
</script>

<template>
    <div class="w-full max-w-md mx-auto bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 text-center mb-6">
            {{ t('auth.login') }}
        </h2>

        <div
            v-if="generalError"
            role="alert"
            class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm"
        >
            {{ generalError }}
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div>
                <label for="login-email" class="block text-sm font-medium text-slate-700 mb-1">
                    {{ t('auth.email') }}
                </label>
                <input
                    id="login-email"
                    v-model="form.email"
                    type="email"
                    required
                    autocomplete="email"
                    :placeholder="t('auth.email_placeholder')"
                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                    :class="{ 'border-rose-400 focus:ring-rose-500 focus:border-rose-500': errors.email }"
                />
                <p v-if="errors.email" class="mt-1 text-xs text-rose-600">
                    {{ errors.email[0] }}
                </p>
            </div>

            <div>
                <label for="login-password" class="block text-sm font-medium text-slate-700 mb-1">
                    {{ t('auth.password') }}
                </label>
                <input
                    id="login-password"
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="t('auth.password_placeholder')"
                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                    :class="{ 'border-rose-400 focus:ring-rose-500 focus:border-rose-500': errors.password }"
                />
                <p v-if="errors.password" class="mt-1 text-xs text-rose-600">
                    {{ errors.password[0] }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="isLoading"
                class="w-full py-2.5 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-xs"
            >
                {{ isLoading ? t('auth.submitting') : t('auth.submit_login') }}
            </button>
        </form>

        <div class="mt-6 text-center">
            <button
                type="button"
                @click="emit('switch-to-register')"
                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium focus:outline-none focus:underline"
            >
                {{ t('auth.need_account') }}
            </button>
        </div>
    </div>
</template>