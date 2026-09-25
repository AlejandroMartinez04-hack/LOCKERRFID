<script setup lang="ts">
defineProps<{ label: string; value: number; icon: string; tone: string }>();
</script>

<template>
    <article class="stat-card">
        <div class="stat-top"><span>{{ label }}</span><span :class="`stat-icon tone-${tone}`">{{ icon }}</span></div>
        <strong>{{ value }}</strong>
        <small><span class="up">↑</span> Estado actualizado</small>
    </article>
</template>
