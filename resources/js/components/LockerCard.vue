<script setup lang="ts">
import type { Locker } from '../data/mockData';

defineProps<{ locker: Locker; occupant?: string }>();
defineEmits<{ select: [locker: Locker] }>();
</script>

<template>
    <button class="locker-tile" @click="$emit('select', locker)">
        <span class="locker-number">{{ locker.numero }}</span>
        <strong>{{ locker.estado }}</strong>
        <small>{{ occupant || (locker.estado === 'disponible' ? 'Disponible' : 'Sin asignación visible') }}</small>
    </button>
</template>
