<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Panel\Resources\BroadcastResource\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum BroadcastPermission: string implements HasLabel
{
    case Browse = 'BrowseBroadcast';

    case Create = 'CreateBroadcast';

    case Edit = 'EditBroadcast';

    case Delete = 'DeleteBroadcast';

    public function getLabel(): string
    {
        return __(sprintf('broadcast::permission.name_%s', Str::snake($this->value)));
    }
}
