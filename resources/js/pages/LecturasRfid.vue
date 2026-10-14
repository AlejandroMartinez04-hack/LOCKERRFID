<script setup lang="ts">
import { onMounted, ref } from 'vue';
import Alert from '../components/Alert.vue';
import StatusBadge from '../components/StatusBadge.vue';
import { getResourceCollection } from '../services/api';

type RfidReading = {
    id: number;
    tarjeta_rfid?: { uid: string } | null;
    usuario?: { name: string } | null;
    locker?: { numero: string } | null;
    dispositivo?: { nombre: string } | null;
    tipo_acceso: string;
    resultado: string;
    motivo: string;
    fecha_hora: string | null;
};

const readings = ref<RfidReading[]>([]);
const loading = ref(true);
const refreshing = ref(false);
const error = ref('');

async function refreshReadings() {
    refreshing.value = true;
    error.value = '';

    try {
        readings.value = await getResourceCollection<RfidReading>('lecturas-rfid');
    } catch (cause) {
        error.value = cause instanceof Error ? cause.message : 'No fue posible cargar las lecturas RFID.';
    } finally {
        loading.value = false;
        refreshing.value = false;
    }
}

function formatDate(value: string | null) {
    return value ? new Date(value).toLocaleString('es-MX') : '—';
}

onMounted(refreshReadings);
</script>

<template>
    <div class="section-heading">
        <div>
            <span class="eyebrow">HISTORIAL</span>
            <h2>Lecturas RFID</h2>
            <p>Registro de accesos permitidos y denegados.</p>
        </div>
        <button class="outline-button" :disabled="refreshing" @click="refreshReadings">
            {{ refreshing ? 'Actualizando...' : '↻ Actualizar' }}
        </button>
    </div>
    <Alert v-if="error" :message="error" tone="error" />
    <section class="panel data-table">
        <div class="table-head readings-head" style="grid-template-columns: 1.1fr 1fr 1.2fr 1fr 1.2fr 1fr 1fr">
            <span>FECHA / HORA</span>
            <span>UID</span>
            <span>USUARIO</span>
            <span>LOCKER</span>
            <span>DISPOSITIVO</span>
            <span>TIPO DE ACCESO</span>
            <span>RESULTADO</span>
        </div>
        <div v-if="loading" class="table-row">Cargando lecturas...</div>
        <div v-else-if="readings.length === 0" class="table-row">No hay lecturas RFID registradas.</div>
        <div v-for="reading in readings" :key="reading.id" class="table-row readings-row" style="grid-template-columns: 1.1fr 1fr 1.2fr 1fr 1.2fr 1fr 1fr">
            <span>{{ formatDate(reading.fecha_hora) }}</span>
            <strong class="mono">{{ reading.tarjeta_rfid?.uid || 'No identificada' }}</strong>
            <span>{{ reading.usuario?.name || '—' }}</span>
            <span>{{ reading.locker?.numero || '—' }}</span>
            <span>{{ reading.dispositivo?.nombre || '—' }}</span>
            <span>{{ reading.tipo_acceso }}</span>
            <StatusBadge :value="reading.resultado" />
        </div>
    </section>
</template>
