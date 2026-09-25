<script setup lang="ts">
import HardwareStatus from '../components/HardwareStatus.vue';
import StatusBadge from '../components/StatusBadge.vue';
import type { Locker } from '../data/mockData';
defineProps<{ locker: Locker | null; hardware: string[] }>();
defineEmits<{ back: [] }>();
</script>

<template><button class="back-button" @click="$emit('back')">← Volver a lockers</button><div class="detail-grid"><section class="detail-hero panel"><span class="eyebrow">DETALLE DEL RECURSO</span><div class="big-locker">{{ locker?.numero || '02' }}</div><h2>Locker {{ locker?.numero || '02' }}</h2><p>{{ locker?.ubicacion || 'Bloque A - Planta 1' }}</p><StatusBadge :value="locker?.estado || 'ocupado'" /></section><section class="panel info-panel"><div class="panel-heading"><div><span class="eyebrow">HARDWARE VIRTUAL</span><h3>Estado del dispositivo</h3></div><span class="simulation-tag">SIMULACIÓN</span></div><HardwareStatus :items="hardware" /></section></div></template>
