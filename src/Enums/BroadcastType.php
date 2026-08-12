<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Enums;

enum BroadcastType: string
{
    case Info = 'info';

    case Warning = 'warning';

    case Success = 'success';

    case Error = 'error';
}
