import { ref } from 'vue';
import axios from 'axios';

const tarefas = ref([]);
const carregando = ref(false);
const aGuardar = ref(false);
const aAtualizar = ref(false);
const aEliminar = ref(false);
const aAlternar = ref(new Set());
const erros = ref({});
const mensagemSucesso = ref('');

export function useTarefas() {
    async function listarTarefas() {
        carregando.value = true;
        erros.value = {};

        try {
            const response = await axios.get('/tarefas');
            tarefas.value = response.data.tarefas || [];
        } catch (error) {
            if (error.response && error.response.data && error.response.data.errors) {
                erros.value = error.response.data.errors;
            }
        } finally {
            carregando.value = false;
        }
    }

    async function criarTarefa(dados) {
        aGuardar.value = true;
        erros.value = {};
        mensagemSucesso.value = '';

        try {
            const response = await axios.post('/tarefas', dados);
            tarefas.value.unshift(response.data.tarefa);
            mensagemSucesso.value = response.data.status;
            return response.data;
        } catch (error) {
            if (error.response && error.response.status === 422) {
                erros.value = error.response.data.errors || {};
            }
            throw error;
        } finally {
            aGuardar.value = false;
        }
    }

    async function alternarTarefa(tarefa) {
        if (aAlternar.value.has(tarefa.id)) {
            return;
        }

        aAlternar.value.add(tarefa.id);
        erros.value = {};

        try {
            const response = await axios.patch(`/tarefas/${tarefa.id}/alternar`);
            const index = tarefas.value.findIndex(t => t.id === tarefa.id);
            if (index !== -1) {
                tarefas.value[index] = response.data.tarefa;
            }
            return response.data;
        } catch (error) {
            if (error.response && error.response.data && error.response.data.errors) {
                erros.value = error.response.data.errors;
            }
            throw error;
        } finally {
            aAlternar.value.delete(tarefa.id);
        }
    }

    async function atualizarTarefa(id, dados) {
        if (aAtualizar.value) {
            return;
        }

        aAtualizar.value = true;
        erros.value = {};
        mensagemSucesso.value = '';

        try {
            const response = await axios.put(`/tarefas/${id}`, dados);
            const index = tarefas.value.findIndex(t => t.id === id);
            if (index !== -1) {
                tarefas.value[index] = response.data.tarefa;
            }
            mensagemSucesso.value = response.data.status;
            return response.data;
        } catch (error) {
            if (error.response && error.response.status === 422) {
                erros.value = error.response.data.errors || {};
            }
            throw error;
        } finally {
            aAtualizar.value = false;
        }
    }

    async function eliminarTarefa(id) {
        if (aEliminar.value) {
            return;
        }

        aEliminar.value = true;
        erros.value = {};
        mensagemSucesso.value = '';

        try {
            const response = await axios.delete(`/tarefas/${id}`);
            tarefas.value = tarefas.value.filter(t => t.id !== id);
            mensagemSucesso.value = response.data.status;
            return response.data;
        } catch (error) {
            if (error.response && error.response.data && error.response.data.errors) {
                erros.value = error.response.data.errors;
            }
            throw error;
        } finally {
            aEliminar.value = false;
        }
    }

    function limparErros() {
        erros.value = {};
        mensagemSucesso.value = '';
    }

    return {
        tarefas,
        carregando,
        aGuardar,
        aAtualizar,
        aEliminar,
        aAlternar,
        erros,
        mensagemSucesso,
        listarTarefas,
        criarTarefa,
        alternarTarefa,
        atualizarTarefa,
        eliminarTarefa,
        limparErros,
    };
}