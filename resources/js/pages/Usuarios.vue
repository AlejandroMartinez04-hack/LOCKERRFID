<script setup lang="ts">
import StatusBadge from '../components/StatusBadge.vue';
import type { User } from '../data/mockData';
defineProps<{ users: User[] }>();
</script>

<template><div class="section-heading"><div><span class="eyebrow">GESTIÓN ADMINISTRATIVA</span><h2>Usuarios</h2><p>Usuarios y roles registrados en el sistema.</p></div><span class="simulation-tag">DATOS DE PRUEBA</span></div><section class="panel data-table"><div class="table-head"><span>USUARIO</span><span>CORREO</span><span>ROL</span><span>ESTADO</span></div><div v-for="user in users" :key="user.id" class="table-row"><strong>{{ user.name }}</strong><span>{{ user.email }}</span><span>{{ user.rol }}</span><StatusBadge :value="user.estado" /></div></section></template>
