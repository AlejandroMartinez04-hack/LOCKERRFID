<script setup lang="ts">
defineProps<{ open: boolean; title?: string }>();
defineEmits<{ close: [] }>();
</script>

<template><div v-if="open" class="modal-backdrop" @click.self="$emit('close')"><section class="panel modal"><div class="panel-heading"><h3>{{ title }}</h3><button class="text-button" @click="$emit('close')">Cerrar</button></div><slot /></section></div></template>
