<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yugi | Sistema de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Syne', sans-serif; }
        .mono { font-family: 'Space Mono', monospace; }
        .status-disponible   { background:#ecfdf5; color:#059669; border:1px solid #10b98133; }
        .status-prestado     { background:#fff7ed; color:#d97706; border:1px solid #f59e0b33; }
        .status-mantenimiento{ background:#fef2f2; color:#dc2626; border:1px solid #ef444433; }
        .status-atrasado     { background:#fdf4ff; color:#9333ea; border:1px solid #a855f733; }
        .nav-active { color:#4f46e5 !important; border-bottom:2px solid #4f46e5; }
        .fade-in { animation: fadeIn .3s ease; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        .pill-active { background:#4f46e5 !important; color:#fff !important; border-color:#4f46e5 !important; }
        .item-card { transition: transform .2s, box-shadow .2s; }
        .item-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,.08); }
        .health-bar-inner { transition: width .6s ease; }
        .skeleton { background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
                    background-size:200% 100%; animation:shimmer 1.5s infinite; border-radius:1rem; }
        @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
    </style>
</head>
<body class="bg-slate-50 dark:bg-zinc-950 min-h-screen text-zinc-900 dark:text-zinc-100">

<!-- NAV -->
<nav class="sticky top-0 z-40 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border-b dark:border-zinc-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
        <div class="flex items-center gap-8">
            <h1 class="text-2xl font-extrabold italic tracking-tighter">YUGI<span class="text-indigo-600">.</span></h1>
            <div class="hidden md:flex gap-6 h-16">
                <button onclick="switchTab('catalogo')" id="tab-catalogo"
                    class="nav-active text-[10px] font-black uppercase tracking-widest px-2 transition-all">Catálogo</button>
                <button onclick="switchTab('mis-prestamos')" id="tab-mis-prestamos"
                    class="text-zinc-400 hover:text-indigo-500 text-[10px] font-black uppercase tracking-widest px-2 transition-all flex items-center gap-2">
                    Mis Solicitudes
                    <span id="badge-prestamos" class="hidden bg-indigo-600 text-white text-[9px] px-1.5 py-0.5 rounded-full">0</span>
                </button>
                <button onclick="switchTab('perfil')" id="tab-perfil"
                    class="text-zinc-400 hover:text-indigo-500 text-[10px] font-black uppercase tracking-widest px-2 transition-all">Mi Perfil</button>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <a id="btn-admin-panel" href="/admin/dashboard"
               class="hidden bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white text-[10px] font-black px-4 py-2 rounded-xl hover:bg-indigo-600 hover:text-white transition-all">
                ⚙ ADMIN
            </a>
            <button onclick="logout()"
                class="text-[10px] font-black text-red-500 uppercase tracking-widest border border-red-500/20 px-3 py-1.5 rounded-lg hover:bg-red-500 hover:text-white transition-all">
                Salir
            </button>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-6 py-10">

    <!-- CATÁLOGO -->
    <section id="section-catalogo" class="fade-in">
        <header class="mb-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h2 class="text-4xl font-extrabold tracking-tighter uppercase italic">Equipos disponibles</h2>
                    <p id="user-greeting" class="text-zinc-500 text-sm font-medium mt-1 uppercase tracking-widest italic"></p>
                </div>
                <div class="relative">
                    <input id="buscador" type="text" placeholder="Buscar equipo..."
                           oninput="renderItems()"
                           class="pl-10 pr-4 py-2.5 rounded-xl border dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm focus:ring-2 focus:ring-indigo-500 outline-none w-64 transition-all">
                    <svg class="w-4 h-4 absolute left-3 top-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" stroke-linecap="round" d="M21 21l-4.35-4.35M17 11A6 6 0 111 11a6 6 0 0116 0z"/>
                    </svg>
                </div>
            </div>

            <!-- FILTROS POR ESTADO -->
            <div class="mt-6 flex flex-col gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mr-1">Estado:</span>
                    <button onclick="setFiltroEstado('')"             id="fest-"             class="pill-active text-[10px] font-black px-3 py-1.5 rounded-full border border-zinc-200 dark:border-zinc-700 transition-all">Todos</button>
                    <button onclick="setFiltroEstado('disponible')"    id="fest-disponible"   class="text-[10px] font-black px-3 py-1.5 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 transition-all">✅ Disponible</button>
                    <button onclick="setFiltroEstado('prestado')"      id="fest-prestado"     class="text-[10px] font-black px-3 py-1.5 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 transition-all">📦 Prestado</button>
                    <button onclick="setFiltroEstado('mantenimiento')" id="fest-mantenimiento" class="text-[10px] font-black px-3 py-1.5 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 transition-all">🔧 Mantenimiento</button>
                    <button onclick="setFiltroEstado('atrasado')"      id="fest-atrasado"     class="text-[10px] font-black px-3 py-1.5 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 transition-all">⚠️ Atrasado</button>
                </div>
                <!-- FILTROS POR CATEGORÍA (dinámicos) -->
                <div id="filtros-categoria" class="flex flex-wrap items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mr-1">Categoría:</span>
                    <button onclick="setFiltroCategoria('')" id="fcat-" class="pill-active text-[10px] font-black px-3 py-1.5 rounded-full border border-zinc-200 dark:border-zinc-700 transition-all">Todas</button>
                </div>
            </div>
            <p id="contador-resultados" class="text-[11px] text-zinc-400 font-bold mt-3 uppercase tracking-widest"></p>
        </header>

        <div id="grid-items" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"></div>

        <div id="empty-filtro" class="hidden flex flex-col items-center justify-center py-24 text-center">
            <span class="text-5xl mb-4">🔍</span>
            <h3 class="text-xl font-black uppercase italic tracking-tighter">Sin resultados</h3>
            <p class="text-zinc-400 text-sm mt-2">Prueba con otros filtros</p>
            <button onclick="resetFiltros()" class="mt-4 text-indigo-500 font-bold text-xs uppercase underline">Limpiar filtros</button>
        </div>
    </section>

    <!-- MIS PRÉSTAMOS -->
    <section id="section-mis-prestamos" class="hidden fade-in">
        <header class="mb-10">
            <h2 class="text-4xl font-extrabold tracking-tighter uppercase italic text-indigo-600">Mis Solicitudes</h2>
            <p class="text-zinc-500 text-sm font-medium">Equipos bajo tu resguardo actualmente.</p>
        </header>
        <div id="lista-prestamos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
        <div id="empty-state" class="hidden flex flex-col items-center justify-center py-20 bg-white dark:bg-zinc-900 rounded-3xl border dark:border-zinc-800">
            <span class="text-5xl mb-4">📦</span>
            <h3 class="text-xl font-black uppercase italic tracking-tighter">Sin préstamos activos</h3>
            <button onclick="switchTab('catalogo')" class="mt-4 text-indigo-500 font-bold text-xs uppercase underline">Regresar al catálogo</button>
        </div>
    </section>

    <!-- PERFIL -->
    <section id="section-perfil" class="hidden fade-in">
        <div class="max-w-xl mx-auto">
            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] p-10 border dark:border-zinc-800 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5 text-9xl font-black italic select-none">YUGI</div>
                <div class="flex items-center gap-6 mb-10 relative">
                    <div id="perfil-avatar" class="w-20 h-20 bg-indigo-600 rounded-3xl flex items-center justify-center text-3xl font-black text-white italic shadow-lg shadow-indigo-500/40"></div>
                    <div>
                        <h2 id="perfil-nombre" class="text-3xl font-black tracking-tighter uppercase leading-none mb-1">...</h2>
                        <p id="perfil-email" class="text-zinc-500 text-xs font-bold tracking-widest"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-10">
                    <div class="p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-3xl border dark:border-zinc-800">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2 italic">Reputación</p>
                        <div class="flex items-baseline gap-1">
                            <span id="perfil-reputacion" class="text-4xl font-black italic text-indigo-600">--</span>
                            <span class="text-[10px] font-black text-zinc-400">PTS</span>
                        </div>
                    </div>
                    <div class="p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-3xl border dark:border-zinc-800">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2 italic">Status</p>
                        <span id="perfil-bloqueado" class="text-xs font-black uppercase italic tracking-tighter">--</span>
                    </div>
                </div>
                <div class="p-6 bg-zinc-900 text-white dark:bg-indigo-600 rounded-3xl shadow-xl">
                    <h4 class="text-xs font-black uppercase tracking-widest mb-2 italic">Aviso de Privacidad</h4>
                    <p class="text-[10px] leading-relaxed opacity-70">Tu reputación se basa en la puntualidad y el estado físico de los equipos entregados. Mantén un puntaje alto para evitar bloqueos del sistema.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- MODAL SOLICITAR PRÉSTAMO -->
<div id="modal-prestamo" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 backdrop-blur-md p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-md p-8 rounded-[2rem] shadow-2xl border dark:border-zinc-800">
        <h2 class="text-2xl font-black italic mb-1 tracking-tighter uppercase">Solicitar Activo</h2>
        <p id="modal-item-nombre" class="text-indigo-600 text-xs font-black mb-8 tracking-widest uppercase"></p>
        <form id="form-prestamo">
            <input type="hidden" id="modal-item-id">
            <div class="mb-8">
                <label class="block text-[10px] font-black uppercase text-zinc-400 mb-3 tracking-widest">Fecha Devolución</label>
                <input type="date" id="fecha-devolucion" required
                    class="w-full p-4 bg-zinc-50 dark:bg-zinc-800 border dark:border-zinc-700 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500 font-bold">
            </div>
            <button type="submit" class="w-full py-5 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-500/30 hover:scale-[1.01] transition-all uppercase tracking-widest text-xs">Confirmar Pedido</button>
            <button type="button" onclick="cerrarModal()" class="w-full py-3 text-zinc-500 text-[10px] font-black uppercase mt-2">Cerrar</button>
        </form>
    </div>
</div>

<script>
    let todosLosItems   = [];
    let filtroEstado    = '';
    let filtroCategoria = '';

    function switchTab(tab) {
        ['catalogo','mis-prestamos','perfil'].forEach(s => {
            document.getElementById(`section-${s}`).classList.add('hidden');
            const btn = document.getElementById(`tab-${s}`);
            btn.classList.remove('nav-active');
            btn.classList.add('text-zinc-400');
        });
        document.getElementById(`section-${tab}`).classList.remove('hidden');
        const active = document.getElementById(`tab-${tab}`);
        active.classList.add('nav-active');
        active.classList.remove('text-zinc-400');
        if (tab === 'perfil')         cargarPerfil();
        if (tab === 'mis-prestamos')  cargarMisPrestamos();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const userInfo = JSON.parse(localStorage.getItem('user_info'));
        const token    = localStorage.getItem('token_prestamos');
        if (!token || !userInfo) { window.location.href = '/login'; return; }
        document.getElementById('user-greeting').innerText = `Catálogo Activo / ${userInfo.name}`;
        if (userInfo.rol === 'admin') document.getElementById('btn-admin-panel').classList.remove('hidden');
        const hoy = new Date(); hoy.setDate(hoy.getDate() + 1);
        document.getElementById('fecha-devolucion').min = hoy.toISOString().split('T')[0];
        fetchCatalogo();
        cargarMisPrestamos();
    });

    async function fetchCatalogo() {
        const container = document.getElementById('grid-items');
        container.innerHTML = Array(8).fill('<div class="skeleton h-52"></div>').join('');
        try {
            const res   = await fetch('/api/items');
            const data  = await res.json();
            todosLosItems = data.data || [];
            generarFiltrosCategorias();
            renderItems();
        } catch(e) {
            container.innerHTML = '<p class="col-span-4 text-center text-red-500 font-bold py-20">Error al cargar el catálogo</p>';
        }
    }

    function generarFiltrosCategorias() {
        const cats = [...new Set(todosLosItems.map(i => i.categoria))].sort();
        const cont = document.getElementById('filtros-categoria');
        cont.innerHTML = `
            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mr-1">Categoría:</span>
            <button onclick="setFiltroCategoria('')" id="fcat-"
                class="pill-active text-[10px] font-black px-3 py-1.5 rounded-full border border-zinc-200 dark:border-zinc-700 transition-all">Todas</button>`;
        cats.forEach(cat => {
            const btn = document.createElement('button');
            btn.onclick   = () => setFiltroCategoria(cat);
            btn.id        = `fcat-${cat}`;
            btn.className = 'text-[10px] font-black px-3 py-1.5 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 transition-all';
            btn.textContent = cat;
            cont.appendChild(btn);
        });
    }

    function setFiltroEstado(estado) {
        filtroEstado = estado;
        document.querySelectorAll('[id^="fest-"]').forEach(b => b.classList.remove('pill-active'));
        document.getElementById(`fest-${estado}`).classList.add('pill-active');
        renderItems();
    }

    function setFiltroCategoria(cat) {
        filtroCategoria = cat;
        document.querySelectorAll('[id^="fcat-"]').forEach(b => b.classList.remove('pill-active'));
        const el = document.getElementById(`fcat-${cat}`);
        if (el) el.classList.add('pill-active');
        renderItems();
    }

    function resetFiltros() {
        filtroEstado = ''; filtroCategoria = '';
        document.getElementById('buscador').value = '';
        setFiltroEstado(''); setFiltroCategoria('');
    }

    function renderItems() {
        const busqueda = (document.getElementById('buscador').value || '').toLowerCase().trim();
        let items = todosLosItems;
        if (filtroEstado)    items = items.filter(i => i.estado    === filtroEstado);
        if (filtroCategoria) items = items.filter(i => i.categoria === filtroCategoria);
        if (busqueda)        items = items.filter(i =>
            i.nombre.toLowerCase().includes(busqueda) || i.categoria.toLowerCase().includes(busqueda));

        const container = document.getElementById('grid-items');
        const emptyDiv  = document.getElementById('empty-filtro');
        document.getElementById('contador-resultados').textContent =
            `${items.length} equipo${items.length !== 1 ? 's' : ''} encontrado${items.length !== 1 ? 's' : ''}`;

        if (items.length === 0) {
            container.innerHTML = '';
            emptyDiv.classList.remove('hidden');
            return;
        }
        emptyDiv.classList.add('hidden');
        container.innerHTML = '';

        items.forEach(item => {
            const status     = item.estado.trim().toLowerCase();
            const off        = status !== 'disponible';
            const salud      = item.salud ?? Math.max(0, Math.round(100 - (item.uso_acumulado / item.vida_util_max) * 100));
            const saludColor = salud > 60 ? 'bg-green-500' : salud > 30 ? 'bg-yellow-500' : 'bg-red-500';
            const card       = document.createElement('div');
            card.className   = 'item-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border dark:border-zinc-800 shadow-sm group flex flex-col';
            card.innerHTML   = `
                <div class="flex justify-between items-start mb-4">
                    <span class="text-[9px] px-3 py-1 rounded-full font-black uppercase status-${status}">${item.estado}</span>
                    <span class="text-[9px] text-zinc-400 mono">#${item.id}</span>
                </div>
                <h3 class="text-xl font-black mt-1 tracking-tighter italic uppercase group-hover:text-indigo-600 transition-all leading-tight">${item.nombre}</h3>
                <p class="text-zinc-400 text-[10px] font-bold uppercase tracking-widest mt-1 mb-4 italic">${item.categoria}</p>
                <div class="mt-auto mb-5">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-wider">Salud</span>
                        <span class="text-[9px] font-black ${salud>60?'text-green-500':salud>30?'text-yellow-500':'text-red-500'}">${salud}%</span>
                    </div>
                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5">
                        <div class="health-bar-inner ${saludColor} h-1.5 rounded-full" style="width:${salud}%"></div>
                    </div>
                </div>
                <button onclick="abrirModal(${item.id}, '${item.nombre.replace(/'/g,"\\'")}')" ${off?'disabled':''}
                    class="w-full py-4 ${off
                        ?"bg-zinc-50 dark:bg-zinc-800 text-zinc-300 cursor-not-allowed"
                        :"bg-zinc-950 dark:bg-white text-white dark:text-black hover:bg-indigo-600 hover:text-white"
                    } rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] transition-all">
                    ${off ? 'No Disponible' : 'Solicitar'}
                </button>`;
            container.appendChild(card);
        });
    }

    async function cargarPerfil() {
        const token     = localStorage.getItem('token_prestamos');
        const localUser = JSON.parse(localStorage.getItem('user_info'));
        try {
            const res  = await fetch('/api/me', { headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'} });
            const data = await res.json();
            const user = data.data || localUser;
            document.getElementById('perfil-nombre').innerText     = user.name       || localUser.name;
            document.getElementById('perfil-email').innerText      = user.email      || localUser.email;
            document.getElementById('perfil-reputacion').innerText = user.reputacion !== undefined ? user.reputacion : '--';
            document.getElementById('perfil-avatar').innerText     = (user.name||'U').charAt(0).toUpperCase();
            const lbl = document.getElementById('perfil-bloqueado');
            lbl.innerText  = user.bloqueado ? '🛑 BLOQUEADO' : '✅ ACTIVO';
            lbl.className  = user.bloqueado ? 'text-xs font-black text-red-500 italic' : 'text-xs font-black text-green-500 italic';
        } catch(e) {
            document.getElementById('perfil-nombre').innerText = localUser.name;
            document.getElementById('perfil-email').innerText  = localUser.email;
        }
    }

    async function cargarMisPrestamos() {
        const token      = localStorage.getItem('token_prestamos');
        const lista      = document.getElementById('lista-prestamos');
        const emptyState = document.getElementById('empty-state');
        const badge      = document.getElementById('badge-prestamos');
        try {
            const res      = await fetch('/api/me', { headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'} });
            const data     = await res.json();
            const prestamos= data.data?.prestamos_activos || [];
            if (prestamos.length > 0) {
                badge.innerText = prestamos.length;
                badge.classList.remove('hidden');
                emptyState.classList.add('hidden');
                lista.innerHTML = '';
                prestamos.forEach(p => {
                    lista.innerHTML += `
                        <div class="bg-indigo-600 text-white p-8 rounded-[2rem] shadow-xl">
                            <p class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-2 italic">Equipo Solicitado</p>
                            <h3 class="text-2xl font-black italic tracking-tighter mb-6 uppercase">${p.item.nombre}</h3>
                            <div class="bg-white/10 p-3 rounded-xl text-[10px] font-bold mb-8 uppercase tracking-tighter italic">
                                Devolución: ${p.fecha_devolucion_esperada}
                            </div>
                            <button onclick="devolverObjeto(${p.id})"
                                class="w-full py-4 bg-white text-indigo-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-50 transition-all">
                                Finalizar Entrega
                            </button>
                        </div>`;
                });
            } else {
                lista.innerHTML = '';
                emptyState.classList.remove('hidden');
                badge.classList.add('hidden');
            }
        } catch(e) { console.error(e); }
    }

    document.getElementById('form-prestamo').addEventListener('submit', async (e) => {
        e.preventDefault();
        const token = localStorage.getItem('token_prestamos');
        const payload = { item_id: document.getElementById('modal-item-id').value,
                          fecha_devolucion_esperada: document.getElementById('fecha-devolucion').value };
        const res = await fetch('/api/loans', {
            method:'POST',
            headers:{'Content-Type':'application/json','Authorization':`Bearer ${token}`,'Accept':'application/json'},
            body: JSON.stringify(payload)
        });
        if (res.ok) { cerrarModal(); cargarMisPrestamos(); fetchCatalogo(); switchTab('mis-prestamos'); }
        else { const d = await res.json(); alert('ERROR: ' + d.message); }
    });

    async function devolverObjeto(loanId) {
        const token = localStorage.getItem('token_prestamos');
        if (!confirm('¿Confirmas la entrega física del equipo?')) return;
        const res = await fetch(`/api/loans/${loanId}`, {
            method:'PUT',
            headers:{'Authorization':`Bearer ${token}`,'Content-Type':'application/json','Accept':'application/json'},
            body: JSON.stringify({ estado_fisico_entrada:'bueno' })
        });
        if (res.ok) { cargarMisPrestamos(); fetchCatalogo(); }
    }

    function abrirModal(id, nombre) {
        document.getElementById('modal-item-id').value = id;
        document.getElementById('modal-item-nombre').innerText = nombre;
        document.getElementById('modal-prestamo').classList.replace('hidden','flex');
    }
    function cerrarModal() {
        document.getElementById('modal-prestamo').classList.replace('flex','hidden');
    }

    // LOGOUT CORRECTO — revoca el token en el servidor
    async function logout() {
        const token = localStorage.getItem('token_prestamos');
        try {
            await fetch('/api/logout', {
                method:'POST',
                headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}
            });
        } catch(e) { /* si falla la red, igual limpiamos */ }
        localStorage.clear();
        window.location.href = '/login';
    }
</script>
</body>
</html>
