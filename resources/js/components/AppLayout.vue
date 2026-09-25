<script setup lang="ts">
import { computed, ref } from 'vue';
import type { User } from '../data/mockData';

type NavItem = { id: string; label: string; icon: string; admin?: boolean };
const props = defineProps<{ currentUser: User; currentView: string; navItems: NavItem[]; mockEnabled: boolean }>();
const emit = defineEmits<{ navigate: [view: string]; logout: [] }>();
const mobileMenu = ref(false);
const visibleNav = computed(() => props.navItems.filter((item) => !item.admin || props.currentUser.rol === 'admin'));
function go(view: string) { emit('navigate', view); mobileMenu.value = false; }
function iconFor(icon: string) { return ({ grid: '⊞', lock: '⌑', box: '□', users: '♙', wave: '⌁', scan: '◎', cpu: '▦', card: '▭', link: '↗' }[icon] || '◌'); }
function displayName(name: string) { return name; }
</script>

<template>
    <div class="app-shell">
        <aside class="sidebar" :class="{ open: mobileMenu }">
            <div class="brand-lockup side-brand"><span class="brand-mark">LR</span><span><strong>LOCKER</strong><b>RFID</b></span></div>
            <p class="side-caption">CONTROL CENTER</p>
            <nav><button v-for="item in visibleNav" :key="item.id" :class="{ active: currentView === item.id }" @click="go(item.id)"><span class="nav-icon">{{ iconFor(item.icon) }}</span>{{ item.label }}<span v-if="item.id === 'simulator'" class="new-tag">LIVE</span></button></nav>
            <div class="sidebar-bottom"><div class="connection"><span class="pulse-dot"></span><div><strong>API conectada</strong><small>Laravel / Sanctum</small></div></div><button class="logout-button" @click="emit('logout')">↪ <span>Cerrar sesión</span></button></div>
        </aside>
        <section class="main-area"><header class="topbar"><button class="menu-button" @click="mobileMenu = !mobileMenu">☰</button><div><span class="top-kicker">ESPACIO DE TRABAJO</span><h1>{{ navItems.find((item) => item.id === currentView)?.label }}</h1></div><div class="top-actions"><span class="api-pill"><i></i> Sistema en línea</span><div class="user-chip"><span class="avatar">{{ currentUser.name.charAt(0) }}</span><span><strong>{{ displayName(currentUser.name) }}</strong><small>{{ currentUser.rol === 'admin' ? 'Administrador' : 'Usuario' }}</small></span></div></div></header>
            <div class="content"><div class="demo-banner"><span>◈</span><div><strong>Modo demostración</strong><small>{{ mockEnabled ? 'Los datos de esta vista son de prueba. La autenticación usa la API real.' : 'Datos sincronizados desde la API Laravel.' }}</small></div><b>{{ mockEnabled ? 'MOCK DATA' : 'API REAL' }}</b></div><slot /></div>
        </section>
    </div>
</template>
