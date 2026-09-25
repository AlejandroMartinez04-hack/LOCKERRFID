<script setup lang="ts">
import StatusBadge from '../components/StatusBadge.vue';
import type { Assignment } from '../data/mockData';
defineProps<{ assignments: Assignment[] }>();
function date(value: string | null) { return value ? new Date(value).toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' }) : 'Sin fecha de fin'; }
</script>

<template><div class="section-heading"><div><span class="eyebrow">GESTIÓN DE DATOS</span><h2>Asignaciones</h2><p>Relación entre usuarios y lockers.</p></div><span class="simulation-tag">DATOS DE PRUEBA</span></div><section class="panel data-table"><div class="table-head"><span>LOCKER</span><span>USUARIO</span><span>INICIO</span><span>ESTADO</span></div><div v-for="assignment in assignments" :key="assignment.id" class="table-row"><strong>Locker {{ assignment.locker?.numero }}</strong><span>{{ assignment.usuario?.name }}</span><span>{{ date(assignment.fecha_inicio) }}</span><StatusBadge :value="assignment.estado" /></div></section></template>
