<script setup lang="ts">
defineProps<{ headers: string[] }>();
</script>

<template><section class="panel data-table"><div class="table-head"><span v-for="header in headers" :key="header">{{ header }}</span></div><slot /></section></template>
