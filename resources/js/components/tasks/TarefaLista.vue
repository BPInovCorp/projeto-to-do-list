<script setup>
import { onMounted } from 'vue';
import { useTarefas } from '../../composables/useTarefas';
import { useI18n } from '../../i18n';
import TarefaItem from './TarefaItem.vue';

const { tarefas, carregando, listarTarefas } = useTarefas();
const { t } = useI18n();

onMounted(() => {
    listarTarefas();
});
</script>

<template>
    <section :aria-busy="carregando" class="space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div>
                <h3 class="text-xl font-bold text-slate-900">
                    {{ t('tasks.title') }}
                </h3>
                <p class="text-xs text-slate-500">
                    {{ t('tasks.subtitle') }}
                </p>
            </div>
            <span
                v-if="!carregando && tarefas.length > 0"
                class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200"
            >
                {{ tarefas.length }}
            </span>
        </div>

        <div v-if="carregando" class="space-y-3">
            <div
                v-for="n in 3"
                :key="n"
                class="bg-white rounded-xl border border-slate-200 p-5 animate-pulse space-y-3"
            >
                <div class="flex gap-2">
                    <div class="h-5 w-16 bg-slate-200 rounded-full"></div>
                    <div class="h-5 w-20 bg-slate-200 rounded-full"></div>
                </div>
                <div class="h-5 w-3/4 bg-slate-200 rounded-md"></div>
                <div class="h-4 w-1/2 bg-slate-100 rounded-md"></div>
            </div>
        </div>

        <div
            v-else-if="tarefas.length === 0"
            class="bg-white rounded-xl border border-dashed border-slate-300 p-8 sm:p-12 text-center"
        >
            <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h4 class="text-base font-semibold text-slate-900 mb-1">
                {{ t('tasks.empty_state') }}
            </h4>
            <p class="text-sm text-slate-500 max-w-sm mx-auto">
                {{ t('tasks.empty_state_action') }}
            </p>
        </div>

        <div v-else class="space-y-3">
            <TarefaItem
                v-for="tarefa in tarefas"
                :key="tarefa.id"
                :tarefa="tarefa"
            />
        </div>
    </section>
</template>