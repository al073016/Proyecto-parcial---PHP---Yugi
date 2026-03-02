<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YUGI Admin | Dashboard de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
        
        .bg-critico {
            animation: pulse-red 2s infinite;
        }
        @keyframes pulse-red {
            0% { background-color: rgba(220, 38, 38, 1); }
            50% { background-color: rgba(153, 27, 27, 1); }
            100% { background-color: rgba(220, 38, 38, 1); }
        }
    </style>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 min-h-screen">

    <div class="max-w-7xl mx-auto px-6 py-10">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-black italic tracking-tighter text-zinc-900 dark:text-white uppercase">YUGI <span class="text-indigo-600">Admin</span></h1>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.2em]">Panel de Control de Inventario</p>
            </div>
            <div class="flex gap-3">
                <button onclick="abrirModalCrear()" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                    Nuevo Objeto
                </button>
                <button onclick="cargarDashboard()" class="p-3 bg-zinc-200 dark:bg-zinc-800 rounded-xl hover:rotate-180 transition-all duration-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>

        <div id="stats-grid" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12"></div>

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

        <div class="bg-indigo-600 p-1 rounded-[2.5rem] shadow-2xl shadow-indigo-500/20">
            <div class="bg-zinc-900 rounded-[2.4rem] p-8 flex flex-col md:flex-row items-center gap-6">
                <div class="flex-1">
                    <h3 class="text-white text-xl font-black uppercase italic tracking-tighter">Despacho de Equipo</h3>
                    <p class="text-indigo-300 text-[10px] font-bold uppercase tracking-widest">Escaneo de salida o entrada (Express)</p>
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <input type="text" id="scan-id" placeholder="ID del equipo..." 
                           class="bg-zinc-800 border-none rounded-2xl px-6 py-4 text-white focus:ring-2 focus:ring-indigo-500 w-full md:w-64 font-mono uppercase"
                           onkeypress="if(event.key === 'Enter') verFicha(this.value)">
                    <button onclick="verFicha(document.getElementById('scan-id').value)" 
                            class="bg-indigo-600 hover:bg-indigo-500 text-white font-black px-8 py-4 rounded-2xl transition-all uppercase text-xs tracking-widest">
                        Verificar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-crear" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-[120] backdrop-blur-md p-4">
        <div class="bg-white dark:bg-zinc-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl border dark:border-zinc-800 overflow-hidden">
            <div class="p-8 border-b dark:border-zinc-800 flex justify-between items-center bg-indigo-600 text-white">
                <h2 class="text-xl font-black italic uppercase tracking-tighter">Registrar Nuevo Activo</h2>
                <button onclick="cerrarModal('modal-crear')" class="hover:rotate-90 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="form-crear" class="p-8 space-y-4" onsubmit="crearObjeto(event)">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-[10px] font-black uppercase text-zinc-500 ml-2">Nombre del Equipo</label>
                        <input type="text" name="nombre" required class="w-full bg-zinc-100 dark:bg-zinc-800 border-none rounded-2xl px-5 py-3 mt-1 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-zinc-500 ml-2">Categoría</label>
                        <select name="categoria" class="w-full bg-zinc-100 dark:bg-zinc-800 border-none rounded-2xl px-5 py-3 mt-1 focus:ring-2 focus:ring-indigo-500">
                            <option value="Computo">Cómputo</option>
                            <option value="Herramientas">Herramientas</option>
                            <option value="Audio/Video">Audio/Video</option>
                            <option value="Libros Técnicos">Libros Técnicos</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-zinc-500 ml-2">Vida Útil (Horas)</label>
                        <input type="number" name="vida_util_max" required class="w-full bg-zinc-100 dark:bg-zinc-800 border-none rounded-2xl px-5 py-3 mt-1 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <button type="submit" id="btn-submit-crear" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all mt-4">
                    Guardar en Inventario
                </button>
            </form>
        </div>
    </div>

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
                            <th class="pb-4 text-right">Ficha</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-modal-contenido" class="text-sm"></tbody>
                </table>
            </div>
        </div>
    </div>

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
        const token = localStorage.getItem('token_prestamos');
        const API_URL = '/api/items';

        const ESTADOS = {
            'disponible': { slug: 'disponible', label: 'Equipos Disponibles', color: 'bg-green-500' },
            'prestado': { slug: 'prestado', label: 'En Préstamo', color: 'bg-blue-500' },
            'atrasado': { slug: 'atrasado', label: 'Con Atraso', color: 'bg-red-500' },
            'mantenimiento': { slug: 'mantenimiento', label: 'Mantenimiento', color: 'bg-orange-500' }
        };

        async function cargarDashboard() {
            try {
                const response = await fetch(API_URL, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const result = await response.json();
                const items = result.data;

                const grid = document.getElementById('stats-grid');
                grid.innerHTML = '';
                Object.keys(ESTADOS).forEach(key => {
                    const count = items.filter(i => i.estado === ESTADOS[key].slug).length;
                    grid.innerHTML += `
                        <div onclick="mostrarDetalleEstado('${key}')" class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border dark:border-zinc-800 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all cursor-pointer group">
                            <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest group-hover:text-indigo-500 transition-colors">${ESTADOS[key].label}</p>
                            <div class="flex items-end justify-between mt-2">
                                <span class="text-5xl font-black italic tracking-tighter">${count}</span>
                                <div class="w-12 h-12 ${ESTADOS[key].color} rounded-2xl opacity-20 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <div class="w-6 h-6 border-4 border-white rounded-full"></div>
                                </div>
                            </div>
                        </div>`;
                });

                const tablaAtrasados = document.getElementById('tabla-atrasados');
                const itemsAtrasados = items.filter(i => i.estado === 'atrasado');
                
                if (itemsAtrasados.length === 0) {
                    tablaAtrasados.innerHTML = '<tr><td colspan="4" class="py-10 text-center text-zinc-500 uppercase font-bold text-[10px]">Sin alertas de retraso pendientes ✨</td></tr>';
                } else {
                    tablaAtrasados.innerHTML = '';
                    itemsAtrasados.forEach(item => {
                        tablaAtrasados.innerHTML += `
                            <tr class="border-b dark:border-zinc-800/50 hover:bg-red-500/5 transition-all">
                                <td class="py-5">
                                    <div class="font-black text-zinc-800 dark:text-zinc-100 uppercase">${item.nombre}</div>
                                    <div class="text-[9px] text-zinc-500 font-mono italic">ITEM-ID: ${item.id}</div>
                                </td>
                                <td class="py-5">
                                    <span class="px-3 py-1 bg-red-500/10 text-red-500 rounded-lg text-[9px] font-black uppercase">Puntos en Riesgo</span>
                                </td>
                                <td class="py-5">
                                    <span class="px-2 py-0.5 rounded bg-red-600 text-white text-[8px] font-black uppercase">Crítico</span>
                                </td>
                                <td class="py-5 text-right">
                                    <button onclick="verFicha(${item.id})" class="bg-zinc-900 text-white px-4 py-2 rounded-xl text-[10px] font-black hover:bg-red-600 transition-all uppercase">Auditar</button>
                                </td>
                            </tr>`;
                    });
                }
            } catch (e) {
                console.error("Error cargando dashboard:", e);
            }
        }

        function abrirModalCrear() {
            document.getElementById('modal-crear').classList.replace('hidden', 'flex');
        }

        async function crearObjeto(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-crear');
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            btn.disabled = true;
            btn.innerText = "GUARDANDO...";
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(data)
                });
                if (response.ok) {
                    alert("Equipo registrado con éxito.");
                    cerrarModal('modal-crear');
                    e.target.reset();
                    cargarDashboard();
                }
            } catch (e) { alert("Error de conexión"); }
            finally { btn.disabled = false; btn.innerText = "GUARDAR EN INVENTARIO"; }
        }

        async function verFicha(id) {
            if(!id) return;
            const modal = document.getElementById('modal-ficha');
            const contenido = document.getElementById('ficha-contenido');
            const linea = document.getElementById('linea-decorativa');
            
            modal.classList.replace('hidden', 'flex');
            contenido.innerHTML = '<div class="py-10 animate-spin border-4 border-indigo-500 border-t-transparent rounded-full w-12 h-12 mx-auto"></div>';

            try {
                const response = await fetch(`${API_URL}/${id}`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const result = await response.json();
                const item = result.data;

                // Color de la línea decorativa según estado
                let lineaColor = 'bg-indigo-600';
                if(item.estado === 'atrasado') lineaColor = 'bg-red-600';
                if(item.estado === 'mantenimiento') lineaColor = 'bg-orange-500';
                if(item.estado === 'disponible') lineaColor = 'bg-green-500';
                linea.className = `absolute top-0 left-0 w-full h-2 ${lineaColor}`;

                let actionButtons = '';
                if (item.estado === 'disponible') {
                    actionButtons = `
                        <button onclick="procesarCambioEstado(${item.id}, 'prestado')" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl mb-3">Autorizar Salida</button>
                        <button onclick="procesarCambioEstado(${item.id}, 'mantenimiento')" class="w-full py-3 bg-orange-500/10 text-orange-600 border border-orange-500/20 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all">Enviar a Mantenimiento</button>
                    `;
                } else if (item.estado === 'prestado' || item.estado === 'atrasado') {
                    const isAtrasado = item.estado === 'atrasado';
                    actionButtons = `
                        <div class="space-y-3">
                            ${isAtrasado ? '<p class="text-[10px] text-red-500 font-bold uppercase tracking-tighter">⚠️ Alerta: Entrega fuera de tiempo.</p>' : ''}
                            <button onclick="procesarCambioEstado(${item.id}, 'disponible')" class="w-full py-4 ${isAtrasado ? 'bg-critico' : 'bg-green-600'} text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                                ${isAtrasado ? 'Confirmar Entrega Tardía' : 'Recibir Equipo'}
                            </button>
                        </div>`;
                } else if (item.estado === 'mantenimiento') {
                    actionButtons = `<button onclick="procesarCambioEstado(${item.id}, 'disponible')" class="w-full py-4 bg-zinc-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-zinc-800 transition-all">Finalizar Mantenimiento</button>`;
                }

                contenido.innerHTML = `
                    <div class="mb-8">
                        <div class="w-20 h-20 ${item.estado === 'atrasado' ? 'bg-red-600/10' : 'bg-indigo-600/10'} rounded-[2rem] flex items-center justify-center mx-auto mb-4 border border-zinc-500/20">
                             <svg class="w-10 h-10 ${item.estado === 'mantenimiento' ? 'text-orange-500' : 'text-indigo-500'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mb-1">${item.categoria}</p>
                        <h2 class="text-3xl font-black italic tracking-tighter uppercase dark:text-white leading-tight">${item.nombre}</h2>
                        <div class="mt-2 inline-block px-3 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-full font-mono text-[9px] text-zinc-400">ID: ${item.id}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-8 text-left">
                        <div class="bg-zinc-100 dark:bg-zinc-800/40 p-4 rounded-3xl">
                            <p class="text-[9px] font-bold text-zinc-500 uppercase mb-1">Uso Acumulado</p>
                            <p class="text-xl font-black">${item.uso_acumulado} Horas</p>
                        </div>
                        <div class="bg-zinc-100 dark:bg-zinc-800/40 p-4 rounded-3xl">
                            <p class="text-[9px] font-bold text-zinc-500 uppercase mb-1">Estado Actual</p>
                            <p class="text-[11px] font-black uppercase ${item.estado === 'mantenimiento' ? 'text-orange-500' : 'text-indigo-500'}">${item.estado}</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        ${actionButtons}
                        <button onclick="cerrarModal('modal-ficha')" class="w-full py-3 text-zinc-500 font-bold text-[10px] uppercase hover:text-zinc-300">Cerrar</button>
                    </div>`;
            } catch (e) {
                contenido.innerHTML = '<p class="text-red-500 font-black uppercase py-10">ID no encontrado</p>';
            }
        }

        async function procesarCambioEstado(id, nuevoEstado) {
            if (!confirm(`¿Confirmar cambio a ${nuevoEstado}?`)) return;
            try {
                const response = await fetch(`${API_URL}/${id}`, {
                    method: 'PUT',
                    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ estado: nuevoEstado })
                });
                if (response.ok) {
                    cerrarModal('modal-ficha');
                    cerrarModal('modal-listado');
                    cargarDashboard();
                }
            } catch (e) { alert("Error de red."); }
        }

        async function mostrarDetalleEstado(key) {
            const modal = document.getElementById('modal-listado');
            const tabla = document.getElementById('tabla-modal-contenido');
            const info = ESTADOS[key];
            document.getElementById('modal-listado-titulo').innerText = info.label;
            document.getElementById('modal-listado-subtitulo').innerText = `Gestión de activos en ${info.slug}`;
            modal.classList.replace('hidden', 'flex');
            tabla.innerHTML = '<tr><td colspan="3" class="py-20 text-center animate-pulse font-black uppercase text-xs">Sincronizando...</td></tr>';
            try {
                const response = await fetch(API_URL, { headers: { 'Authorization': `Bearer ${token}` } });
                const result = await response.json();
                const filtrados = result.data.filter(i => i.estado === info.slug);
                tabla.innerHTML = filtrados.length ? '' : '<tr><td colspan="3" class="py-20 text-center text-zinc-500 font-black text-xs uppercase">Vacio</td></tr>';
                filtrados.forEach(item => {
                    tabla.innerHTML += `
                        <tr class="border-b dark:border-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800/30 transition-all">
                            <td class="py-4">
                                <div class="font-black text-zinc-800 dark:text-zinc-100 uppercase">${item.nombre}</div>
                                <div class="text-[9px] text-zinc-500 font-mono">#${item.id}</div>
                            </td>
                            <td class="py-4">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black uppercase text-zinc-400">${item.categoria}</span>
                                    <span class="text-indigo-500 text-[10px] font-black uppercase">USO: ${item.uso_acumulado}H</span>
                                </div>
                            </td>
                            <td class="py-4 text-right">
                                <button onclick="verFicha(${item.id})" class="bg-indigo-600/10 text-indigo-500 border border-indigo-500/20 px-4 py-2 rounded-xl text-[10px] font-black hover:bg-indigo-600 hover:text-white transition-all">ABRIR</button>
                            </td>
                        </tr>`;
                });
            } catch (e) { console.error(e); }
        }

        function cerrarModal(id) {
            document.getElementById(id).classList.replace('flex', 'hidden');
        }

        document.addEventListener('DOMContentLoaded', cargarDashboard);
    </script>
</body>
</html>