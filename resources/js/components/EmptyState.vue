<script setup lang="ts">
defineProps<{ title: string; message?: string }>();
</script>

<template><div class="empty-simulation"><span>◎</span><h3>{{ title }}</h3><p v-if="message">{{ message }}</p></div></template>
