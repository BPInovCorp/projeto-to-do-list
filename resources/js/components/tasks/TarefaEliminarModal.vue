<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { useTarefas } from '../../composables/useTarefas';
import { useI18n } from '../../i18n';

const props = defineProps({
    tarefa: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['fechar', 'eliminada']);

const { eliminarTarefa, aEliminar } = useTarefas();
const { t } = useI18n();

const botaoCancelarRef = ref(null);

function handleKeydown(evento) {
    if (evento.key === 'Escape') {
        emit('fechar');
    }
}

async function confirmarEliminacao() {
    try {
        await eliminarTarefa(props.tarefa.id);
        emit('eliminada');
        emit('fechar');
    } catch (error) {
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
    if (botaoCancelarRef.value) {
        botaoCancelarRef.value.focus();
    }
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity"
        @click.self="emit('fechar')"
    >
        <div
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="modal-eliminar-titulo"
            aria-describedby="modal-eliminar-descricao"
            class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden transform transition-all p-6 sm:p-8"
        >
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 id="modal-eliminar-titulo" class="text-lg font-bold text-slate-900">
                        {{ t('tasks.delete_confirm_title') }}
                    </h3>
                    <p class="text-xs text-slate-500 truncate max-w-xs">
                        {{ tarefa.titulo }}
                    </p>
                </div>
            </div>

            <p id="modal-eliminar-descricao" class="text-sm text-slate-600 mb-6 leading-relaxed">
                {{ t('tasks.delete_confirm_message') }}
            </p>

            <div class="flex items-center justify-end gap-3">
                <button
                    ref="botaoCancelarRef"
                    type="button"
                    @click="emit('fechar')"
                    :disabled="aEliminar"
                    class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400 disabled:opacity-50"
                >
                    {{ t('tasks.cancel') }}
                </button>
                <button
                    type="button"
                    @click="confirmarEliminacao"
                    :disabled="aEliminar"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold transition-colors shadow-xs focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg v-if="aEliminar" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ aEliminar ? t('tasks.deleting') : t('tasks.confirm_delete') }}</span>
                </button>
            </div>
        </div>
    </div>
</template>