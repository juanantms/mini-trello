<?php

namespace App\Listeners;

use App\Models\AuditTrail;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if ($event->user) {
            AuditTrail::create([
                'user_id' => $event->user->id,
                'action' => 'logout',
                'model_type' => get_class($event->user),
                'model_id' => $event->user->id,
                'old_data' => null,
                'new_data' => [
                    'ip' => request()->ip(),
                ],
            ]);
        }
    }
}
