<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    // REGISTRO DE SERVICIOS (REGISTER)
    // Se utiliza para enlazar clases o dependencias al contenedor de Laravel. 
    // Importante: No se debe usar para ejecutar código o llamar a otros servicios aquí.

    public function register(): void
    {
        //
    }


    // INICIALIZACIÓN DE SERVICIOS (BOOT)
    // Se ejecuta después de que todos los servicios hayan sido registrados.
    // Es el lugar ideal para configuraciones globales, observadores de modelos o macros.

    public function boot(): void
    {
        //
    }
}