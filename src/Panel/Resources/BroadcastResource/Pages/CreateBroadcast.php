<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Panel\Resources\BroadcastResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\Response;
use Panelis\Broadcast\Actions\SendBroadcast;
use Panelis\Broadcast\Enums\BroadcastStatus;
use Panelis\Broadcast\Models\Broadcast;
use Panelis\Broadcast\Panel\Resources\BroadcastResource;
use Panelis\Broadcast\Panel\Resources\BroadcastResource\Enums\BroadcastPermission;

class CreateBroadcast extends CreateRecord
{
    protected static string $resource = BroadcastResource::class;

    protected bool $sendImmediately = false;

    public function authorizeAccess(): void
    {
        abort_unless(user_can(BroadcastPermission::Create), Response::HTTP_FORBIDDEN);
    }

    /**
     * Matikan notifikasi "created" bawaan Filament agar tidak dobel
     * dengan notifikasi sukses kirim/simpan di afterCreate().
     */
    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $isDraft = (bool) ($data['save_as_draft'] ?? false);
        $sendNow = (bool) ($data['send_now'] ?? true);

        $this->sendImmediately = ! $isDraft && $sendNow;

        $data['status'] = $isDraft || $sendNow
            ? BroadcastStatus::Draft
            : BroadcastStatus::Scheduled;

        if ($isDraft || $sendNow) {
            $data['send_at'] = null;
        }

        unset($data['send_now'], $data['save_as_draft']);

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Broadcast $record */
        $record = $this->record;

        if ($this->sendImmediately) {
            SendBroadcast::run($record);

            Notification::make()
                ->title(__('broadcast::broadcast.notifications.success.title'))
                ->body(__('broadcast::broadcast.notifications.success.body'))
                ->success()
                ->send();

            return;
        }

        Notification::make()
            ->title(__('broadcast::broadcast.notifications.saved.title'))
            ->body(__('broadcast::broadcast.notifications.saved.body'))
            ->success()
            ->send();
    }
}
