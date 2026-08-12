<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Enums;

enum BroadcastStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Sent = 'sent';
}
