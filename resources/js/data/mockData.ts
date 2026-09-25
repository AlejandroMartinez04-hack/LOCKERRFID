export type User = { id: number; name: string; email: string; rol: string; estado: string };
export type Locker = { id: number; numero: string; ubicacion: string; estado: string; asignacion_activa?: Assignment | null };
export type Assignment = { id: number; locker_id: number; usuario_id: number; fecha_inicio: string; fecha_fin: string | null; estado: string; locker?: Locker; usuario?: User };
export type Reading = { fecha: string; uid: string; usuario: string; locker: string; dispositivo: string; tipo: string; resultado: string; motivo: string };
export type RfidCard = { id: number; uid: string; usuario: string; estado: string; fecha: string };
export type Device = { id: number; nombre: string; ubicacion: string; estado: string; ip: string; comunicacion: string };

export const users: User[] = [
    { id: 1, name: 'Administrador', email: 'admin@silocker.local', rol: 'admin', estado: 'activo' },
    { id: 2, name: 'Carlos Mendoza', email: 'carlos.mendoza@example.com', rol: 'usuario', estado: 'activo' },
    { id: 3, name: 'María Fernández', email: 'maria.fernandez@example.com', rol: 'usuario', estado: 'activo' },
    { id: 4, name: 'Juan Morales', email: 'juan.morales@example.com', rol: 'usuario', estado: 'activo' },
    { id: 5, name: 'Ana Gómez', email: 'ana.gomez@example.com', rol: 'usuario', estado: 'activo' },
    { id: 6, name: 'Pedro Rivas', email: 'pedro.rivas@example.com', rol: 'usuario', estado: 'inactivo' },
];

export const lockers: Locker[] = [
    { id: 1, numero: '01', ubicacion: 'Bloque A - Planta 1', estado: 'disponible' },
    { id: 2, numero: '02', ubicacion: 'Bloque A - Planta 1', estado: 'ocupado' },
    { id: 3, numero: '03', ubicacion: 'Bloque B - Planta 1', estado: 'ocupado' },
    { id: 4, numero: '04', ubicacion: 'Bloque B - Planta 2', estado: 'mantenimiento' },
    { id: 5, numero: '05', ubicacion: 'Bloque C - Planta 2', estado: 'bloqueado' },
    { id: 6, numero: '06', ubicacion: 'Bloque C - Planta 2', estado: 'disponible' },
];

export const assignments: Assignment[] = [
    { id: 1, locker_id: 2, usuario_id: 2, fecha_inicio: '2026-09-01', fecha_fin: null, estado: 'activa', locker: lockers[1], usuario: users[1] },
    { id: 2, locker_id: 3, usuario_id: 3, fecha_inicio: '2026-09-05', fecha_fin: null, estado: 'activa', locker: lockers[2], usuario: users[2] },
    { id: 3, locker_id: 1, usuario_id: 4, fecha_inicio: '2026-08-01', fecha_fin: '2026-09-01', estado: 'finalizada', locker: lockers[0], usuario: users[3] },
];

export const cards: RfidCard[] = [
    { id: 1, uid: 'A3:B7:91:2F', usuario: 'Carlos Mendoza', estado: 'activa', fecha: '24 sep 2026' },
    { id: 2, uid: 'F2:41:8A:11', usuario: 'María Fernández', estado: 'activa', fecha: '24 sep 2026' },
    { id: 3, uid: 'B1:33:92:AA', usuario: 'Juan Morales', estado: 'activa', fecha: '23 sep 2026' },
];

export const devices: Device[] = [
    { id: 1, nombre: 'ESP32-LOCKER-01', ubicacion: 'Bloque A', estado: 'activo', ip: '192.168.1.120', comunicacion: 'hace 2 min' },
    { id: 2, nombre: 'ESP32-LOCKER-02', ubicacion: 'Bloque B', estado: 'activo', ip: '192.168.1.121', comunicacion: 'hace 4 min' },
    { id: 3, nombre: 'ESP32-LOCKER-03', ubicacion: 'Bloque C', estado: 'mantenimiento', ip: '192.168.1.122', comunicacion: 'hace 2 días' },
];

export const readings: Reading[] = [
    { fecha: '24/09/2026 09:45', uid: 'ADMIN', usuario: 'Administrador', locker: 'Locker 02', dispositivo: 'ESP32-LOCKER-01', tipo: 'admin', resultado: 'permitido', motivo: 'Apertura administrativa' },
    { fecha: '24/09/2026 09:40', uid: 'B1:33:92:AA', usuario: 'Juan Morales', locker: 'Locker 02', dispositivo: 'ESP32-LOCKER-01', tipo: 'usuario', resultado: 'denegado', motivo: 'Locker asignado a otro usuario' },
    { fecha: '24/09/2026 09:35', uid: 'F2:41:8A:11', usuario: 'María Fernández', locker: 'Locker 03', dispositivo: 'ESP32-LOCKER-01', tipo: 'usuario', resultado: 'permitido', motivo: 'Locker asignado' },
    { fecha: '24/09/2026 09:30', uid: 'A3:B7:91:2F', usuario: 'Carlos Mendoza', locker: 'Locker 02', dispositivo: 'ESP32-LOCKER-01', tipo: 'usuario', resultado: 'permitido', motivo: 'Locker asignado' },
];

export const hardware = ['RC522 conectado', 'Servo listo', 'OLED conectada', 'LED verde listo', 'LED rojo listo', 'Buzzer listo'];
