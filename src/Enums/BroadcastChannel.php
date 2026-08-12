<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Enums;

enum BroadcastChannel: string
{
    case Database = 'database';

    case Mail = 'mail';
}
