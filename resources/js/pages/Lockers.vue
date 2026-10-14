<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import Alert from '../components/Alert.vue';
import StatusBadge from '../components/StatusBadge.vue';
import { getResourceCollection } from '../services/api';

type Locker = {
    id: number;
    numero: string;
    ubicacion: string;
    estado: string;
    asignacion_activa?: {
        id: number;
        locker_id: number;
        usuario_id: number;
        fecha_inicio: string;
        fecha_fin: string | null;
        estado: string;
        usuario?: { id: number; name: string; email: string; rol: string; estado: string };
    } | null;
};

const emit = defineEmits<{ detail: [locker: Locker] }>();
const search = ref('');
const lockers = ref<Locker[]>([]);
const loading = ref(true);
const error = ref('');
const filtered = computed(() => lockers.value.filter((locker) => `${locker.numero} ${locker.ubicacion} ${locker.estado}`.toLowerCase().includes(search.value.toLowerCase())));

async function loadLockers() {
    error.value = '';

    try {
        lockers.value = await getResourceCollection<Locker>('lockers');
    } catch (cause) {
        error.value = cause instanceof Error ? cause.message : 'No fue posible cargar los lockers.';
    } finally {
        loading.value = false;
    }
}

onMounted(loadLockers);
</script>

<template>
    <div class="section-heading">
        <div>
            <span class="eyebrow">INVENTARIO</span>
            <h2>Lockers</h2>
            <p>Gestiona el estado y disponibilidad de cada compartimento.</p>
        </div>
        <label class="search"><span>⌕</span><input v-model="search" placeholder="Buscar locker..."></label>
    </div>
    <Alert v-if="error" :message="error" tone="error" />
    <div class="locker-table panel">
        <div class="table-head">
            <span>LOCKER</span><span>UBICACIÓN</span><span>ESTADO</span><span>ASIGNACIÓN</span><span></span>
        </div>
        <div v-if="loading" class="table-row">Cargando lockers...</div>
        <div v-else-if="filtered.length === 0" class="table-row">No hay lockers para mostrar.</div>
        <button v-for="locker in filtered" :key="locker.id" class="table-row" @click="emit('detail', locker)">
            <strong>Locker {{ locker.numero }}</strong>
            <span>{{ locker.ubicacion }}</span>
            <StatusBadge :value="locker.estado" />
            <span>{{ locker.asignacion_activa?.usuario?.name || '—' }}</span>
            <span class="row-arrow">→</span>
        </button>
    </div>
</template>
