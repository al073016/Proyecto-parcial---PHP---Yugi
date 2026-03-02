<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yugi | Sistema de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        .status-disponible { background: #ecfdf5; color: #059669; border: 1px solid #10b98133; }
        .status-prestado { background: #fff7ed; color: #d97706; border: 1px solid #f59e0b33; }
        .status-mantenimiento { background: #fef2f2; color: #dc2626; border: 1px solid #ef444433; }
        .nav-active { color: #4f46e5 !important; border-bottom: 2px solid #4f46e5; }
        
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-slate-50 dark:bg-zinc-950 min-h-screen text-zinc-900 dark:text-zinc-100">

    <nav class="sticky top-0 z-40 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border-b dark:border-zinc-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
            <div class="flex items-center gap-8">
                <h1 class="text-2xl font-black italic tracking-tighter">YUGI<span class="text-indigo-600">.</span></h1>
                
                <div class="hidden md:flex gap-6 h-16">
                    <button onclick="switchTab('catalogo')" id="tab-catalogo" class="nav-active text-[10px] font-black uppercase tracking-widest px-2 transition-all">
                        Catálogo
                    </button>
                    <button onclick="switchTab('mis-prestamos')" id="tab-mis-prestamos" class="text-zinc-400 hover:text-indigo-500 text-[10px] font-black uppercase tracking-widest px-2 transition-all flex items-center gap-2">
                        Mis Solicitudes
                        <span id="badge-prestamos" class="hidden bg-indigo-600 text-white text-[9px] px-1.5 py-0.5 rounded-full">0</span>
                    </button>
                    <button onclick="switchTab('perfil')" id="tab-perfil" class="text-zinc-400 hover:text-indigo-500 text-[10px] font-black uppercase tracking-widest px-2 transition-all">
                        Mi Perfil
                    </button>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <a id="btn-admin-panel" href="/admin/dashboard" class="hidden bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white text-[10px] font-black px-4 py-2 rounded-xl">
                    ADMIN
                </a>
                <button onclick="logout()" class="text-[10px] font-black text-red-500 uppercase tracking-widest border border-red-500/20 px-3 py-1.5 rounded-lg hover:bg-red-500 hover:text-white transition-all">Salir</button>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-10">
        
        <section id="section-catalogo" class="fade-in">
            <header class="mb-10 text-center md:text-left">
                <h2 class="text-4xl font-black tracking-tighter uppercase italic">Equipos disponibles</h2>
                <p id="user-greeting" class="text-zinc-500 text-sm font-medium mt-1 uppercase tracking-widest italic"></p>
            </header>
            <div id="grid-items" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"></div>
        </section>

        <section id="section-mis-prestamos" class="hidden fade-in">
            <header class="mb-10 text-center md:text-left">
                <h2 class="text-4xl font-black tracking-tighter uppercase italic text-indigo-600">Mis Solicitudes</h2>
                <p class="text-zinc-500 text-sm font-medium">Equipos bajo tu resguardo actualmente.</p>
            </header>
            <div id="lista-prestamos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
            <div id="empty-state" class="hidden flex flex-col items-center justify-center py-20 bg-white dark:bg-zinc-900 rounded-3xl border dark:border-zinc-800">
                <span class="text-5xl mb-4 italic">📦</span>
                <h3 class="text-xl font-black uppercase italic tracking-tighter">Sin préstamos activos</h3>
                <button onclick="switchTab('catalogo')" class="mt-4 text-indigo-500 font-bold text-xs uppercase underline">Regresar al catálogo</button>
            </div>
        </section>

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

    <div id="modal-prestamo" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 backdrop-blur-md p-4">
        <div class="bg-white dark:bg-zinc-900 w-full max-w-md p-8 rounded-[2rem] shadow-2xl border dark:border-zinc-800">
            <h2 class="text-2xl font-black italic mb-1 tracking-tighter uppercase">Solicitar Activo</h2>
            <p id="modal-item-nombre" class="text-indigo-600 text-xs font-black mb-8 tracking-widest uppercase"></p>
            <form id="form-prestamo">
                <input type="hidden" id="modal-item-id">
                <div class="mb-8">
                    <label class="block text-[10px] font-black uppercase text-zinc-400 mb-3 tracking-widest">Fecha Devolución</label>
                    <input type="date" id="fecha-devolucion" required class="w-full p-4 bg-zinc-50 dark:bg-zinc-800 border dark:border-zinc-700 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500 font-bold">
                </div>
                <button type="submit" class="w-full py-5 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-500/30 hover:scale-[1.01] transition-all uppercase tracking-widest text-xs">Confirmar Pedido</button>
                <button type="button" onclick="cerrarModal()" class="w-full py-3 text-zinc-500 text-[10px] font-black uppercase mt-2">Cerrar</button>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            const sections = ['section-catalogo', 'section-mis-prestamos', 'section-perfil'];
            const tabs = ['tab-catalogo', 'tab-mis-prestamos', 'tab-perfil'];
            sections.forEach(s => document.getElementById(s).classList.add('hidden'));
            tabs.forEach(t => {
                const el = document.getElementById(t);
                el.classList.remove('nav-active');
                el.classList.add('text-zinc-400');
            });
            document.getElementById(`section-${tab}`).classList.remove('hidden');
            const activeTab = document.getElementById(`tab-${tab}`);
            activeTab.classList.add('nav-active');
            activeTab.classList.remove('text-zinc-400');
            if (tab === 'perfil') cargarPerfil();
            if (tab === 'mis-prestamos') cargarMisPrestamos();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const userInfo = JSON.parse(localStorage.getItem('user_info'));
            const token = localStorage.getItem('token_prestamos');
            if (!token || !userInfo) { window.location.href = '/login'; return; }
            document.getElementById('user-greeting').innerText = `Catálogo Activo / ${userInfo.name}`;
            if (userInfo.rol === 'admin') document.getElementById('btn-admin-panel').classList.remove('hidden');
            const hoy = new Date(); hoy.setDate(hoy.getDate() + 1);
            document.getElementById('fecha-devolucion').min = hoy.toISOString().split('T')[0];
            fetchCatalogo();
            cargarMisPrestamos();
        });

        async function cargarPerfil() {
            const token = localStorage.getItem('token_prestamos');
            const localUser = JSON.parse(localStorage.getItem('user_info'));
            try {
                const response = await fetch('/api/me', { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }});
                const result = await response.json();
                
                // CORRECCIÓN DEL ERROR: Manejo flexible de la respuesta de la API
                const user = (result.data && result.data.user) ? result.data.user : (result.user ? result.user : localUser);

                document.getElementById('perfil-nombre').innerText = user.name || "Usuario";
                document.getElementById('perfil-email').innerText = user.email || "";
                document.getElementById('perfil-reputacion').innerText = user.reputacion !== undefined ? user.reputacion : "--";
                document.getElementById('perfil-avatar').innerText = (user.name || "U").charAt(0).toUpperCase();
                
                const statusLabel = document.getElementById('perfil-bloqueado');
                statusLabel.innerText = user.bloqueado ? "🛑 BLOQUEADO" : "✅ ACTIVO";
                statusLabel.className = user.bloqueado ? "text-xs font-black text-red-500 italic" : "text-xs font-black text-green-500 italic";
            } catch (e) { 
                console.error("Error cargando perfil:", e);
                // Fallback a localStorage si falla la API
                document.getElementById('perfil-nombre').innerText = localUser.name;
                document.getElementById('perfil-email').innerText = localUser.email;
            }
        }

        async function cargarMisPrestamos() {
            const token = localStorage.getItem('token_prestamos');
            const lista = document.getElementById('lista-prestamos');
            const emptyState = document.getElementById('empty-state');
            const badge = document.getElementById('badge-prestamos');
            try {
                const response = await fetch('/api/me', { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }});
                const result = await response.json();
                const prestamos = (result.data && result.data.prestamos_activos) ? result.data.prestamos_activos : [];

                if (prestamos.length > 0) {
                    badge.innerText = prestamos.length;
                    badge.classList.remove('hidden');
                    emptyState.classList.add('hidden');
                    lista.innerHTML = '';
                    prestamos.forEach(p => {
                        lista.innerHTML += `
                            <div class="bg-indigo-600 text-white p-8 rounded-[2rem] shadow-xl relative overflow-hidden group">
                                <div class="relative z-10">
                                    <p class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-2 italic">Equipo Solicitado</p>
                                    <h3 class="text-2xl font-black italic tracking-tighter mb-6 uppercase">${p.item.nombre}</h3>
                                    <div class="bg-white/10 p-3 rounded-xl text-[10px] font-bold mb-8 uppercase tracking-tighter italic">Devolución: ${p.fecha_devolucion_esperada}</div>
                                    <button onclick="devolverObjeto(${p.id})" class="w-full py-4 bg-white text-indigo-600 rounded-2xl text-[10px] font-black uppercase tracking-widest">Finalizar Entrega</button>
                                </div>
                            </div>`;
                    });
                } else {
                    lista.innerHTML = ''; emptyState.classList.remove('hidden'); badge.classList.add('hidden');
                }
            } catch (e) { console.error(e); }
        }

        async function fetchCatalogo() {
            try {
                const response = await fetch('/api/items');
                const result = await response.json();
                const container = document.getElementById('grid-items');
                container.innerHTML = ''; 
                result.data.forEach(item => {
                    const status = item.estado.trim().toLowerCase();
                    const off = status !== 'disponible';
                    container.innerHTML += `
                        <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border dark:border-zinc-800 shadow-sm transition-all hover:shadow-xl group">
                            <span class="text-[9px] px-3 py-1 rounded-full font-black uppercase status-${status}">${item.estado}</span>
                            <h3 class="text-xl font-black mt-4 tracking-tighter italic uppercase group-hover:text-indigo-600 transition-all">${item.nombre}</h3>
                            <p class="text-zinc-400 text-[10px] font-bold uppercase tracking-widest mt-1 mb-8 italic">${item.categoria}</p>
                            <button onclick="abrirModal(${item.id}, '${item.nombre}')" ${off ? 'disabled' : ''}
                                class="w-full py-4 ${off ? 'bg-zinc-50 text-zinc-300' : 'bg-zinc-950 dark:bg-white text-white dark:text-black hover:bg-indigo-600 hover:text-white'} rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] transition-all">
                                ${off ? 'No Disponible' : 'Solicitar'}
                            </button>
                        </div>`;
                });
            } catch (e) { console.error(e); }
        }

        document.getElementById('form-prestamo').addEventListener('submit', async (e) => {
            e.preventDefault();
            const token = localStorage.getItem('token_prestamos');
            const payload = { item_id: document.getElementById('modal-item-id').value, fecha_devolucion_esperada: document.getElementById('fecha-devolucion').value };
            const response = await fetch('/api/loans', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }, body: JSON.stringify(payload) });
            if (response.ok) { cerrarModal(); cargarMisPrestamos(); fetchCatalogo(); switchTab('mis-prestamos'); } 
            else { const res = await response.json(); alert("ERROR: " + res.message); }
        });

        async function devolverObjeto(loanId) {
            const token = localStorage.getItem('token_prestamos');
            if(!confirm("¿Confirmas la entrega física del equipo?")) return;
            const response = await fetch(`/api/loans/${loanId}`, { method: 'PUT', headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ estado_fisico_entrada: 'bueno' }) });
            if (response.ok) { cargarMisPrestamos(); fetchCatalogo(); }
        }

        function abrirModal(id, nombre) {
            document.getElementById('modal-item-id').value = id;
            document.getElementById('modal-item-nombre').innerText = nombre;
            document.getElementById('modal-prestamo').classList.replace('hidden', 'flex');
        }
        function cerrarModal() { document.getElementById('modal-prestamo').classList.replace('flex', 'hidden'); }
        function logout() { localStorage.clear(); window.location.href = '/login'; }
    </script>
</body>
</html>