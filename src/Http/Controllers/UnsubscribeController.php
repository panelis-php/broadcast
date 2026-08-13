<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Http\Controllers;

use Illuminate\Contracts\View\View;
use Panelis\Broadcast\Enums\BroadcastChannel;

class UnsubscribeController
{
    /**
     * Unsubscribe a user from broadcast emails without requiring login.
     * The URL is protected by a signed signature (see routes/web.php).
     */
    public function __invoke(string $user): View
    {
        $userModel = config('auth.providers.users.model');

        $user = $userModel::query()->findOrFail($user);

        broadcast_unsubscribe(BroadcastChannel::Mail, $user);

        return view('broadcast::unsubscribe', ['user' => $user]);
    }
}
