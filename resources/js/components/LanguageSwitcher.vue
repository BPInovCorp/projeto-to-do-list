<script setup>
import { ref } from 'vue';
import { useI18n } from '../i18n';

const { currentLocale, setLocale, t, supportedLocales } = useI18n();

const isChanging = ref(false);

async function changeLocale(locale) {
    if (currentLocale.value === locale || isChanging.value) {
        return;
    }

    isChanging.value = true;

    try {
        await setLocale(locale);
    } catch (error) {
        console.error(error);
    } finally {
        isChanging.value = false;
    }
}
</script>

<template>
    <div
        role="group"
        :aria-label="t('locale.label')"
        :aria-busy="isChanging"
        class="inline-flex items-center rounded-lg bg-slate-100 p-1 border border-slate-200"
    >
        <button
            v-for="locale in supportedLocales"
            :key="locale"
            type="button"
            :disabled="isChanging"
            :aria-pressed="currentLocale === locale"
            :aria-label="locale === 'pt_PT' ? t('locale.switch_to_pt') : t('locale.switch_to_en')"
            @click="changeLocale(locale)"
            class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed"
            :class="currentLocale === locale ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
        >
            <span v-if="locale === 'pt_PT'">PT</span>
            <span v-else>EN</span>
        </button>
    </div>
</template>