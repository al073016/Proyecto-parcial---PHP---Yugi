<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Console\Command;

class ProcesarSanciones extends Command
{
    /**
     * The name and signature of the console command.
     * Se ejecuta con: php artisan prestamos:procesar-sanciones
     */
    protected $signature = 'prestamos:procesar-sanciones';

    protected $description = 'Detecta préstamos atrasados, bloquea usuarios y actualiza estados a ATRASADO. Corre cada medianoche.';

    public function handle(): void
    {
        $this->info('🔍 Iniciando procesamiento de sanciones...');

        // Busca préstamos donde la fecha esperada ya pasó y NO han sido devueltos
        $prestamosAtrasados = Loan::with(['item', 'user'])
            ->whereNull('fecha_devolucion_real')
            ->where('fecha_devolucion_esperada', '<', now())
            ->get();

        if ($prestamosAtrasados->isEmpty()) {
            $this->info('✅ No hay préstamos atrasados. Todo en orden.');
            return;
        }

        $this->info("⚠️  Se encontraron {$prestamosAtrasados->count()} préstamo(s) atrasado(s).");

        $usuariosAfectados = collect();

        foreach ($prestamosAtrasados as $loan) {
            // 1. Cambiar estado del ítem a 'atrasado'
            if ($loan->item && $loan->item->estado !== 'atrasado') {
                $loan->item->update(['estado' => 'atrasado']);
                $this->line("  📦 Objeto '{$loan->item->nombre}' marcado como ATRASADO.");
            }

            // 2. Bloquear al usuario responsable
            if ($loan->user && !$loan->user->bloqueado) {
                $loan->user->update(['bloqueado' => true]);
                $usuariosAfectados->push($loan->user->name);
                $this->line("  🚫 Usuario '{$loan->user->name}' BLOQUEADO.");
            }
        }

        $this->info("✅ Procesamiento finalizado.");
        $this->info("   → Préstamos marcados como atrasados: {$prestamosAtrasados->count()}");
        $this->info("   → Usuarios bloqueados: {$usuariosAfectados->unique()->count()}");
    }
}
