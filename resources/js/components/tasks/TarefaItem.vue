<script setup>
import { computed } from 'vue';
import { useTarefas } from '../../composables/useTarefas';
import { useI18n } from '../../i18n';

const props = defineProps({
    tarefa: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['detalhes', 'editar', 'eliminar']);

const { alternarTarefa, aAlternar } = useTarefas();
const { t } = useI18n();

const isAlternando = computed(() => aAlternar.value.has(props.tarefa.id));
const isConcluida = computed(() => props.tarefa.estado === 'concluida');

async function handleAlternar() {
    try {
        await alternarTarefa(props.tarefa);
    } catch (error) {
    }
}

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

function formatarData(dataStr) {
    if (!dataStr) return '';
    const partes = dataStr.split('-');
    if (partes.length === 3) {
        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    }
    return dataStr;
}
</script>

<template>
    <article
        @click="emit('detalhes', tarefa)"
        class="group bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-xs transition-all hover:shadow-sm hover:border-slate-300 cursor-pointer"
        :class="{ 'opacity-75 bg-slate-50/60': isConcluida }"
    >
        <div class="flex items-start gap-3 sm:gap-4">
            <button
                type="button"
                @click.stop="handleAlternar"
                :disabled="isAlternando"
                class="mt-1 w-5 h-5 rounded-md border flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 shrink-0 disabled:opacity-50"
                :class="isConcluida ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-slate-300 hover:border-indigo-500 bg-white'"
                :aria-label="isConcluida ? t('tasks.mark_as_pending') : t('tasks.mark_as_completed')"
            >
                <svg v-if="isConcluida" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </button>

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border"
                        :class="prioridadeConfig.classe"
                    >
                        {{ prioridadeConfig.rotulo }}
                    </span>

                    <span
                        v-if="isConcluida"
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"
                    >
                        {{ t('tasks.status_completed') }}
                    </span>
                    <span
                        v-else
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200"
                    >
                        {{ t('tasks.status_pending') }}
                    </span>

                    <span
                        v-if="tarefa.data_vencimento"
                        class="text-xs text-slate-500 inline-flex items-center gap-1"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ t('tasks.due_date') }}: {{ formatarData(tarefa.data_vencimento) }}</span>
                    </span>
                </div>

                <h4
                    class="text-base font-semibold text-slate-900 truncate"
                    :class="{ 'line-through text-slate-400': isConcluida }"
                >
                    {{ tarefa.titulo }}
                </h4>

                <p
                    v-if="tarefa.descricao"
                    class="mt-1 text-sm text-slate-600 line-clamp-2 leading-relaxed"
                >
                    {{ tarefa.descricao }}
                </p>
            </div>

            <div class="flex items-center gap-1 shrink-0 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                <button
                    type="button"
                    @click.stop="emit('editar', tarefa)"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    :aria-label="t('tasks.edit')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click.stop="emit('eliminar', tarefa)"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors focus:outline-none focus:ring-2 focus:ring-rose-500"
                    :aria-label="t('tasks.delete')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </article>
</template>