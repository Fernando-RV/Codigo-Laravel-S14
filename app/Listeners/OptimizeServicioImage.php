<?php

namespace App\Listeners;

use App\Events\ServicioSaved;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OptimizeServicioImage implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $image = Image::make(Storage::get($event->servicio->image))
            ->widen(600) //Redimensionamos la imagen a 600 px
            ->limitColors(255) //Limitamos el color a 255
            ->encode(); //Volvemos a codificar la nueva imagen
        //Sobreescribimos la misma imagen con la nueva imagen redimensionada
        Storage::put($event->servicio->image, (string) $image);
        
    }
}
