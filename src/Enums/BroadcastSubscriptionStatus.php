<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Enums;

enum BroadcastSubscriptionStatus: string
{
    case Subscribed = 'subscribed';

    case Unsubscribed = 'unsubscribed';
}
