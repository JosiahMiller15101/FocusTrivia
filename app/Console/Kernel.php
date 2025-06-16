<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // ...existing commands...
    ];

    protected function schedule(Schedule $schedule)
    {
        // ...existing schedule...
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        // No need to call $this->commands([...]) unless you have commands outside the default path
    }
}