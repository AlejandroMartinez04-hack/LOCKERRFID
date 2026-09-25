<script setup lang="ts">
import StatusBadge from '../components/StatusBadge.vue';
import type { Device } from '../data/mockData';
defineProps<{ devices: Device[]; hardware: string[] }>();
</script>

<template><div class="section-heading"><div><span class="eyebrow">GESTIÓN DE DATOS</span><h2>Dispositivos</h2><p>Estado de los ESP32 registrados en la instalación.</p></div><span class="simulation-tag">DATOS DE PRUEBA</span></div><section class="panel data-table"><div class="device-grid"><article v-for="device in devices" :key="device.id" class="device-card"><div class="device-top"><span class="device-icon">▦</span><StatusBadge :value="device.estado" /></div><h3>{{ device.nombre }}</h3><p>{{ device.ubicacion }} · {{ device.ip }}</p><div class="device-meta"><span v-for="item in hardware.slice(0, 4)" :key="item">● {{ item.split(' ')[0] }}</span></div><small>Última comunicación: {{ device.comunicacion }}</small></article></div></section></template>
