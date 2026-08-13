<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Panel\Resources\BroadcastResource\Enums;

enum BroadcastPermission: string
{
    case Browse = 'BrowseBroadcast';

    case Create = 'CreateBroadcast';

    case Edit = 'EditBroadcast';

    case Delete = 'DeleteBroadcast';
}
