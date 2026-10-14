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

type User = { id: number; name: string; email: string };
type Locker = { id: number; numero: string; ubicacion: string; estado: string };
type Assignment = {
    id: number;
    locker_id: number;
    usuario_id: number;
    fecha_inicio: string;
    fecha_fin: string | null;
    estado: 'activa' | 'finalizada' | 'cancelada';
    locker?: Locker | null;
    usuario?: User | null;
};
type AssignmentForm = {
    locker_id: number | '';
    usuario_id: number | '';
    fecha_inicio: string;
    estado: Assignment['estado'];
};

const assignments = ref<Assignment[]>([]);
const lockers = ref<Locker[]>([]);
const users = ref<User[]>([]);
const loading = ref(true);
const saving = ref(false);
const error = ref('');
const modalOpen = ref(false);
const editingAssignment = ref<Assignment | null>(null);
const form = reactive<AssignmentForm>({
    locker_id: '',
    usuario_id: '',
    fecha_inicio: '',
    estado: 'activa',
});

async function loadData() {
    error.value = '';

    try {
        const [assignmentRows, lockerRows, userRows] = await Promise.all([
            getResourceCollection<Assignment>('asignaciones-lockers'),
            getResourceCollection<Locker>('lockers'),
            getResourceCollection<User>('users'),
        ]);
        assignments.value = assignmentRows;
        lockers.value = lockerRows;
        users.value = userRows;
    } catch (cause) {
        error.value =
            cause instanceof Error
                ? cause.message
                : 'No fue posible cargar los datos de asignaciones.';
    } finally {
        loading.value = false;
    }
}

function localDateTime(value: string) {
    const date = new Date(value);
    const local = new Date(date.getTime() - date.getTimezoneOffset() * 60_000);
    return local.toISOString().slice(0, 16);
}

function openCreateModal() {
    editingAssignment.value = null;
    Object.assign(form, {
        locker_id: '',
        usuario_id: '',
        fecha_inicio: localDateTime(new Date().toISOString()),
        estado: 'activa',
    });
    modalOpen.value = true;
}

function openEditModal(assignment: Assignment) {
    editingAssignment.value = assignment;
    Object.assign(form, {
        locker_id: assignment.locker_id,
        usuario_id: assignment.usuario_id,
        fecha_inicio: localDateTime(assignment.fecha_inicio),
        estado: assignment.estado,
    });
    modalOpen.value = true;
}

async function saveAssignment() {
    if (form.locker_id === '' || form.usuario_id === '') {
        error.value = 'Selecciona un locker y un usuario.';
        return;
    }

    saving.value = true;
    error.value = '';
    const payload = {
        locker_id: Number(form.locker_id),
        usuario_id: Number(form.usuario_id),
        fecha_inicio: form.fecha_inicio,
    };

    try {
        if (editingAssignment.value) {
            await updateResource<Assignment>(
                `asignaciones-lockers/${editingAssignment.value.id}`,
                { ...payload, estado: form.estado },
            );
        } else {
            await createResource<Assignment>('asignaciones-lockers', payload);
        }
        modalOpen.value = false;
        await loadData();
    } catch (cause) {
        error.value =
            cause instanceof Error
                ? cause.message
                : 'No fue posible guardar la asignación.';
    } finally {
        saving.value = false;
    }
}

async function removeAssignment(assignment: Assignment) {
    if (
        !window.confirm(
            `¿Eliminar la asignación del locker ${assignment.locker?.numero || ''}?`,
        )
    ) {
        return;
    }

    error.value = '';

    try {
        await deleteResource(`asignaciones-lockers/${assignment.id}`);
        await loadData();
    } catch (cause) {
        error.value =
            cause instanceof Error
                ? cause.message
                : 'No fue posible eliminar la asignación.';
    }
}

function date(value: string | null) {
    return value
        ? new Date(value).toLocaleDateString('es-MX', {
              day: '2-digit',
              month: 'short',
              year: 'numeric',
          })
        : 'Sin fecha de fin';
}

onMounted(loadData);
</script>

<template>
    <div class="section-heading">
        <div>
            <span class="eyebrow">GESTIÓN DE DATOS</span>
            <h2>Asignaciones</h2>
            <p>Relación entre usuarios y lockers.</p>
        </div>
        <button
            class="primary-button !mt-0 !w-auto"
            type="button"
            @click="openCreateModal"
        >
            Nueva Asignación
        </button>
    </div>

    <Alert v-if="error" :message="error" tone="error" />

    <section class="panel data-table">
        <div
            class="table-head"
            style="grid-template-columns: 1fr 1.2fr 1fr 0.8fr 1fr"
        >
            <span>LOCKER</span><span>USUARIO</span><span>INICIO</span
            ><span>ESTADO</span><span>ACCIONES</span>
        </div>
        <div v-if="loading" class="table-row">Cargando asignaciones...</div>
        <div v-else-if="assignments.length === 0" class="table-row">
            No hay asignaciones registradas.
        </div>
        <div
            v-for="assignment in assignments"
            :key="assignment.id"
            class="table-row"
            style="grid-template-columns: 1fr 1.2fr 1fr 0.8fr 1fr"
        >
            <strong
                >Locker
                {{ assignment.locker?.numero || assignment.locker_id }}</strong
            >
            <span>{{ assignment.usuario?.name || assignment.usuario_id }}</span>
            <span>{{ date(assignment.fecha_inicio) }}</span>
            <StatusBadge :value="assignment.estado" />
            <div class="flex flex-wrap gap-2">
                <button
                    class="text-button"
                    type="button"
                    @click="openEditModal(assignment)"
                >
                    Editar
                </button>
                <button
                    class="text-button"
                    type="button"
                    @click="removeAssignment(assignment)"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </section>

    <Modal
        :open="modalOpen"
        :title="editingAssignment ? 'Editar Asignación' : 'Nueva Asignación'"
        @close="modalOpen = false"
    >
        <form class="mt-5 grid gap-4" @submit.prevent="saveAssignment">
            <label class="grid gap-2">
                Locker
                <select v-model="form.locker_id" required>
                    <option value="" disabled>Selecciona un locker</option>
                    <option
                        v-for="locker in lockers"
                        :key="locker.id"
                        :value="locker.id"
                        :disabled="
                            locker.estado !== 'disponible' &&
                            locker.id !== editingAssignment?.locker_id
                        "
                    >
                        {{ locker.numero }} — {{ locker.ubicacion }} ({{
                            locker.estado
                        }})
                    </option>
                </select>
            </label>
            <label class="grid gap-2">
                Usuario
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
                Fecha de inicio
                <input
                    v-model="form.fecha_inicio"
                    type="datetime-local"
                    required
                />
            </label>
            <label v-if="editingAssignment" class="grid gap-2">
                Estado
                <select v-model="form.estado" required>
                    <option value="activa">Activa</option>
                    <option value="finalizada">Finalizada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </label>
            <p v-if="!editingAssignment" class="text-sm text-slate-500">
                Al crear, la asignación inicia activa y el backend marca el
                locker como ocupado.
            </p>
            <Alert v-if="error" :message="error" tone="error" />
            <button
                class="primary-button !mt-0"
                type="submit"
                :disabled="saving"
            >
                {{ saving ? 'Guardando...' : 'Guardar asignación' }}
            </button>
        </form>
    </Modal>
</template>
