<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import Alert from '../components/Alert.vue';
import Modal from '../components/Modal.vue';
import StatusBadge from '../components/StatusBadge.vue';
import {
    createResource,
    deleteResource,
    getResourceCollection,
    updateResource,
} from '../services/api';

type User = { id: number; name: string; email: string; estado: string };
type RfidCard = {
    id: number;
    uid: string;
    usuario_id: number;
    estado: 'activa' | 'inactiva';
    usuario?: User | null;
    created_at: string | null;
};
type CardForm = {
    uid: string;
    usuario_id: number | '';
    estado: RfidCard['estado'];
};

const cards = ref<RfidCard[]>([]);
const users = ref<User[]>([]);
const loading = ref(true);
const saving = ref(false);
const error = ref('');
const modalOpen = ref(false);
const editingCard = ref<RfidCard | null>(null);
const form = reactive<CardForm>({ uid: '', usuario_id: '', estado: 'activa' });

async function loadData() {
    error.value = '';

    try {
        const [cardRows, userRows] = await Promise.all([
            getResourceCollection<RfidCard>('tarjetas-rfid'),
            getResourceCollection<User>('users'),
        ]);
        cards.value = cardRows;
        users.value = userRows;
    } catch (cause) {
        error.value =
            cause instanceof Error
                ? cause.message
                : 'No fue posible cargar tarjetas y usuarios.';
    } finally {
        loading.value = false;
    }
}

function openCreateModal() {
    editingCard.value = null;
    Object.assign(form, { uid: '', usuario_id: '', estado: 'activa' });
    modalOpen.value = true;
}

function openEditModal(card: RfidCard) {
    editingCard.value = card;
    Object.assign(form, {
        uid: card.uid,
        usuario_id: card.usuario_id,
        estado: card.estado,
    });
    modalOpen.value = true;
}

async function saveCard() {
    if (form.usuario_id === '') {
        error.value = 'Selecciona el usuario asignado a la tarjeta.';
        return;
    }

    saving.value = true;
    error.value = '';
    const payload = {
        uid: form.uid,
        usuario_id: Number(form.usuario_id),
        estado: form.estado,
    };

    try {
        if (editingCard.value) {
            await updateResource<RfidCard>(
                `tarjetas-rfid/${editingCard.value.id}`,
                payload,
            );
        } else {
            await createResource<RfidCard>('tarjetas-rfid', payload);
        }
        modalOpen.value = false;
        await loadData();
    } catch (cause) {
        error.value =
            cause instanceof Error
                ? cause.message
                : 'No fue posible guardar la tarjeta.';
    } finally {
        saving.value = false;
    }
}

async function removeCard(card: RfidCard) {
    if (!window.confirm(`¿Eliminar la tarjeta ${card.uid}?`)) {
        return;
    }

    error.value = '';

    try {
        await deleteResource(`tarjetas-rfid/${card.id}`);
        await loadData();
    } catch (cause) {
        error.value =
            cause instanceof Error
                ? cause.message
                : 'No fue posible eliminar la tarjeta.';
    }
}

function formatDate(value: string | null) {
    return value ? new Date(value).toLocaleDateString('es-MX') : '—';
}

onMounted(loadData);
</script>

<template>
    <div class="section-heading">
        <div>
            <span class="eyebrow">GESTIÓN DE DATOS</span>
            <h2>Tarjetas RFID</h2>
            <p>Identificadores asociados a usuarios del sistema.</p>
        </div>
        <button
            class="primary-button !mt-0 !w-auto"
            type="button"
            @click="openCreateModal"
        >
            Nueva Tarjeta RFID
        </button>
    </div>

    <Alert v-if="error" :message="error" tone="error" />

    <section class="panel data-table">
        <div
            class="table-head"
            style="grid-template-columns: 1fr 1.3fr 1fr 0.8fr 1fr"
        >
            <span>UID</span><span>USUARIO</span><span>REGISTRO</span
            ><span>ESTADO</span><span>ACCIONES</span>
        </div>
        <div v-if="loading" class="table-row">Cargando tarjetas...</div>
        <div v-else-if="cards.length === 0" class="table-row">
            No hay tarjetas registradas.
        </div>
        <div
            v-for="card in cards"
            :key="card.id"
            class="table-row"
            style="grid-template-columns: 1fr 1.3fr 1fr 0.8fr 1fr"
        >
            <strong class="mono">{{ card.uid }}</strong>
            <span>{{ card.usuario?.name || 'Usuario no disponible' }}</span>
            <span>{{ formatDate(card.created_at) }}</span>
            <StatusBadge :value="card.estado" />
            <div class="flex flex-wrap gap-2">
                <button
                    class="text-button"
                    type="button"
                    @click="openEditModal(card)"
                >
                    Editar
                </button>
                <button
                    class="text-button"
                    type="button"
                    @click="removeCard(card)"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </section>

    <Modal
        :open="modalOpen"
        :title="editingCard ? 'Editar Tarjeta RFID' : 'Nueva Tarjeta RFID'"
        @close="modalOpen = false"
    >
        <form class="mt-5 grid gap-4" @submit.prevent="saveCard">
            <label class="grid gap-2">
                UID
                <input v-model="form.uid" required maxlength="50" />
            </label>
            <label class="grid gap-2">
                Usuario asignado
                <select v-model="form.usuario_id" required>
                    <option value="" disabled>Selecciona un usuario</option>
                    <option
                        v-for="user in users"
                        :key="user.id"
                        :value="user.id"
                    >
                        {{ user.name }} — {{ user.email }}
                    </option>
                </select>
            </label>
            <label class="grid gap-2">
                Estado
                <select v-model="form.estado" required>
                    <option value="activa">Activa</option>
                    <option value="inactiva">Inactiva</option>
                </select>
            </label>
            <Alert v-if="error" :message="error" tone="error" />
            <button
                class="primary-button !mt-0"
                type="submit"
                :disabled="saving"
            >
                {{ saving ? 'Guardando...' : 'Guardar tarjeta' }}
            </button>
        </form>
    </Modal>
</template>
