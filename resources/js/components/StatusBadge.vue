<script setup lang="ts">
defineProps<{ value: string }>();
</script>

<template>
    <span :class="`status status-${value}`">{{ value }}</span>
</template>
