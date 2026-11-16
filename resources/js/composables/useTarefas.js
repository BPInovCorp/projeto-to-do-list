import { reactive, ref } from 'vue';
import axios from 'axios';

const tarefas = ref([]);
const carregando = ref(false);
const aGuardar = ref(false);
const aAtualizar = ref(false);
const aEliminar = ref(false);
const aAlternar = ref(new Set());
const erros = ref({});
const mensagemSucesso = ref('');

const filtros = reactive({
    estado: 'todas',
    prioridade: 'todas',
    prazo: 'todos',
});

const contadores = ref({
    total: 0,
    pendentes: 0,
    concluidas: 0,
    vencidas: 0,
});

let ultimoPedidoId = 0;

export function useTarefas() {
    async function listarTarefas() {
        const pedidoId = ++ultimoPedidoId;
        carregando.value = true;
        erros.value = {};

        try {
            const response = await axios.get('/tarefas', { params: filtros });

            if (pedidoId === ultimoPedidoId) {
                tarefas.value = response.data.tarefas || [];
                if (response.data.contadores) {
                    contadores.value = response.data.contadores;
                }
            }
        } catch (error) {
            if (error.response && error.response.data && error.response.data.errors) {
                erros.value = error.response.data.errors;
            }
        } finally {
            if (pedidoId === ultimoPedidoId) {
                carregando.value = false;
            }
        }
    }

    function aplicarFiltro(chave, valor) {
        if (filtros[chave] !== valor) {
            filtros[chave] = valor;
            listarTarefas();
        }
    }

    function limparFiltros() {
        filtros.estado = 'todas';
        filtros.prioridade = 'todas';
        filtros.prazo = 'todos';
        listarTarefas();
    }

    async function criarTarefa(dados) {
        aGuardar.value = true;
        erros.value = {};
        mensagemSucesso.value = '';

        try {
            const response = await axios.post('/tarefas', dados);
            await listarTarefas();
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
            await listarTarefas();
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
            await listarTarefas();
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
            await listarTarefas();
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
        filtros,
        contadores,
        erros,
        mensagemSucesso,
        listarTarefas,
        aplicarFiltro,
        limparFiltros,
        criarTarefa,
        alternarTarefa,
        atualizarTarefa,
        eliminarTarefa,
        limparErros,
    };
}