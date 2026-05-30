<?php

use Illuminate\Support\Facades\Route;

// Serve the React SPA for everything except API, health and storage routes.
Route::get('/{any?}', fn () => view('app'))
    ->where('any', '^(?!api|up|storage).*$');
