<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import Alert from '../components/Alert.vue';
import LockerCard from '../components/LockerCard.vue';
import StatCard from '../components/StatCard.vue';
import { getResourceCollection } from '../services/api';

type DashboardUser = { name: string; rol: string };
type DashboardAssignment = {
    id: number;
    locker_id: number;
    usuario_id: number;
    fecha_inicio: string;
    fecha_fin: string | null;
    estado: string;
    usuario?: { id: number; name: string; email: string; rol: string; estado: string };
};
type DashboardLocker = {
    id: number;
    numero: string;
    ubicacion: string;
    estado: string;
    asignacion_activa?: DashboardAssignment | null;
};
type DashboardReading = {
    id: number;
    tarjeta_rfid?: { uid: string } | null;
    usuario?: { name: string } | null;
    locker?: { numero: string } | null;
    resultado: string;
    fecha_hora: string | null;
};

const props = defineProps<{ currentUser: DashboardUser }>();
const emit = defineEmits<{ navigate: [view: string]; locker: [locker: DashboardLocker] }>();
const lockers = ref<DashboardLocker[]>([]);
const readings = ref<DashboardReading[]>([]);
const loading = ref(true);
const refreshing = ref(false);
const error = ref('');
let refreshTimer: number | undefined;

const today = computed(() => dateKey(new Date()));
const readingsToday = computed(() => readings.value.filter(
    (reading) => reading.fecha_hora && dateKey(new Date(reading.fecha_hora)) === today.value,
).length);
const stats = computed(() => [
    { label: 'Total lockers', value: lockers.value.length, icon: '⌑', tone: 'blue' },
    { label: 'Disponibles', value: lockers.value.filter((locker) => locker.estado === 'disponible').length, icon: '✓', tone: 'green' },
    { label: 'Ocupados', value: lockers.value.filter((locker) => locker.estado === 'ocupado').length, icon: '♙', tone: 'orange' },
    { label: 'Mantenimiento', value: lockers.value.filter((locker) => locker.estado === 'mantenimiento').length, icon: '⚒', tone: 'yellow' },
    { label: 'Accesos hoy', value: readingsToday.value, icon: '⌁', tone: 'violet' },
]);
const latestReadings = computed(() => [...readings.value]
    .sort((a, b) => new Date(b.fecha_hora || 0).getTime() - new Date(a.fecha_hora || 0).getTime())
    .slice(0, 5));

function dateKey(value: Date) {
    const year = value.getFullYear();
    const month = String(value.getMonth() + 1).padStart(2, '0');
    const day = String(value.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatTime(value: string | null) {
    return value ? new Date(value).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' }) : '—';
}

async function refreshDashboard() {
    if (props.currentUser.rol !== 'admin') {
        error.value = 'El resumen general de lockers y lecturas está disponible para administradores.';
        loading.value = false;
        return;
    }

    refreshing.value = true;
    error.value = '';

    try {
        const [lockerRows, readingRows] = await Promise.all([
            getResourceCollection<DashboardLocker>('lockers'),
            getResourceCollection<DashboardReading>('lecturas-rfid'),
        ]);
        lockers.value = lockerRows;
        readings.value = readingRows;
    } catch (cause) {
        error.value = cause instanceof Error ? cause.message : 'No fue posible cargar el resumen del sistema.';
    } finally {
        loading.value = false;
        refreshing.value = false;
    }
}

onMounted(() => {
    void refreshDashboard();
    refreshTimer = window.setInterval(() => void refreshDashboard(), 15000);
});

onUnmounted(() => {
    if (refreshTimer !== undefined) {
        window.clearInterval(refreshTimer);
    }
});
</script>

<template>
    <div class="welcome-row">
        <div>
            <span class="eyebrow">RESUMEN OPERATIVO</span>
            <h2>Buenos días, {{ currentUser.name.split(' ')[0] }}.</h2>
            <p>Aquí tienes una vista general del sistema de lockers.</p>
        </div>
        <div>
            <button class="outline-button" :disabled="refreshing" @click="refreshDashboard">
                {{ refreshing ? 'Actualizando...' : '↻ Actualizar' }}
            </button>
            <button class="outline-button" @click="emit('navigate', 'simulator')">◎ Abrir simulador RFID</button>
        </div>
    </div>
    <Alert v-if="error" :message="error" tone="error" />
    <div class="stats-grid">
        <StatCard v-for="stat in stats" :key="stat.label" v-bind="stat" />
    </div>
    <div class="dashboard-grid">
        <section class="panel">
            <div class="panel-heading">
                <div><span class="eyebrow">MONITOREO EN TIEMPO REAL</span><h3>Estado de lockers</h3></div>
                <button class="text-button" @click="emit('navigate', 'lockers')">Ver todos →</button>
            </div>
            <div v-if="loading" class="empty-simulation">Cargando lockers...</div>
            <div v-else-if="lockers.length === 0" class="empty-simulation">No hay lockers disponibles.</div>
            <div v-else class="locker-grid">
                <LockerCard
                    v-for="locker in lockers"
                    :key="locker.id"
                    :locker="locker"
                    :occupant="locker.asignacion_activa?.usuario?.name"
                    @select="emit('locker', $event)"
                />
            </div>
        </section>
        <section class="panel readings-panel">
            <div class="panel-heading">
                <div><span class="eyebrow">ACTIVIDAD RECIENTE</span><h3>Últimas lecturas</h3></div>
                <button class="text-button" @click="emit('navigate', 'readings')">Ver historial →</button>
            </div>
            <div v-if="loading" class="empty-simulation">Cargando lecturas...</div>
            <div v-else-if="latestReadings.length === 0" class="empty-simulation">No hay lecturas RFID registradas.</div>
            <div v-for="reading in latestReadings" :key="reading.id" class="reading-row">
                <span class="reading-status" :class="reading.resultado"></span>
                <div>
                    <strong>{{ reading.tarjeta_rfid?.uid || 'Tarjeta no identificada' }}</strong>
                    <small>{{ reading.usuario?.name || 'Usuario desconocido' }} · Locker {{ reading.locker?.numero || '—' }}</small>
                </div>
                <time>{{ formatTime(reading.fecha_hora) }}</time>
            </div>
        </section>
    </div>
</template>
