<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    // REGISTRO DE SERVICIOS (REGISTER)
    // Se utiliza para enlazar clases o dependencias al contenedor de Laravel. 

    public function register(): void
    {
        //
    }


    // INICIALIZACIÓN DE SERVICIOS (BOOT)
    // Se ejecuta después de que todos los servicios hayan sido registrados.

    public function boot(): void
    {
        //
    }
}