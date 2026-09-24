<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
// Importa la fachada del cliente HTTP para bloquear las peticiones reales.
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    /**
     * Prepara cada prueba impidiendo que se llame al backend real.
     */
    protected function setUp(): void
    {
        // Ejecuta la preparación estándar de Laravel.
        parent::setUp();

        // Hace fallar cualquier petición HTTP que no haya sido simulada con Http::fake().
        Http::preventStrayRequests();

        // Evita que las vistas necesiten el manifiesto compilado de Vite.
        $this->withoutVite();
    }
}
