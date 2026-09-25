<script setup lang="ts">
import StatusBadge from '../components/StatusBadge.vue';
import type { Locker } from '../data/mockData';
defineProps<{ locker: Locker | null }>();
</script>

<template><div v-if="locker" class="my-locker-hero"><div class="hero-locker">{{ locker.numero }}</div><div><span class="eyebrow">TU ESPACIO ASIGNADO</span><h2>Locker {{ locker.numero }}</h2><p>{{ locker.ubicacion }} · Asignación activa</p></div><StatusBadge :value="locker.estado" /></div><div v-else class="empty-simulation"><span>□</span><h3>No tienes un locker asignado actualmente.</h3></div></template>
