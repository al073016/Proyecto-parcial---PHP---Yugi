<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Cron Job: Procesa sanciones automáticas cada medianoche
// Para activar el scheduler en producción, agrega esto al crontab del servidor:
// * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
Schedule::command('prestamos:procesar-sanciones')->dailyAt('00:00');
