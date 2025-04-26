<?php

namespace App\Listeners;

class FlushTranslatorLoaded
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if (! $event->sandbox->resolved('translator')) {
            return;
        }

        $translator = $event->sandbox->make('translator');
        $translator->setLoaded([]);
    }
}
