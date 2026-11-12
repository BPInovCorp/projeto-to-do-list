<script setup>
import { computed } from 'vue';
import { useI18n } from '../../i18n';

const props = defineProps({
    tarefa: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

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
    const partes = dataStr.split('-');
    if (partes.length === 3) {
        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    }
    return dataStr;
}
</script>

<template>
    <article
        class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs transition-shadow hover:shadow-sm"
        :class="{ 'opacity-75 bg-slate-50/50': isConcluida }"
    >
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
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
                        class="text-xs text-slate-500 inline-flex items-center gap-1 ml-auto sm:ml-0"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ t('tasks.due_date') }}: {{ formatarData(tarefa.data_vencimento) }}</span>
                    </span>
                </div>

                <h4
                    class="text-base font-semibold text-slate-900 truncate"
                    :class="{ 'line-through text-slate-500': isConcluida }"
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
        </div>
    </article>
</template>