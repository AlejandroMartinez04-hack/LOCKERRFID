<script setup lang="ts">
import StatusBadge from '../components/StatusBadge.vue';
import type { Reading } from '../data/mockData';
defineProps<{ readings: Reading[] }>();
</script>

<template><div class="section-heading"><div><span class="eyebrow">HISTORIAL</span><h2>Lecturas RFID</h2><p>Registro de accesos permitidos y denegados.</p></div><span class="simulation-tag">DATOS DE PRUEBA</span></div><section class="panel data-table"><div class="table-head readings-head"><span>FECHA / HORA</span><span>UID</span><span>USUARIO</span><span>LOCKER</span><span>RESULTADO</span></div><div v-for="reading in readings" :key="reading.fecha" class="table-row readings-row"><span>{{ reading.fecha }}</span><strong class="mono">{{ reading.uid }}</strong><span>{{ reading.usuario }}</span><span>{{ reading.locker }}</span><StatusBadge :value="reading.resultado" /></div></section></template>
