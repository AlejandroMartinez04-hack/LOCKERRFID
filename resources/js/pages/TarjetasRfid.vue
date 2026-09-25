<script setup lang="ts">
import StatusBadge from '../components/StatusBadge.vue';
defineProps<{ cards: { id: number; uid: string; usuario: string; estado: string; fecha: string }[] }>();
</script>

<template><div class="section-heading"><div><span class="eyebrow">GESTIÓN DE DATOS</span><h2>Tarjetas RFID</h2><p>Identificadores asociados a usuarios del sistema.</p></div><span class="simulation-tag">DATOS DE PRUEBA</span></div><section class="panel data-table"><div class="table-head"><span>UID</span><span>USUARIO</span><span>REGISTRO</span><span>ESTADO</span></div><div v-for="card in cards" :key="card.uid" class="table-row"><strong class="mono">{{ card.uid }}</strong><span>{{ card.usuario }}</span><span>{{ card.fecha }}</span><StatusBadge :value="card.estado" /></div></section></template>
