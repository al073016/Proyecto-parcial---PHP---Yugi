<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YUGI Admin | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Syne', sans-serif; }
        .mono { font-family: 'Space Mono', monospace; }
        .custom-scroll::-webkit-scrollbar { width:6px; }
        .custom-scroll::-webkit-scrollbar-track { background:transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background:#3f3f46; border-radius:10px; }
        @keyframes pulse-red { 0%{background-color:rgba(220,38,38,1)} 50%{background-color:rgba(153,27,27,1)} 100%{background-color:rgba(220,38,38,1)} }
        .bg-critico { animation: pulse-red 2s infinite; }
        .fade-in { animation: fadeIn .25s ease; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
    </style>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 min-h-screen">

<div class="max-w-7xl mx-auto px-6 py-10">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold italic tracking-tighter text-zinc-900 dark:text-white uppercase">
                YUGI <span class="text-indigo-600">Admin</span>
            </h1>
            <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.2em]">Panel de Control de Inventario</p>
        </div>
        <div class="flex gap-3 items-center">
            <a href="/catalogo" class="px-4 py-2.5 bg-zinc-200 dark:bg-zinc-800 rounded-xl text-[10px] font-black uppercase hover:bg-zinc-300 transition-all">
                ← Catálogo
            </a>
            <button onclick="abrirModalCrear()"
                class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                Nuevo Objeto
            </button>
            <button onclick="cargarDashboard()" class="p-3 bg-zinc-200 dark:bg-zinc-800 rounded-xl hover:rotate-180 transition-all duration-500" title="Actualizar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button onclick="logout()" class="text-[10px] font-black text-red-500 uppercase border border-red-500/20 px-3 py-2 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                Salir
            </button>
        </div>
    </div>

    <!-- STATS -->
    <div id="stats-grid" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12"></div>

    <!-- TABLA ATRASADOS -->
    <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-8 border dark:border-zinc-800 shadow-xl mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black uppercase italic tracking-tighter flex items-center gap-2">
                <span class="w-3 h-3 bg-red-600 rounded-full animate-pulse"></span>
                Alertas de Devolución con Retraso
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-400 text-[10px] uppercase tracking-widest border-b dark:border-zinc-800">
                        <th class="pb-4">Equipo / ID</th>
                        <th class="pb-4">Estado del Préstamo</th>
                        <th class="pb-4">Nivel de Riesgo</th>
                        <th class="pb-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody id="tabla-atrasados" class="text-sm"></tbody>
            </table>
        </div>
    </div>

    <!-- HISTORIAL DE PRÉSTAMOS -->
    <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-8 border dark:border-zinc-800 shadow-xl mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black uppercase italic tracking-tighter">📋 Historial de Préstamos</h2>
            <span id="total-prestamos" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest"></span>
        </div>
        <div class="overflow-x-auto custom-scroll">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-400 text-[10px] uppercase tracking-widest border-b dark:border-zinc-800">
                        <th class="pb-4">Alumno</th>
                        <th class="pb-4">Objeto</th>
                        <th class="pb-4">Fecha Préstamo</th>
                        <th class="pb-4">Fecha Límite</th>
                        <th class="pb-4">Multa</th>
                        <th class="pb-4 text-right">Estado</th>
                    </tr>
                </thead>
                <tbody id="tabla-prestamos" class="text-sm"></tbody>
            </table>
        </div>
    </div>

    <!-- ESCÁNER EXPRESS -->
    <div class="bg-indigo-600 p-1 rounded-[2.5rem] shadow-2xl shadow-indigo-500/20">
        <div class="bg-zinc-900 rounded-[2.4rem] p-8 flex flex-col md:flex-row items-center gap-6">
            <div class="flex-1">
                <h3 class="text-white text-xl font-black uppercase italic tracking-tighter">Despacho de Equipo</h3>
                <p class="text-indigo-300 text-[10px] font-bold uppercase tracking-widest">Buscar por ID de equipo</p>
            </div>
            <div class="flex gap-3 w-full md:w-auto">
                <input type="text" id="scan-id" placeholder="ID del equipo..."
                       class="bg-zinc-800 border-none rounded-2xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 w-full md:w-64 mono uppercase"
                       onkeypress="if(event.key==='Enter') verFicha(this.value)">
                <button onclick="verFicha(document.getElementById('scan-id').value)"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white font-black px-8 py-4 rounded-2xl transition-all uppercase text-xs tracking-widest">
                    Verificar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════ MODAL: CREAR OBJETO ══════════ -->
<div id="modal-crear" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-[120] backdrop-blur-md p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl border dark:border-zinc-800 overflow-hidden">
        <div class="p-8 border-b dark:border-zinc-800 flex justify-between items-center bg-indigo-600 text-white">
            <h2 class="text-xl font-black italic uppercase tracking-tighter">Registrar Nuevo Activo</h2>
            <button onclick="cerrarModal('modal-crear')" class="hover:rotate-90 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-crear" class="p-8 space-y-4" onsubmit="crearObjeto(event)">
            <div>
                <label class="text-[10px] font-black uppercase text-zinc-500 ml-2">Nombre del Equipo</label>
                <input type="text" name="nombre" required
                    class="w-full bg-zinc-100 dark:bg-zinc-800 border-none rounded-2xl px-5 py-3 mt-1 focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-500 ml-2">Categoría</label>
                    <select name="categoria" class="w-full bg-zinc-100 dark:bg-zinc-800 border-none rounded-2xl px-5 py-3 mt-1 focus:ring-2 focus:ring-indigo-500">
                        <option value="Computo">Cómputo</option>
                        <option value="Herramientas">Herramientas</option>
                        <option value="Audio/Video">Audio/Video</option>
                        <option value="Libros Técnicos">Libros Técnicos</option>
                        <option value="Instrumentos">Instrumentos</option>
                        <option value="Electrónica">Electrónica</option>
                        <option value="Maquinaria">Maquinaria</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-500 ml-2">Vida Útil (Horas)</label>
                    <input type="number" name="vida_util_max" required min="1"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border-none rounded-2xl px-5 py-3 mt-1 focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <button type="submit" id="btn-submit-crear"
                class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all mt-4">
                Guardar en Inventario
            </button>
        </form>
    </div>
</div>

<!-- ══════════ MODAL: EDITAR OBJETO ══════════ -->
<div id="modal-editar" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-[120] backdrop-blur-md p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl border dark:border-zinc-800 overflow-hidden">
        <div class="p-8 border-b dark:border-zinc-800 flex justify-between items-center bg-zinc-800 text-white">
            <h2 class="text-xl font-black italic uppercase tracking-tighter">✏️ Editar Activo</h2>
            <button onclick="cerrarModal('modal-editar')" class="hover:rotate-90 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-editar" class="p-8 space-y-4" onsubmit="guardarEdicion(event)">
            <input type="hidden" id="editar-id">
            <div>
                <label class="text-[10px] font-black uppercase text-zinc-500 ml-2">Nombre del Equipo</label>
                <input type="text" id="editar-nombre" required
                    class="w-full bg-zinc-100 dark:bg-zinc-800 border-none rounded-2xl px-5 py-3 mt-1 focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-500 ml-2">Categoría</label>
                    <select id="editar-categoria" class="w-full bg-zinc-100 dark:bg-zinc-800 border-none rounded-2xl px-5 py-3 mt-1 focus:ring-2 focus:ring-indigo-500">
                        <option value="Computo">Cómputo</option>
                        <option value="Herramientas">Herramientas</option>
                        <option value="Audio/Video">Audio/Video</option>
                        <option value="Libros Técnicos">Libros Técnicos</option>
                        <option value="Instrumentos">Instrumentos</option>
                        <option value="Electrónica">Electrónica</option>
                        <option value="Maquinaria">Maquinaria</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-500 ml-2">Vida Útil (Horas)</label>
                    <input type="number" id="editar-vida-util" required min="1"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border-none rounded-2xl px-5 py-3 mt-1 focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" id="btn-submit-editar"
                    class="flex-1 py-4 bg-zinc-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-600 transition-all">
                    Guardar Cambios
                </button>
                <button type="button" onclick="cerrarModal('modal-editar')"
                    class="px-6 py-4 bg-zinc-100 dark:bg-zinc-800 rounded-2xl font-black text-xs uppercase text-zinc-500">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════ MODAL: LISTADO POR ESTADO ══════════ -->
<div id="modal-listado" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-[100] backdrop-blur-md p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-4xl max-h-[85vh] overflow-hidden flex flex-col rounded-[2.5rem] shadow-2xl border dark:border-zinc-800">
        <div class="p-8 border-b dark:border-zinc-800 flex justify-between items-center">
            <div>
                <h2 id="modal-listado-titulo" class="text-2xl font-black italic uppercase tracking-tighter text-indigo-600">Listado</h2>
                <p id="modal-listado-subtitulo" class="text-zinc-500 text-[10px] font-bold uppercase tracking-[0.2em]"></p>
            </div>
            <button onclick="cerrarModal('modal-listado')" class="p-3 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-full transition-colors">
                <svg class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-8 overflow-y-auto custom-scroll">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-400 text-[10px] uppercase tracking-widest border-b dark:border-zinc-800">
                        <th class="pb-4">Nombre del Activo</th>
                        <th class="pb-4">Categoría / Uso</th>
                        <th class="pb-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-modal-contenido" class="text-sm"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══════════ MODAL: FICHA DEL OBJETO ══════════ -->
<div id="modal-ficha" class="fixed inset-0 bg-black/90 hidden items-center justify-center z-[110] backdrop-blur-xl p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-md p-10 rounded-[3rem] shadow-2xl border dark:border-zinc-800 relative overflow-hidden">
        <div id="linea-decorativa" class="absolute top-0 left-0 w-full h-2 bg-indigo-600 transition-colors duration-500"></div>
        <button onclick="cerrarModal('modal-ficha')" class="absolute top-6 right-6 text-zinc-500 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div id="ficha-contenido" class="text-center pt-4"></div>
    </div>
</div>

<script>
    const token   = localStorage.getItem('token_prestamos');
    const userInfo= JSON.parse(localStorage.getItem('user_info') || '{}');
    const API     = '/api/items';

    // Redirigir si no es admin
    if (!token || userInfo.rol !== 'admin') window.location.href = '/login';

    const ESTADOS = {
        'disponible':    { label:'Equipos Disponibles', color:'bg-green-500' },
        'prestado':      { label:'En Préstamo',         color:'bg-blue-500'  },
        'atrasado':      { label:'Con Atraso',          color:'bg-red-500'   },
        'mantenimiento': { label:'Mantenimiento',       color:'bg-orange-500'},
    };

    // ── Dashboard principal ──
    async function cargarDashboard() {
        try {
            const [resItems, resLoans] = await Promise.all([
                fetch(API, { headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'} }),
                fetch('/api/loans', { headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'} }),
            ]);
            const { data: items }  = await resItems.json();
            const { data: loans }  = await resLoans.json();

            // Stats
            const grid = document.getElementById('stats-grid');
            grid.innerHTML = '';
            Object.keys(ESTADOS).forEach(key => {
                const count = items.filter(i => i.estado === key).length;
                grid.innerHTML += `
                    <div onclick="mostrarDetalleEstado('${key}')"
                        class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border dark:border-zinc-800 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all cursor-pointer group">
                        <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest group-hover:text-indigo-500 transition-colors">${ESTADOS[key].label}</p>
                        <div class="flex items-end justify-between mt-2">
                            <span class="text-5xl font-black italic tracking-tighter">${count}</span>
                            <div class="w-12 h-12 ${ESTADOS[key].color} rounded-2xl opacity-20 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                    </div>`;
            });

            // Tabla atrasados
            const atrasados = items.filter(i => i.estado === 'atrasado');
            const tablaAt   = document.getElementById('tabla-atrasados');
            if (atrasados.length === 0) {
                tablaAt.innerHTML = '<tr><td colspan="4" class="py-10 text-center text-zinc-500 uppercase font-bold text-[10px]">Sin alertas de retraso pendientes ✨</td></tr>';
            } else {
                tablaAt.innerHTML = atrasados.map(item => `
                    <tr class="border-b dark:border-zinc-800/50 hover:bg-red-500/5 transition-all">
                        <td class="py-5">
                            <div class="font-black uppercase">${item.nombre}</div>
                            <div class="text-[9px] text-zinc-500 mono">ITEM-ID: ${item.id}</div>
                        </td>
                        <td class="py-5"><span class="px-3 py-1 bg-red-500/10 text-red-500 rounded-lg text-[9px] font-black uppercase">Puntos en Riesgo</span></td>
                        <td class="py-5"><span class="px-2 py-0.5 rounded bg-red-600 text-white text-[8px] font-black uppercase">Crítico</span></td>
                        <td class="py-5 text-right">
                            <button onclick="verFicha(${item.id})" class="bg-zinc-900 text-white px-4 py-2 rounded-xl text-[10px] font-black hover:bg-red-600 transition-all uppercase">Auditar</button>
                        </td>
                    </tr>`).join('');
            }

            // Historial de préstamos (GET /api/loans — endpoint real)
            const tablaP = document.getElementById('tabla-prestamos');
            document.getElementById('total-prestamos').textContent = `${(loans||[]).length} registros`;
            if (!loans || loans.length === 0) {
                tablaP.innerHTML = '<tr><td colspan="6" class="py-10 text-center text-zinc-500 font-bold text-[10px] uppercase">Sin préstamos registrados</td></tr>';
            } else {
                tablaP.innerHTML = loans.map(l => {
                    const activo   = !l.fecha_devolucion_real;
                    const atrasado = activo && new Date(l.fecha_devolucion_esperada) < new Date();
                    const badge    = activo
                        ? (atrasado
                            ? '<span class="px-2 py-1 bg-red-100 text-red-600 rounded-lg text-[9px] font-black uppercase">Atrasado</span>'
                            : '<span class="px-2 py-1 bg-green-100 text-green-600 rounded-lg text-[9px] font-black uppercase">Activo</span>')
                        : '<span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 rounded-lg text-[9px] font-black uppercase">Devuelto</span>';
                    const fechaP   = l.fecha_prestamo ? new Date(l.fecha_prestamo).toLocaleDateString('es-MX') : '—';
                    const fechaL   = l.fecha_devolucion_esperada ? new Date(l.fecha_devolucion_esperada).toLocaleDateString('es-MX') : '—';
                    const multa    = parseFloat(l.monto_multa||0) > 0
                        ? `<span class="text-red-500 font-black">$${parseFloat(l.monto_multa).toFixed(2)}</span>`
                        : '<span class="text-zinc-400">$0.00</span>';
                    return `
                        <tr class="border-b dark:border-zinc-800/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-all">
                            <td class="py-4 font-bold">${l.user?.name || '—'}</td>
                            <td class="py-4 uppercase font-black text-sm">${l.item?.nombre || '—'}</td>
                            <td class="py-4 text-zinc-500 text-[11px] mono">${fechaP}</td>
                            <td class="py-4 text-zinc-500 text-[11px] mono">${fechaL}</td>
                            <td class="py-4">${multa}</td>
                            <td class="py-4 text-right">${badge}</td>
                        </tr>`;
                }).join('');
            }

        } catch(e) { console.error('Error dashboard:', e); }
    }

    // ── Crear objeto (POST /api/items) ──
    function abrirModalCrear() {
        document.getElementById('modal-crear').classList.replace('hidden','flex');
    }
    async function crearObjeto(e) {
        e.preventDefault();
        const btn  = document.getElementById('btn-submit-crear');
        const data = Object.fromEntries(new FormData(e.target).entries());
        data.vida_util_max = parseInt(data.vida_util_max);
        btn.disabled = true; btn.innerText = 'GUARDANDO...';
        try {
            const res = await fetch(API, {
                method:'POST',
                headers:{'Authorization':`Bearer ${token}`,'Content-Type':'application/json','Accept':'application/json'},
                body: JSON.stringify(data)
            });
            if (res.ok) { alert('Equipo registrado con éxito.'); cerrarModal('modal-crear'); e.target.reset(); cargarDashboard(); }
            else { const d = await res.json(); alert('Error: ' + d.message); }
        } catch(e) { alert('Error de conexión'); }
        finally { btn.disabled = false; btn.innerText = 'GUARDAR EN INVENTARIO'; }
    }

    // ── Editar objeto (PUT /api/items/{id}) ──
    function abrirModalEditar(id, nombre, categoria, vidaUtil) {
        document.getElementById('editar-id').value         = id;
        document.getElementById('editar-nombre').value     = nombre;
        document.getElementById('editar-vida-util').value  = vidaUtil;
        // Seleccionar la categoría correcta
        const sel = document.getElementById('editar-categoria');
        for (let opt of sel.options) { opt.selected = opt.value === categoria; }
        cerrarModal('modal-ficha');
        cerrarModal('modal-listado');
        document.getElementById('modal-editar').classList.replace('hidden','flex');
    }
    async function guardarEdicion(e) {
        e.preventDefault();
        const id  = document.getElementById('editar-id').value;
        const btn = document.getElementById('btn-submit-editar');
        const payload = {
            nombre:        document.getElementById('editar-nombre').value,
            categoria:     document.getElementById('editar-categoria').value,
            vida_util_max: parseInt(document.getElementById('editar-vida-util').value),
        };
        btn.disabled = true; btn.innerText = 'GUARDANDO...';
        try {
            const res = await fetch(`${API}/${id}`, {
                method:'PUT',
                headers:{'Authorization':`Bearer ${token}`,'Content-Type':'application/json','Accept':'application/json'},
                body: JSON.stringify(payload)
            });
            if (res.ok) { alert('Objeto actualizado correctamente.'); cerrarModal('modal-editar'); cargarDashboard(); }
            else { const d = await res.json(); alert('Error: ' + d.message); }
        } catch(e) { alert('Error de conexión'); }
        finally { btn.disabled = false; btn.innerText = 'GUARDAR CAMBIOS'; }
    }

    // ── Eliminar objeto (DELETE /api/items/{id}) ──
    async function eliminarObjeto(id, nombre) {
        if (!confirm(`¿Eliminar "${nombre}" del inventario? Esta acción no se puede deshacer.`)) return;
        try {
            const res = await fetch(`${API}/${id}`, {
                method:'DELETE',
                headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}
            });
            const d = await res.json();
            if (res.ok) { alert('Objeto eliminado del inventario.'); cerrarModal('modal-ficha'); cerrarModal('modal-listado'); cargarDashboard(); }
            else { alert('Error: ' + d.message); }
        } catch(e) { alert('Error de conexión'); }
    }

    // ── Ficha del objeto ──
    async function verFicha(id) {
        if (!id) return;
        const modal    = document.getElementById('modal-ficha');
        const contenido= document.getElementById('ficha-contenido');
        const linea    = document.getElementById('linea-decorativa');
        modal.classList.replace('hidden','flex');
        contenido.innerHTML = '<div class="py-10 animate-spin border-4 border-indigo-500 border-t-transparent rounded-full w-12 h-12 mx-auto"></div>';

        try {
            const res    = await fetch(`${API}/${id}`, { headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'} });
            const result = await res.json();
            const item   = result.data;

            const lineaColor = { atrasado:'bg-red-600', mantenimiento:'bg-orange-500', disponible:'bg-green-500' }[item.estado] || 'bg-indigo-600';
            linea.className  = `absolute top-0 left-0 w-full h-2 ${lineaColor}`;

            // Botones de acción según estado
            let actionButtons = '';
            if (item.estado === 'disponible') {
                actionButtons = `
                    <button onclick="procesarCambioEstado(${item.id}, 'prestado')"
                        class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl mb-3">
                        Autorizar Salida
                    </button>
                    <button onclick="procesarCambioEstado(${item.id}, 'mantenimiento')"
                        class="w-full py-3 bg-orange-500/10 text-orange-600 border border-orange-500/20 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all mb-3">
                        Enviar a Mantenimiento
                    </button>`;
            } else if (item.estado === 'prestado' || item.estado === 'atrasado') {
                const isAtrasado = item.estado === 'atrasado';
                actionButtons = `
                    <div class="space-y-3">
                        ${isAtrasado ? '<p class="text-[10px] text-red-500 font-bold uppercase tracking-tighter">⚠️ Alerta: Entrega fuera de tiempo.</p>' : ''}
                        <button onclick="procesarCambioEstado(${item.id}, 'disponible')"
                            class="w-full py-4 ${isAtrasado ? 'bg-critico' : 'bg-green-600'} text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all mb-3">
                            ${isAtrasado ? 'Confirmar Entrega Tardía' : 'Recibir Equipo'}
                        </button>
                    </div>`;
            } else if (item.estado === 'mantenimiento') {
                // USA EL ENDPOINT CORRECTO: completar-mantenimiento (resetea uso_acumulado a 0)
                actionButtons = `
                    <button onclick="completarMantenimiento(${item.id})"
                        class="w-full py-4 bg-zinc-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-600 transition-all mb-3">
                        Finalizar Mantenimiento
                    </button>`;
            }

            contenido.innerHTML = `
                <div class="mb-8">
                    <div class="w-20 h-20 ${item.estado==='atrasado'?'bg-red-600/10':'bg-indigo-600/10'} rounded-[2rem] flex items-center justify-center mx-auto mb-4 border border-zinc-500/20">
                        <svg class="w-10 h-10 ${item.estado==='mantenimiento'?'text-orange-500':'text-indigo-500'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mb-1">${item.categoria}</p>
                    <h2 class="text-3xl font-black italic tracking-tighter uppercase dark:text-white leading-tight">${item.nombre}</h2>
                    <div class="mt-2 inline-block px-3 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-full mono text-[9px] text-zinc-400">ID: ${item.id}</div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-8 text-left">
                    <div class="bg-zinc-100 dark:bg-zinc-800/40 p-4 rounded-3xl">
                        <p class="text-[9px] font-bold text-zinc-500 uppercase mb-1">Uso Acumulado</p>
                        <p class="text-xl font-black">${item.uso_acumulado} Horas</p>
                    </div>
                    <div class="bg-zinc-100 dark:bg-zinc-800/40 p-4 rounded-3xl">
                        <p class="text-[9px] font-bold text-zinc-500 uppercase mb-1">Estado Actual</p>
                        <p class="text-[11px] font-black uppercase ${item.estado==='mantenimiento'?'text-orange-500':'text-indigo-500'}">${item.estado}</p>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    ${actionButtons}
                    <button onclick="abrirModalEditar(${item.id}, '${item.nombre.replace(/'/g,"\\'")}', '${item.categoria}', ${item.vida_util_max})"
                        class="w-full py-3 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-100 transition-all">
                        ✏️ Editar Nombre / Categoría / Vida Útil
                    </button>
                    <button onclick="eliminarObjeto(${item.id}, '${item.nombre.replace(/'/g,"\\'")}' )"
                        class="w-full py-3 bg-red-50 dark:bg-red-900/20 text-red-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all">
                        🗑 Eliminar del Inventario
                    </button>
                    <button onclick="cerrarModal('modal-ficha')" class="w-full py-3 text-zinc-500 font-bold text-[10px] uppercase hover:text-zinc-300">Cerrar</button>
                </div>`;
        } catch(e) {
            contenido.innerHTML = '<p class="text-red-500 font-black uppercase py-10">ID no encontrado</p>';
        }
    }

    // ── Completar mantenimiento — usa el endpoint correcto ──
    async function completarMantenimiento(id) {
        if (!confirm('¿Confirmar que el mantenimiento ha sido completado?')) return;
        try {
            const res = await fetch(`${API}/${id}/completar-mantenimiento`, {
                method:'POST',
                headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}
            });
            if (res.ok) { cerrarModal('modal-ficha'); cargarDashboard(); }
            else { const d = await res.json(); alert('Error: ' + d.message); }
        } catch(e) { alert('Error de conexión'); }
    }

    // ── Cambio de estado genérico ──
    async function procesarCambioEstado(id, nuevoEstado) {
        if (!confirm(`¿Confirmar cambio a ${nuevoEstado}?`)) return;
        try {
            const res = await fetch(`${API}/${id}`, {
                method:'PUT',
                headers:{'Authorization':`Bearer ${token}`,'Content-Type':'application/json','Accept':'application/json'},
                body: JSON.stringify({ estado: nuevoEstado })
            });
            if (res.ok) { cerrarModal('modal-ficha'); cerrarModal('modal-listado'); cargarDashboard(); }
        } catch(e) { alert('Error de red.'); }
    }

    // ── Listado por estado ──
    async function mostrarDetalleEstado(key) {
        const modal = document.getElementById('modal-listado');
        const tabla = document.getElementById('tabla-modal-contenido');
        document.getElementById('modal-listado-titulo').innerText    = ESTADOS[key].label;
        document.getElementById('modal-listado-subtitulo').innerText = `Gestión de activos en estado: ${key}`;
        modal.classList.replace('hidden','flex');
        tabla.innerHTML = '<tr><td colspan="3" class="py-20 text-center animate-pulse font-black uppercase text-xs">Sincronizando...</td></tr>';
        try {
            const res    = await fetch(API, { headers:{'Authorization':`Bearer ${token}`} });
            const result = await res.json();
            const filtrados = result.data.filter(i => i.estado === key);
            tabla.innerHTML = filtrados.length ? '' : '<tr><td colspan="3" class="py-20 text-center text-zinc-500 font-black text-xs uppercase">Vacío</td></tr>';
            filtrados.forEach(item => {
                tabla.innerHTML += `
                    <tr class="border-b dark:border-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800/30 transition-all">
                        <td class="py-4">
                            <div class="font-black uppercase">${item.nombre}</div>
                            <div class="text-[9px] text-zinc-500 mono">#${item.id}</div>
                        </td>
                        <td class="py-4">
                            <span class="text-[10px] font-black uppercase text-zinc-400">${item.categoria}</span>
                            <span class="block text-indigo-500 text-[10px] font-black uppercase">USO: ${item.uso_acumulado}H</span>
                        </td>
                        <td class="py-4 text-right flex gap-2 justify-end">
                            <button onclick="abrirModalEditar(${item.id},'${item.nombre.replace(/'/g,"\\'")}','${item.categoria}',${item.vida_util_max})"
                                class="bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 px-3 py-2 rounded-xl text-[10px] font-black hover:bg-indigo-600 hover:text-white transition-all">
                                ✏️
                            </button>
                            <button onclick="verFicha(${item.id})"
                                class="bg-indigo-600/10 text-indigo-500 border border-indigo-500/20 px-4 py-2 rounded-xl text-[10px] font-black hover:bg-indigo-600 hover:text-white transition-all">
                                ABRIR
                            </button>
                        </td>
                    </tr>`;
            });
        } catch(e) { console.error(e); }
    }

    function cerrarModal(id) {
        document.getElementById(id).classList.replace('flex','hidden');
    }

    // ── Logout correcto ──
    async function logout() {
        try {
            await fetch('/api/logout', {
                method:'POST',
                headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}
            });
        } catch(e) {}
        localStorage.clear();
        window.location.href = '/login';
    }

    document.addEventListener('DOMContentLoaded', cargarDashboard);
</script>
</body>
</html>
