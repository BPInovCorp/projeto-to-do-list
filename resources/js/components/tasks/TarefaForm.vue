<script setup>
import { reactive, ref } from 'vue';
import { useTarefas } from '../../composables/useTarefas';
import { useI18n } from '../../i18n';

const emit = defineEmits(['criada', 'cancelar']);

const { criarTarefa, aGuardar, erros, limparErros } = useTarefas();
const { t } = useI18n();

const formAberto = ref(false);

const form = reactive({
    titulo: '',
    descricao: '',
    data_vencimento: '',
    prioridade: 'media',
});

function abrirFormulario() {
    formAberto.value = true;
    limparErros();
}

function fecharFormulario() {
    formAberto.value = false;
    form.titulo = '';
    form.descricao = '';
    form.data_vencimento = '';
    form.prioridade = 'media';
    limparErros();
    emit('cancelar');
}

async function submeter() {
    try {
        await criarTarefa({
            titulo: form.titulo,
            descricao: form.descricao || null,
            data_vencimento: form.data_vencimento || null,
            prioridade: form.prioridade,
        });

        fecharFormulario();
        emit('criada');
    } catch (error) {
    }
}
</script>

<template>
    <div class="mb-8">
        <div v-if="!formAberto" class="flex justify-end">
            <button
                type="button"
                @click="abrirFormulario"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition-colors shadow-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>{{ t('tasks.new_task') }}</span>
            </button>
        </div>

        <div
            v-else
            class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-xs transition-all"
        >
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">
                    {{ t('tasks.new_task') }}
                </h3>
                <button
                    type="button"
                    @click="fecharFormulario"
                    class="text-slate-400 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-400 rounded-md p-1"
                    :aria-label="t('tasks.cancel')"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submeter" class="space-y-5">
                <div>
                    <label for="tarefa-titulo" class="block text-sm font-medium text-slate-700 mb-1">
                        {{ t('tasks.input_title') }} <span class="text-rose-500">*</span>
                    </label>
                    <input
                        id="tarefa-titulo"
                        v-model="form.titulo"
                        type="text"
                        required
                        :placeholder="t('tasks.input_title_placeholder')"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                        :class="{ 'border-rose-400 focus:ring-rose-500 focus:border-rose-500': erros.titulo }"
                    />
                    <p v-if="erros.titulo" class="mt-1 text-xs text-rose-600">
                        {{ erros.titulo[0] }}
                    </p>
                </div>

                <div>
                    <label for="tarefa-descricao" class="block text-sm font-medium text-slate-700 mb-1">
                        {{ t('tasks.input_description') }}
                    </label>
                    <textarea
                        id="tarefa-descricao"
                        v-model="form.descricao"
                        rows="3"
                        :placeholder="t('tasks.input_description_placeholder')"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors resize-y"
                        :class="{ 'border-rose-400 focus:ring-rose-500 focus:border-rose-500': erros.descricao }"
                    ></textarea>
                    <p v-if="erros.descricao" class="mt-1 text-xs text-rose-600">
                        {{ erros.descricao[0] }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tarefa-vencimento" class="block text-sm font-medium text-slate-700 mb-1">
                            {{ t('tasks.input_due_date') }}
                        </label>
                        <input
                            id="tarefa-vencimento"
                            v-model="form.data_vencimento"
                            type="date"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                            :class="{ 'border-rose-400 focus:ring-rose-500 focus:border-rose-500': erros.data_vencimento }"
                        />
                        <p v-if="erros.data_vencimento" class="mt-1 text-xs text-rose-600">
                            {{ erros.data_vencimento[0] }}
                        </p>
                    </div>

                    <div>
                        <label for="tarefa-prioridade" class="block text-sm font-medium text-slate-700 mb-1">
                            {{ t('tasks.input_priority') }} <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="tarefa-prioridade"
                            v-model="form.prioridade"
                            required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors bg-white"
                            :class="{ 'border-rose-400 focus:ring-rose-500 focus:border-rose-500': erros.prioridade }"
                        >
                            <option value="baixa">{{ t('tasks.priority_low') }}</option>
                            <option value="media">{{ t('tasks.priority_medium') }}</option>
                            <option value="alta">{{ t('tasks.priority_high') }}</option>
                        </select>
                        <p v-if="erros.prioridade" class="mt-1 text-xs text-rose-600">
                            {{ erros.prioridade[0] }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        @click="fecharFormulario"
                        :disabled="aGuardar"
                        class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400 disabled:opacity-50"
                    >
                        {{ t('tasks.cancel') }}
                    </button>
                    <button
                        type="submit"
                        :disabled="aGuardar"
                        class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-xs"
                    >
                        {{ aGuardar ? t('tasks.submitting') : t('tasks.create_task') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>