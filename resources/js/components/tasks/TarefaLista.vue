<script setup>
import { computed, onMounted, ref } from 'vue';
import { useTarefas } from '../../composables/useTarefas';
import { useI18n } from '../../i18n';
import TarefaItem from './TarefaItem.vue';
import TarefaDetalhesModal from './TarefaDetalhesModal.vue';
import TarefaEditarModal from './TarefaEditarModal.vue';
import TarefaEliminarModal from './TarefaEliminarModal.vue';

const {
    tarefas,
    carregando,
    filtros,
    contadores,
    listarTarefas,
    aplicarFiltro,
    limparFiltros,
} = useTarefas();

const { t } = useI18n();

const temFiltrosAtivos = computed(() => {
    return filtros.estado !== 'todas' || filtros.prioridade !== 'todas' || filtros.prazo !== 'todos';
});

const tarefaDetalhes = ref(null);
const tarefaEditar = ref(null);
const tarefaEliminar = ref(null);

function abrirDetalhes(tarefa) {
    fecharModais();
    tarefaDetalhes.value = tarefa;
}

function abrirEditar(tarefa) {
    fecharModais();
    tarefaEditar.value = tarefa;
}

function abrirEliminar(tarefa) {
    fecharModais();
    tarefaEliminar.value = tarefa;
}

function abrirEditarDeDetalhes() {
    const tarefa = tarefaDetalhes.value;
    fecharModais();
    if (tarefa) {
        tarefaEditar.value = tarefa;
    }
}

function fecharModais() {
    tarefaDetalhes.value = null;
    tarefaEditar.value = null;
    tarefaEliminar.value = null;
}

onMounted(() => {
    listarTarefas();
});
</script>

<template>
    <section :aria-busy="carregando" class="space-y-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs font-medium text-slate-500 block mb-1">{{ t('summary.total') }}</span>
                <span class="text-2xl font-bold text-slate-900">{{ contadores.total }}</span>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs font-medium text-blue-600 block mb-1">{{ t('summary.pending') }}</span>
                <span class="text-2xl font-bold text-blue-700">{{ contadores.pendentes }}</span>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs font-medium text-emerald-600 block mb-1">{{ t('summary.completed') }}</span>
                <span class="text-2xl font-bold text-emerald-700">{{ contadores.concluidas }}</span>
            </div>
            <div
                class="bg-white rounded-xl border p-4 shadow-xs transition-colors"
                :class="contadores.vencidas > 0 ? 'border-rose-200 bg-rose-50/30' : 'border-slate-200'"
            >
                <span
                    class="text-xs font-medium block mb-1"
                    :class="contadores.vencidas > 0 ? 'text-rose-600' : 'text-slate-500'"
                >
                    {{ t('summary.overdue') }}
                </span>
                <span
                    class="text-2xl font-bold"
                    :class="contadores.vencidas > 0 ? 'text-rose-700' : 'text-slate-900'"
                >
                    {{ contadores.vencidas }}
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div
                    role="group"
                    :aria-label="t('filters.status')"
                    class="inline-flex rounded-lg bg-slate-100 p-1 border border-slate-200 shrink-0"
                >
                    <button
                        type="button"
                        :aria-pressed="filtros.estado === 'todas'"
                        @click="aplicarFiltro('estado', 'todas')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        :class="filtros.estado === 'todas' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    >
                        {{ t('filters.all') }}
                    </button>
                    <button
                        type="button"
                        :aria-pressed="filtros.estado === 'pendente'"
                        @click="aplicarFiltro('estado', 'pendente')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        :class="filtros.estado === 'pendente' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    >
                        {{ t('tasks.status_pending') }}
                    </button>
                    <button
                        type="button"
                        :aria-pressed="filtros.estado === 'concluida'"
                        @click="aplicarFiltro('estado', 'concluida')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        :class="filtros.estado === 'concluida' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    >
                        {{ t('tasks.status_completed') }}
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div>
                        <select
                            id="filtro-prioridade"
                            :aria-label="t('filters.priority')"
                            :value="filtros.prioridade"
                            @change="aplicarFiltro('prioridade', $event.target.value)"
                            class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        >
                            <option value="todas">{{ t('filters.priority_all') }}</option>
                            <option value="baixa">{{ t('tasks.priority_low') }}</option>
                            <option value="media">{{ t('tasks.priority_medium') }}</option>
                            <option value="alta">{{ t('tasks.priority_high') }}</option>
                        </select>
                    </div>

                    <div>
                        <select
                            id="filtro-prazo"
                            :aria-label="t('filters.due')"
                            :value="filtros.prazo"
                            @change="aplicarFiltro('prazo', $event.target.value)"
                            class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        >
                            <option value="todos">{{ t('filters.due_all') }}</option>
                            <option value="hoje">{{ t('filters.due_today') }}</option>
                            <option value="semana">{{ t('filters.due_week') }}</option>
                            <option value="vencidas">{{ t('filters.due_overdue') }}</option>
                            <option value="sem_data">{{ t('filters.due_no_date') }}</option>
                        </select>
                    </div>

                    <button
                        v-if="temFiltrosAtivos"
                        type="button"
                        @click="limparFiltros"
                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium px-2 py-1.5 rounded-md hover:bg-indigo-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                    >
                        {{ t('filters.clear') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-bold text-slate-900">
                    {{ t('tasks.title') }}
                </h3>
            </div>
            <div aria-live="polite" class="flex items-center gap-2">
                <span v-if="carregando" class="text-xs text-slate-400">
                    {{ t('filters.loading') }}
                </span>
                <span
                    v-else
                    class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200"
                >
                    {{ tarefas.length }}
                </span>
            </div>
        </div>

        <div v-if="carregando && tarefas.length === 0" class="space-y-3">
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
            v-else-if="contadores.total === 0"
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

        <div
            v-else-if="tarefas.length === 0"
            class="bg-white rounded-xl border border-slate-200 p-8 sm:p-12 text-center"
        >
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            </div>
            <h4 class="text-base font-semibold text-slate-900 mb-1">
                {{ t('filters.no_results') }}
            </h4>
            <button
                type="button"
                @click="limparFiltros"
                class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
            >
                {{ t('filters.clear') }}
            </button>
        </div>

        <div v-else class="space-y-3">
            <TarefaItem
                v-for="tarefa in tarefas"
                :key="tarefa.id"
                :tarefa="tarefa"
                @detalhes="abrirDetalhes"
                @editar="abrirEditar"
                @eliminar="abrirEliminar"
            />
        </div>

        <TarefaDetalhesModal
            v-if="tarefaDetalhes"
            :tarefa="tarefaDetalhes"
            @fechar="tarefaDetalhes = null"
            @editar="abrirEditarDeDetalhes"
        />

        <TarefaEditarModal
            v-if="tarefaEditar"
            :tarefa="tarefaEditar"
            @fechar="tarefaEditar = null"
        />

        <TarefaEliminarModal
            v-if="tarefaEliminar"
            :tarefa="tarefaEliminar"
            @fechar="tarefaEliminar = null"
        />
    </section>
</template>