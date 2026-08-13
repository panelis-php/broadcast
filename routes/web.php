<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Panelis\Broadcast\Http\Controllers\UnsubscribeController;

Route::get('/broadcast/unsubscribe/{user}', UnsubscribeController::class)
    ->middleware('signed')
    ->name('broadcast.unsubscribe');
