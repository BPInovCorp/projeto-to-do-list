<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useI18n } from '../../i18n';

const props = defineProps({
    tarefa: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['fechar', 'editar']);
const { t } = useI18n();
const botaoFecharRef = ref(null);

const prioridadeConfig = computed(() => {
    switch (props.tarefa.prioridade) {
        case 'alta':
            return {
                rotulo: t('tasks.priority_high'),
                classe: 'bg-rose-50 text-rose-700 border-rose-200',
            };
        case 'baixa':
            return {
                rotulo: t('tasks.priority_low'),
                classe: 'bg-slate-100 text-slate-700 border-slate-200',
            };
        default:
            return {
                rotulo: t('tasks.priority_medium'),
                classe: 'bg-amber-50 text-amber-700 border-amber-200',
            };
    }
});

const isConcluida = computed(() => props.tarefa.estado === 'concluida');

function formatarData(dataStr) {
    if (!dataStr) return '';
    const partes = dataStr.split('T')[0].split('-');
    if (partes.length === 3) {
        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    }
    return dataStr;
}

function handleKeydown(evento) {
    if (evento.key === 'Escape') {
        emit('fechar');
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
    if (botaoFecharRef.value) {
        botaoFecharRef.value.focus();
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
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-detalhes-titulo"
            class="w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden transform transition-all"
        >
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                        :class="prioridadeConfig.classe"
                    >
                        {{ prioridadeConfig.rotulo }}
                    </span>
                    <span
                        v-if="isConcluida"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"
                    >
                        {{ t('tasks.status_completed') }}
                    </span>
                    <span
                        v-else
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200"
                    >
                        {{ t('tasks.status_pending') }}
                    </span>
                </div>

                <button
                    ref="botaoFecharRef"
                    type="button"
                    @click="emit('fechar')"
                    class="text-slate-400 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-400 rounded-md p-1"
                    :aria-label="t('tasks.close')"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <h3
                        id="modal-detalhes-titulo"
                        class="text-xl font-bold text-slate-900 leading-snug"
                        :class="{ 'line-through text-slate-400': isConcluida }"
                    >
                        {{ tarefa.titulo }}
                    </h3>
                </div>

                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                        {{ t('tasks.input_description') }}
                    </h4>
                    <p v-if="tarefa.descricao" class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                        {{ tarefa.descricao }}
                    </p>
                    <p v-else class="text-sm text-slate-400 italic">
                        {{ t('tasks.no_description') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-600 pt-2">
                    <div v-if="tarefa.data_vencimento" class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span><strong>{{ t('tasks.input_due_date') }}:</strong> {{ formatarData(tarefa.data_vencimento) }}</span>
                    </div>

                    <div v-if="tarefa.concluida_em" class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span><strong>{{ t('tasks.completed_at') }}:</strong> {{ formatarData(tarefa.concluida_em) }}</span>
                    </div>

                    <div v-if="tarefa.created_at" class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><strong>{{ t('tasks.created_at') }}:</strong> {{ formatarData(tarefa.created_at) }}</span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="emit('fechar')"
                    class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400"
                >
                    {{ t('tasks.close') }}
                </button>
                <button
                    type="button"
                    @click="emit('editar')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors shadow-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>{{ t('tasks.edit') }}</span>
                </button>
            </div>
        </div>
    </div>
</template>