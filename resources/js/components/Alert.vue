<script setup lang="ts">
defineProps<{ message: string; tone?: 'error' | 'info' }>();
</script>

<template><div :class="`alert alert-${tone || 'info'}`">{{ message }}</div></template>
