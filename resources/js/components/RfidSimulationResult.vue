<script setup lang="ts">
import type { Reading } from '../data/mockData';
defineProps<{ result: Reading }>();
</script>

<template>
    <section class="simulation-result" :class="result.resultado"><div class="result-header"><span class="result-symbol">{{ result.resultado === 'permitido' ? '✓' : '×' }}</span><div><span class="eyebrow">RESULTADO DE LA VALIDACIÓN</span><h2>ACCESO {{ result.resultado.toUpperCase() }}</h2></div></div><div class="result-details"><div><small>Locker</small><strong>{{ result.locker }}</strong></div><div><small>Usuario</small><strong>{{ result.usuario }}</strong></div><div><small>Motivo</small><strong>{{ result.motivo }}</strong></div></div><div class="hardware-result"><span>Servo <b>{{ result.resultado === 'permitido' ? 'DESBLOQUEADO' : 'BLOQUEADO' }}</b></span><span>LED verde <b>{{ result.resultado === 'permitido' ? 'ENCENDIDO' : 'APAGADO' }}</b></span><span>LED rojo <b>{{ result.resultado === 'permitido' ? 'APAGADO' : 'ENCENDIDO' }}</b></span><span>Buzzer <b>{{ result.resultado === 'permitido' ? '1 BEEP' : '3 BEEPS' }}</b></span></div></section>
</template>
