import { ref } from 'vue';
import axios from 'axios';
import pt_PT from './locales/pt_PT.js';
import en from './locales/en.js';

const SUPPORTED_LOCALES = ['pt_PT', 'en'];

const messages = {
    pt_PT,
    en,
};

const storedLocale = typeof localStorage !== 'undefined' ? localStorage.getItem('locale') : null;
const initialLocale = SUPPORTED_LOCALES.includes(storedLocale) ? storedLocale : 'pt_PT';

axios.defaults.headers.common['X-Locale'] = initialLocale;

if (typeof document !== 'undefined') {
    document.documentElement.lang = initialLocale.replace('_', '-');
}

const currentLocale = ref(initialLocale);

function resolvePath(obj, path) {
    return path.split('.').reduce((acc, segment) => {
        if (acc && typeof acc === 'object' && segment in acc) {
            return acc[segment];
        }
        return undefined;
    }, obj);
}

function t(path) {
    const activeDict = messages[currentLocale.value] || {};
    const translated = resolvePath(activeDict, path);

    if (typeof translated === 'string') {
        return translated;
    }

    const fallbackDict = messages.en || {};
    const fallback = resolvePath(fallbackDict, path);

    if (typeof fallback === 'string') {
        return fallback;
    }

    return path;
}

async function setLocale(newLocale) {
    if (!SUPPORTED_LOCALES.includes(newLocale)) {
        return;
    }

    const previousLocale = currentLocale.value;

    try {
        await axios.post('/locale', { locale: newLocale });

        currentLocale.value = newLocale;
        localStorage.setItem('locale', newLocale);
        axios.defaults.headers.common['X-Locale'] = newLocale;

        if (typeof document !== 'undefined') {
            document.documentElement.lang = newLocale.replace('_', '-');
        }
    } catch (error) {
        currentLocale.value = previousLocale;
        throw error;
    }
}

export function useI18n() {
    return {
        currentLocale,
        t,
        setLocale,
        supportedLocales: SUPPORTED_LOCALES,
    };
}