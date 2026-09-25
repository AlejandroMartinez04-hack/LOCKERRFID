<script setup lang="ts">
defineProps<{ items: string[] }>();
</script>

<template>
    <div class="hardware-grid"><div v-for="item in items" :key="item"><span class="hardware-light"></span><strong>{{ item.split(' ')[0] }}</strong><small>{{ item.substring(item.indexOf(' ') + 1) }}</small></div></div>
</template>
