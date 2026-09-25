<script setup lang="ts">
import Alert from '../components/Alert.vue';
defineProps<{ email: string; password: string; loading: boolean; error: string }>();
const emit = defineEmits<{ 'update:email': [value: string]; 'update:password': [value: string]; submit: [] }>();
</script>

<template>
    <main class="login-page"><section class="login-intro"><div class="brand-lockup"><span class="brand-mark">LR</span><span><strong>LOCKER</strong><b>RFID</b></span></div><div class="intro-copy"><span class="eyebrow">CONTROL DE ACCESO INTELIGENTE</span><h1>Tu espacio.<br><em>Tu control.</em></h1><p>Gestiona lockers, accesos y dispositivos RFID desde un solo lugar.</p></div><div class="intro-footer"><span class="pulse-dot"></span> Sistema operativo <span>v1.0.0</span></div></section><section class="login-panel"><div class="login-form"><span class="eyebrow">PORTAL DE ACCESO</span><h2>Bienvenido de vuelta</h2><p>Ingresa tus credenciales para continuar.</p><form @submit.prevent="emit('submit')"><label>Correo electrónico<input :value="email" type="email" placeholder="nombre@ejemplo.com" required @input="emit('update:email', ($event.target as HTMLInputElement).value)"></label><label>Contraseña<input :value="password" type="password" placeholder="••••••••" required @input="emit('update:password', ($event.target as HTMLInputElement).value)"></label><Alert v-if="error" :message="error" tone="error" /><button class="primary-button" :disabled="loading">{{ loading ? 'Verificando...' : 'Iniciar sesión' }} <span>→</span></button></form><small>La autorización se valida de forma segura en Laravel.</small></div><div class="login-note"><span class="signal-icon">◉</span><span><strong>Acceso protegido</strong><br>Conexión cifrada con la API</span></div></section></main>
</template>
