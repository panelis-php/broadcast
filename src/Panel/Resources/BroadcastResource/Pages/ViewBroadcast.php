<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Panel\Resources\BroadcastResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Http\Response;
use Panelis\Broadcast\Enums\BroadcastStatus;
use Panelis\Broadcast\Enums\BroadcastType;
use Panelis\Broadcast\Models\Broadcast;
use Panelis\Broadcast\Panel\Resources\BroadcastResource;
use Panelis\Broadcast\Panel\Resources\BroadcastResource\Enums\BroadcastPermission;

class ViewBroadcast extends ViewRecord
{
    protected static string $resource = BroadcastResource::class;

    public function authorizeAccess(): void
    {
        abort_unless(user_can(BroadcastPermission::Browse), Response::HTTP_FORBIDDEN);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn (): bool => $this->record->isDraft() && user_can(BroadcastPermission::Edit)),

            DeleteAction::make()
                ->visible(fn (): bool => $this->record->isDraft() && user_can(BroadcastPermission::Delete)),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make(__('broadcast::broadcast.section.message'))
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('title')
                            ->hiddenLabel()
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold),

                        TextEntry::make('body')
                            ->hiddenLabel()
                            ->markdown(),
                    ]),

                Section::make(__('broadcast::broadcast.section.details'))
                    ->columnSpan(1)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('type')
                            ->label(__('broadcast::broadcast.column.type'))
                            ->badge()
                            ->formatStateUsing(fn (mixed $state): string => __('broadcast::broadcast.type.'.($state instanceof BroadcastType ? $state->value : $state))),

                        TextEntry::make('status')
                            ->label(__('broadcast::broadcast.column.status'))
                            ->badge()
                            ->formatStateUsing(fn (mixed $state): string => __('broadcast::broadcast.status.'.($state instanceof BroadcastStatus ? $state->value : $state))),

                        TextEntry::make('channels')
                            ->label(__('broadcast::broadcast.column.channels'))
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => __("broadcast::broadcast.channel.{$state}")),

                        TextEntry::make('url')
                            ->label(__('broadcast::broadcast.form.url'))
                            ->visible(fn (Broadcast $record): bool => filled($record->url))
                            ->url(fn (Broadcast $record): string => $record->url, true),

                        TextEntry::make('label')
                            ->label(__('broadcast::broadcast.form.label'))
                            ->visible(fn (Broadcast $record): bool => filled($record->label)),

                        TextEntry::make('roles')
                            ->label(__('broadcast::broadcast.column.roles'))
                            ->badge()
                            ->state(fn (Broadcast $record): array => ($record->roles->isEmpty() && $record->users->isEmpty())
                                ? [__('broadcast::broadcast.all_users')]
                                : $record->roles->pluck('name')->all()),

                        TextEntry::make('users')
                            ->label(__('broadcast::broadcast.column.users'))
                            ->badge()
                            ->state(fn (Broadcast $record): array => $record->users->pluck('name')->all()),

                        TextEntry::make('send_at')
                            ->label(__('broadcast::broadcast.column.send_at'))
                            ->dateTime(config('app.datetime_format'))
                            ->placeholder(__('broadcast::broadcast.now')),

                        TextEntry::make('sent_at')
                            ->label(__('broadcast::broadcast.column.sent_at'))
                            ->dateTime(config('app.datetime_format'))
                            ->placeholder(__('broadcast::broadcast.now')),

                        TextEntry::make('created_at')
                            ->label(__('broadcast::broadcast.column.created_at'))
                            ->dateTime(config('app.datetime_format')),
                    ]),
            ]);
    }
}
