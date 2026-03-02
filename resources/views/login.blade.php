<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema de Préstamos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-zinc-950 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md bg-white dark:bg-zinc-900 p-8 rounded-3xl shadow-xl border border-zinc-200 dark:border-zinc-800">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">Bienvenido</h1>
            <p class="text-zinc-500 mt-2">Ingresa tus datos para gestionar tus préstamos</p>
        </div>

        <form id="login-form" class="space-y-6">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Correo Electrónico</label>
                <input type="email" id="email" required placeholder="ejemplo@correo.com"
                    class="w-full p-4 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white transition-all">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Contraseña</label>
                <input type="password" id="password" required placeholder="••••••••"
                    class="w-full p-4 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white transition-all">
            </div>

            <button type="submit" id="btn-login"
                class="w-full py-4 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-bold rounded-2xl hover:opacity-90 active:scale-[0.98] transition-all shadow-lg">
                Iniciar Sesión
            </button>
        </form>

        <div id="error-container" class="mt-4 hidden p-3 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl">
            <p id="error-msg" class="text-red-600 dark:text-red-400 text-xs text-center font-bold"></p>
        </div>

        <div class="mt-8 pt-6 border-t border-zinc-100 dark:border-zinc-800 text-center">
            <p class="text-sm text-zinc-500">
                ¿No tienes una cuenta todavía? <br>
                <a href="/register" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline transition-all mt-2 inline-block">
                    Crea una cuenta de alumno
                </a>
            </p>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorContainer = document.getElementById('error-container');
            const errorMsg = document.getElementById('error-msg');
            const btn = document.getElementById('btn-login');

            // Limpiar estado previo
            errorContainer.classList.add('hidden');
            btn.innerText = "Cargando...";
            btn.disabled = true;

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 
                        'Accept': 'application/json', 
                        'Content-Type': 'application/json' 
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok) {
                    const token = data.access_token; 

                    if (token) {
                        localStorage.clear();
                        localStorage.setItem('token_prestamos', token); 
                        localStorage.setItem('user_info', JSON.stringify(data.user));
                        
                        window.location.href = '/catalogo';
                    } else {
                        throw new Error("El servidor no envió el 'access_token'.");
                    }
                } else {
                    errorMsg.innerText = data.message || "Credenciales incorrectas";
                    errorContainer.classList.remove('hidden');
                }
            } catch (error) {
                console.error("Error Login:", error);
                errorMsg.innerText = "Error de conexión o datos inválidos";
                errorContainer.classList.remove('hidden');
            } finally {
                btn.innerText = "Iniciar Sesión";
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>