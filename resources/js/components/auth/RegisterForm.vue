<script setup>
import { reactive, ref } from 'vue';
import { useAuth } from '../../composables/useAuth';
import { useI18n } from '../../i18n';

const emit = defineEmits(['success', 'switch-to-login']);

const { register, isLoading } = useAuth();
const { t } = useI18n();

const form = reactive({
    nome: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const errors = ref({});
const generalError = ref('');

async function handleSubmit() {
    errors.value = {};
    generalError.value = '';

    try {
        await register(form);
        emit('success');
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors || {};
            generalError.value = error.response.data.message || '';
        } else {
            generalError.value = 'Ocorreu um erro ao criar a conta. Por favor, tente novamente.';
        }
    }
}
</script>

<template>
    <div class="w-full max-w-md mx-auto bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 text-center mb-6">
            {{ t('auth.register') }}
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
                <label for="register-name" class="block text-sm font-medium text-slate-700 mb-1">
                    {{ t('auth.name') }}
                </label>
                <input
                    id="register-name"
                    v-model="form.nome"
                    type="text"
                    required
                    autocomplete="name"
                    :placeholder="t('auth.name_placeholder')"
                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                    :class="{ 'border-rose-400 focus:ring-rose-500 focus:border-rose-500': errors.nome }"
                />
                <p v-if="errors.nome" class="mt-1 text-xs text-rose-600">
                    {{ errors.nome[0] }}
                </p>
            </div>

            <div>
                <label for="register-email" class="block text-sm font-medium text-slate-700 mb-1">
                    {{ t('auth.email') }}
                </label>
                <input
                    id="register-email"
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
                <label for="register-password" class="block text-sm font-medium text-slate-700 mb-1">
                    {{ t('auth.password') }}
                </label>
                <input
                    id="register-password"
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="t('auth.password_placeholder')"
                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                    :class="{ 'border-rose-400 focus:ring-rose-500 focus:border-rose-500': errors.password }"
                />
                <p v-if="errors.password" class="mt-1 text-xs text-rose-600">
                    {{ errors.password[0] }}
                </p>
            </div>

            <div>
                <label for="register-password-confirmation" class="block text-sm font-medium text-slate-700 mb-1">
                    {{ t('auth.password_confirmation') }}
                </label>
                <input
                    id="register-password-confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="t('auth.password_confirmation_placeholder')"
                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                    :class="{ 'border-rose-400 focus:ring-rose-500 focus:border-rose-500': errors.password_confirmation }"
                />
                <p v-if="errors.password_confirmation" class="mt-1 text-xs text-rose-600">
                    {{ errors.password_confirmation[0] }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="isLoading"
                class="w-full py-2.5 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-xs"
            >
                {{ isLoading ? t('auth.submitting') : t('auth.submit_register') }}
            </button>
        </form>

        <div class="mt-6 text-center">
            <button
                type="button"
                @click="emit('switch-to-login')"
                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium focus:outline-none focus:underline"
            >
                {{ t('auth.already_registered') }}
            </button>
        </div>
    </div>
</template>