<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Panel\Resources\BroadcastResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Response;
use Panelis\Broadcast\Panel\Resources\BroadcastResource;
use Panelis\Broadcast\Panel\Resources\BroadcastResource\Enums\BroadcastPermission;

class ListBroadcasts extends ListRecords
{
    protected static string $resource = BroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->visible(user_can(BroadcastPermission::Create)),
        ];
    }

    public function authorizeAccess(): void
    {
        abort_unless(user_can(BroadcastPermission::Browse), Response::HTTP_FORBIDDEN);
    }
}
