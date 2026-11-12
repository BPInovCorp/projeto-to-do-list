import { ref } from 'vue';
import axios from 'axios';

const tarefas = ref([]);
const carregando = ref(false);
const aGuardar = ref(false);
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

    function limparErros() {
        erros.value = {};
        mensagemSucesso.value = '';
    }

    return {
        tarefas,
        carregando,
        aGuardar,
        erros,
        mensagemSucesso,
        listarTarefas,
        criarTarefa,
        limparErros,
    };
}