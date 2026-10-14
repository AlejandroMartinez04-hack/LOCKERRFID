<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import AppLayout from '../components/AppLayout.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import Login from './Login.vue';
import Dashboard from './Dashboard.vue';
import Lockers from './Lockers.vue';
import LockerDetail from './LockerDetail.vue';
import MiLocker from './MiLocker.vue';
import Asignaciones from './Asignaciones.vue';
import TarjetasRfid from './TarjetasRfid.vue';
import Dispositivos from './Dispositivos.vue';
import LecturasRfid from './LecturasRfid.vue';
import SimuladorRfid from './SimuladorRfid.vue';
import Usuarios from './Usuarios.vue';
import {
    cards,
    devices,
    hardware,
    type Locker,
    type User,
} from '../data/mockData';
import {
    auth,
    login as apiLogin,
    logout as apiLogout,
    me,
    mockEnabled,
} from '../services/api';

const currentView = ref('dashboard');
const loading = ref(true);
const loginLoading = ref(false);
const loginError = ref('');
const email = ref('admin@silocker.local');
const password = ref('');
const currentUser = ref<User | null>(null);
const selectedLocker = ref<Locker | null>(null);
const navItems = [
    { id: 'dashboard', label: 'Dashboard', icon: 'grid' },
    { id: 'my-locker', label: 'Mi locker', icon: 'box' },
    { id: 'lockers', label: 'Lockers', icon: 'lock' },
    { id: 'assignments', label: 'Asignaciones', icon: 'link', admin: true },
    { id: 'cards', label: 'Tarjetas RFID', icon: 'card', admin: true },
    { id: 'devices', label: 'Dispositivos', icon: 'cpu', admin: true },
    { id: 'readings', label: 'Lecturas RFID', icon: 'wave', admin: true },
    { id: 'simulator', label: 'Simulador RFID', icon: 'scan' },
    { id: 'users', label: 'Usuarios', icon: 'users', admin: true },
];
const pageComponent = computed(
    () =>
        ({
            dashboard: Dashboard,
            lockers: Lockers,
            'locker-detail': LockerDetail,
            'my-locker': MiLocker,
            assignments: Asignaciones,
            cards: TarjetasRfid,
            devices: Dispositivos,
            readings: LecturasRfid,
            simulator: SimuladorRfid,
            users: Usuarios,
        })[currentView.value] || Dashboard,
);
const pageProps = computed<Record<string, unknown>>(() => {
    switch (currentView.value) {
        case 'dashboard':
            return { currentUser: currentUser.value };
        case 'locker-detail':
            return { locker: selectedLocker.value, hardware };
        case 'devices':
            return { devices, hardware };
        case 'simulator':
            return { cards };
        default:
            return {};
    }
});
function navigate(view: string) {
    currentView.value = view;
}
function openLocker(locker: Locker) {
    selectedLocker.value = locker;
    navigate('locker-detail');
}
async function submitLogin() {
    loginError.value = '';
    loginLoading.value = true;
    try {
        currentUser.value = (await apiLogin(
            email.value,
            password.value,
        )) as User;
        navigate('dashboard');
    } catch (error) {
        loginError.value =
            error instanceof Error
                ? error.message
                : 'No fue posible iniciar sesión';
    } finally {
        loginLoading.value = false;
    }
}
async function signOut() {
    await apiLogout().catch(() => undefined);
    currentUser.value = null;
    navigate('dashboard');
}
onMounted(async () => {
    if (auth.token()) {
        try {
            currentUser.value = (await me()) as User;
        } catch {
            auth.clear();
        }
    }
    loading.value = false;
});
</script>

<template>
    <Head title="LOCKER RFID" />
    <LoadingSpinner v-if="loading" />
    <Login
        v-else-if="!currentUser"
        v-model:email="email"
        v-model:password="password"
        :loading="loginLoading"
        :error="loginError"
        @submit="submitLogin"
    />
    <AppLayout
        v-else
        :current-user="currentUser"
        :current-view="currentView"
        :nav-items="navItems"
        :mock-enabled="mockEnabled"
        @navigate="navigate"
        @logout="signOut"
    >
        <component
            :is="pageComponent"
            v-bind="pageProps"
            @navigate="navigate"
            @locker="openLocker"
            @detail="openLocker"
            @back="navigate('lockers')"
        />
    </AppLayout>
</template>
