<script setup>
import { onMounted, onUnmounted, reactive, ref } from 'vue';
import { useTarefas } from '../../composables/useTarefas';
import { useI18n } from '../../i18n';

const props = defineProps({
    tarefa: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['fechar', 'atualizada']);

const { atualizarTarefa, aAtualizar, erros, limparErros } = useTarefas();
const { t } = useI18n();

const inputTituloRef = ref(null);

const form = reactive({
    titulo: props.tarefa.titulo || '',
    descricao: props.tarefa.descricao || '',
    data_vencimento: props.tarefa.data_vencimento ? props.tarefa.data_vencimento.split('T')[0] : '',
    prioridade: props.tarefa.prioridade || 'media',
    estado: props.tarefa.estado || 'pendente',
});

function handleKeydown(evento) {
    if (evento.key === 'Escape') {
        emit('fechar');
    }
}

async function submeter() {
    try {
        await atualizarTarefa(props.tarefa.id, {
            titulo: form.titulo,
            descricao: form.descricao || null,
            data_vencimento: form.data_vencimento || null,
            prioridade: form.prioridade,
            estado: form.estado,
        });

        emit('atualizada');
        emit('fechar');
    } catch (error) {
    }
}

onMounted(() => {
    limparErros();
    document.addEventListener('keydown', handleKeydown);
    if (inputTituloRef.value) {
        inputTituloRef.value.focus();
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
            aria-labelledby="modal-editar-titulo"
            class="w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden transform transition-all"
        >
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 id="modal-editar-titulo" class="text-lg font-bold text-slate-900">
                    {{ t('tasks.edit_task') }}
                </h3>

                <button
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

            <form @submit.prevent="submeter" class="p-6 space-y-4">
                <div>
                    <label for="editar-titulo" class="block text-sm font-medium text-slate-700 mb-1">
                        {{ t('tasks.input_title') }} <span class="text-rose-500">*</span>
                    </label>
                    <input
                        id="editar-titulo"
                        ref="inputTituloRef"
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
                    <label for="editar-descricao" class="block text-sm font-medium text-slate-700 mb-1">
                        {{ t('tasks.input_description') }}
                    </label>
                    <textarea
                        id="editar-descricao"
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

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label for="editar-vencimento" class="block text-sm font-medium text-slate-700 mb-1">
                            {{ t('tasks.input_due_date') }}
                        </label>
                        <input
                            id="editar-vencimento"
                            v-model="form.data_vencimento"
                            type="date"
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                            :class="{ 'border-rose-400 focus:ring-rose-500 focus:border-rose-500': erros.data_vencimento }"
                        />
                        <p v-if="erros.data_vencimento" class="mt-1 text-xs text-rose-600">
                            {{ erros.data_vencimento[0] }}
                        </p>
                    </div>

                    <div>
                        <label for="editar-prioridade" class="block text-sm font-medium text-slate-700 mb-1">
                            {{ t('tasks.input_priority') }}
                        </label>
                        <select
                            id="editar-prioridade"
                            v-model="form.prioridade"
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors bg-white"
                        >
                            <option value="baixa">{{ t('tasks.priority_low') }}</option>
                            <option value="media">{{ t('tasks.priority_medium') }}</option>
                            <option value="alta">{{ t('tasks.priority_high') }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="editar-estado" class="block text-sm font-medium text-slate-700 mb-1">
                            {{ t('tasks.status_pending') }} / {{ t('tasks.status_completed') }}
                        </label>
                        <select
                            id="editar-estado"
                            v-model="form.estado"
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors bg-white"
                        >
                            <option value="pendente">{{ t('tasks.status_pending') }}</option>
                            <option value="concluida">{{ t('tasks.status_completed') }}</option>
                        </select>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 -mx-6 -mb-6 mt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="emit('fechar')"
                        :disabled="aAtualizar"
                        class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400 disabled:opacity-50"
                    >
                        {{ t('tasks.cancel') }}
                    </button>
                    <button
                        type="submit"
                        :disabled="aAtualizar"
                        class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors shadow-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ aAtualizar ? t('tasks.updating') : t('tasks.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>