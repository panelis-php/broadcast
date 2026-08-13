<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Panel\Resources\BroadcastResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\Response;
use Panelis\Broadcast\Actions\SendBroadcast;
use Panelis\Broadcast\Enums\BroadcastStatus;
use Panelis\Broadcast\Models\Broadcast;
use Panelis\Broadcast\Panel\Resources\BroadcastResource;
use Panelis\Broadcast\Panel\Resources\BroadcastResource\Enums\BroadcastPermission;

class EditBroadcast extends EditRecord
{
    protected static string $resource = BroadcastResource::class;

    protected bool $sendImmediately = false;

    public function authorizeAccess(): void
    {
        abort_unless(user_can(BroadcastPermission::Edit), Response::HTTP_FORBIDDEN);

        abort_unless($this->record->isDraft(), Response::HTTP_FORBIDDEN);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => $this->record->isDraft() && user_can(BroadcastPermission::Delete)),
        ];
    }

    /**
     * Matikan notifikasi "saved" bawaan Filament agar tidak dobel
     * dengan notifikasi sukses kirim/simpan di afterSave().
     */
    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['save_as_draft'] = $this->record->isDraft();
        $data['send_now'] = false;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function afterSave(): void
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
