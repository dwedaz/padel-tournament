<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'wasit/referee/*/update-serving',
            'wasit/referee/*/update-score',
            'wasit/referee/*/update-tiebreak-score',
            'wasit/referee/*/end-game',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
