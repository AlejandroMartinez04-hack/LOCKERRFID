<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import Alert from '../components/Alert.vue';
import Modal from '../components/Modal.vue';
import StatusBadge from '../components/StatusBadge.vue';
import {
    createResource,
    getResourceCollection,
    updateResource,
} from '../services/api';

type User = {
    id: number;
    name: string;
    email: string;
    rol: 'admin' | 'usuario';
    estado: 'activo' | 'inactivo';
};

type UserForm = {
    name: string;
    email: string;
    password: string;
    rol: User['rol'];
    estado: User['estado'];
};

const users = ref<User[]>([]);
const loading = ref(true);
const saving = ref(false);
const error = ref('');
const modalOpen = ref(false);
const editingUser = ref<User | null>(null);
const form = reactive<UserForm>(emptyForm());

function emptyForm(): UserForm {
    return {
        name: '',
        email: '',
        password: '',
        rol: 'usuario',
        estado: 'activo',
    };
}

async function loadUsers() {
    error.value = '';

    try {
        users.value = await getResourceCollection<User>('users');
    } catch (cause) {
        error.value =
            cause instanceof Error
                ? cause.message
                : 'No fue posible cargar los usuarios.';
    } finally {
        loading.value = false;
    }
}

function openCreateModal() {
    editingUser.value = null;
    Object.assign(form, emptyForm());
    modalOpen.value = true;
}

function openEditModal(user: User) {
    editingUser.value = user;
    Object.assign(form, { ...user, password: '' });
    modalOpen.value = true;
}

async function saveUser() {
    saving.value = true;
    error.value = '';
    const payload = {
        name: form.name,
        email: form.email,
        rol: form.rol,
        estado: form.estado,
        ...(form.password ? { password: form.password } : {}),
    };

    try {
        if (editingUser.value) {
            await updateResource<User>(
                `users/${editingUser.value.id}`,
                payload,
            );
        } else {
            await createResource<User>('users', {
                ...payload,
                password: form.password,
            });
        }

        modalOpen.value = false;
        await loadUsers();
    } catch (cause) {
        error.value =
            cause instanceof Error
                ? cause.message
                : 'No fue posible guardar el usuario.';
    } finally {
        saving.value = false;
    }
}

async function deactivateUser(user: User) {
    if (user.estado === 'inactivo') {
        return;
    }

    if (!window.confirm(`¿Desactivar al usuario ${user.name}?`)) {
        return;
    }

    error.value = '';

    try {
        await updateResource<User>(`users/${user.id}`, { estado: 'inactivo' });
        await loadUsers();
    } catch (cause) {
        error.value =
            cause instanceof Error
                ? cause.message
                : 'No fue posible desactivar el usuario.';
    }
}

onMounted(loadUsers);
</script>

<template>
    <div class="section-heading">
        <div>
            <span class="eyebrow">GESTIÓN ADMINISTRATIVA</span>
            <h2>Usuarios</h2>
            <p>Usuarios y roles registrados en el sistema.</p>
        </div>
        <button
            class="primary-button !mt-0 !w-auto"
            type="button"
            @click="openCreateModal"
        >
            Nuevo Usuario
        </button>
    </div>

    <Alert v-if="error" :message="error" tone="error" />

    <section class="panel data-table">
        <div
            class="table-head"
            style="grid-template-columns: 1.2fr 1.4fr 0.7fr 0.7fr 1fr"
        >
            <span>USUARIO</span><span>CORREO</span><span>ROL</span
            ><span>ESTADO</span><span>ACCIONES</span>
        </div>
        <div v-if="loading" class="table-row">Cargando usuarios...</div>
        <div v-else-if="users.length === 0" class="table-row">
            No hay usuarios registrados.
        </div>
        <div
            v-for="user in users"
            :key="user.id"
            class="table-row"
            style="grid-template-columns: 1.2fr 1.4fr 0.7fr 0.7fr 1fr"
        >
            <strong>{{ user.name }}</strong>
            <span>{{ user.email }}</span>
            <span>{{ user.rol }}</span>
            <StatusBadge :value="user.estado" />
            <div class="flex flex-wrap gap-2">
                <button
                    class="text-button"
                    type="button"
                    @click="openEditModal(user)"
                >
                    Editar
                </button>
                <button
                    v-if="user.estado === 'activo'"
                    class="text-button"
                    type="button"
                    @click="deactivateUser(user)"
                >
                    Desactivar
                </button>
            </div>
        </div>
    </section>

    <Modal
        :open="modalOpen"
        :title="editingUser ? 'Editar Usuario' : 'Nuevo Usuario'"
        @close="modalOpen = false"
    >
        <form class="mt-5 grid gap-4" @submit.prevent="saveUser">
            <label class="grid gap-2">
                Nombre
                <input v-model="form.name" required maxlength="255" />
            </label>
            <label class="grid gap-2">
                Correo electrónico
                <input
                    v-model="form.email"
                    type="email"
                    required
                    maxlength="255"
                />
            </label>
            <label class="grid gap-2">
                {{ editingUser ? 'Nueva contraseña (opcional)' : 'Contraseña' }}
                <input
                    v-model="form.password"
                    type="password"
                    :required="!editingUser"
                    minlength="8"
                    autocomplete="new-password"
                />
            </label>
            <label class="grid gap-2">
                Rol
                <select v-model="form.rol" required>
                    <option value="usuario">Usuario</option>
                    <option value="admin">Administrador</option>
                </select>
            </label>
            <label class="grid gap-2">
                Estado
                <select v-model="form.estado" required>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </label>
            <Alert v-if="error" :message="error" tone="error" />
            <button
                class="primary-button !mt-0"
                type="submit"
                :disabled="saving"
            >
                {{ saving ? 'Guardando...' : 'Guardar usuario' }}
            </button>
        </form>
    </Modal>
</template>
