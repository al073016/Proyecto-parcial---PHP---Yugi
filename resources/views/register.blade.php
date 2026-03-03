<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Sistema de Préstamos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-zinc-950 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md bg-white dark:bg-zinc-900 p-8 rounded-3xl shadow-xl border border-zinc-200 dark:border-zinc-800">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">Crear Cuenta</h1>
            <p class="text-zinc-500 mt-2">Regístrate como alumno para empezar</p>
        </div>

        <form id="register-form" class="space-y-4">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Nombre Completo</label>
                <input type="text" id="name" required placeholder="Juan Pérez" minlength="3"
                    class="w-full p-4 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Correo Electrónico</label>
                <input type="email" id="email" required placeholder="ejemplo@correo.com"
                    class="w-full p-4 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Contraseña (min. 8)</label>
                    <input type="password" id="password" required placeholder="••••••••" minlength="8"
                        class="w-full p-4 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Confirmar</label>
                    <input type="password" id="password_confirmation" required placeholder="••••••••"
                        class="w-full p-4 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>

            <button type="submit" id="btn-register"
                class="w-full py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 active:scale-[0.98] transition-all shadow-lg shadow-indigo-500/20 mt-4">
                Crear mi cuenta
            </button>
        </form>

        <div id="error-container" class="mt-4 hidden p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-2xl">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p id="error-msg" class="text-red-600 dark:text-red-400 text-[11px] font-bold uppercase"></p>
            </div>
        </div>

        <p class="text-center mt-6 text-sm text-zinc-500 font-medium">
            ¿Ya tienes cuenta? 
            <a href="/login" class="text-indigo-500 font-bold hover:underline transition-all">Inicia sesión</a>
        </p>
    </div>

    <script>
        document.getElementById('register-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            
            const errorContainer = document.getElementById('error-container');
            const errorMsg = document.getElementById('error-msg');
            const btn = document.getElementById('btn-register');

            // 1. VALIDACIÓN DE FORMATO DE CORREO (RegEx)
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError("El formato del correo no es válido (ejemplo@correo.com)");
                return;
            }

            // 2. VALIDACIÓN DE LONGITUD DE CONTRASEÑA
            if (password.length < 8) {
                showError("La contraseña debe tener al menos 8 caracteres");
                return;
            }

            // 3. VALIDACIÓN DE COINCIDENCIA
            if (password !== password_confirmation) {
                showError("Las contraseñas no coinciden");
                return;
            }

            errorContainer.classList.add('hidden');
            btn.innerText = "Sincronizando...";
            btn.disabled = true;

            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: { 
                        'Accept': 'application/json', 
                        'Content-Type': 'application/json' 
                    },
                    body: JSON.stringify({ 
                        name, 
                        email, 
                        password, 
                        password_confirmation 
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    const token = data.access_token; 
                    localStorage.clear();
                    localStorage.setItem('token_prestamos', token); 
                    localStorage.setItem('user_info', JSON.stringify(data.user));
                    
                    window.location.href = '/catalogo';
                } else {
                    // Laravel devuelve errores de validación en data.errors si el email ya existe
                    let mensaje = data.message;
                    if(data.errors && data.errors.email) mensaje = "Este correo ya está registrado";
                    showError(mensaje || "Error en los datos proporcionados");
                }
            } catch (error) {
                showError("Sin conexión con el servidor");
            } finally {
                btn.innerText = "Crear mi cuenta";
                btn.disabled = false;
            }
        });

        function showError(msg) {
            const container = document.getElementById('error-container');
            const text = document.getElementById('error-msg');
            text.innerText = msg;
            container.classList.replace('hidden', 'block');
        }
    </script>
</body>
</html>