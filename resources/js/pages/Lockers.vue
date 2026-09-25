<script setup lang="ts">
import { computed, ref } from 'vue';
import StatusBadge from '../components/StatusBadge.vue';
import type { Locker } from '../data/mockData';
const emit = defineEmits<{ detail: [locker: Locker] }>();
const search = ref('');
const props = defineProps<{ lockers: Locker[] }>();
const filtered = computed(() => props.lockers.filter((locker) => `${locker.numero} ${locker.ubicacion} ${locker.estado}`.toLowerCase().includes(search.value.toLowerCase())));
</script>

<template><div class="section-heading"><div><span class="eyebrow">INVENTARIO</span><h2>Lockers</h2><p>Gestiona el estado y disponibilidad de cada compartimento.</p></div><label class="search"><span>⌕</span><input v-model="search" placeholder="Buscar locker..."></label></div><div class="locker-table panel"><div class="table-head"><span>LOCKER</span><span>UBICACIÓN</span><span>ESTADO</span><span>ASIGNACIÓN</span><span></span></div><button v-for="locker in filtered" :key="locker.id" class="table-row" @click="emit('detail', locker)"><strong>Locker {{ locker.numero }}</strong><span>{{ locker.ubicacion }}</span><StatusBadge :value="locker.estado" /><span>{{ locker.estado === 'ocupado' ? (locker.numero === '02' ? 'Carlos Mendoza' : 'María Fernández') : '—' }}</span><span class="row-arrow">→</span></button></div></template>
