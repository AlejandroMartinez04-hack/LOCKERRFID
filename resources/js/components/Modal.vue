<script setup lang="ts">
defineProps<{ open: boolean; title?: string }>();
defineEmits<{ close: [] }>();
</script>

<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4"
        @click.self="$emit('close')"
    >
        <section class="panel max-h-[90vh] w-full max-w-xl overflow-y-auto p-6">
            <div class="panel-heading">
                <h3>{{ title }}</h3>
                <button
                    class="text-button"
                    type="button"
                    @click="$emit('close')"
                >
                    Cerrar
                </button>
            </div>
            <slot />
        </section>
    </div>
</template>
