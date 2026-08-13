<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Panel\Resources;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Panelis\Broadcast\Actions\SendBroadcast;
use Panelis\Broadcast\Enums\BroadcastChannel;
use Panelis\Broadcast\Enums\BroadcastStatus;
use Panelis\Broadcast\Enums\BroadcastType;
use Panelis\Broadcast\Models\Broadcast;
use Panelis\Broadcast\Panel\Resources\BroadcastResource\Enums\BroadcastPermission;
use Panelis\Broadcast\Panel\Resources\BroadcastResource\Forms\BroadcastForm;
use Panelis\Broadcast\Panel\Resources\BroadcastResource\Forms\ResendForm;
use Panelis\Broadcast\Panel\Resources\BroadcastResource\Pages\CreateBroadcast;
use Panelis\Broadcast\Panel\Resources\BroadcastResource\Pages\EditBroadcast;
use Panelis\Broadcast\Panel\Resources\BroadcastResource\Pages\ListBroadcasts;
use Panelis\Broadcast\Panel\Resources\BroadcastResource\Pages\ViewBroadcast;

class BroadcastResource extends Resource
{
    protected static ?string $model = Broadcast::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    public static function getNavigationLabel(): string
    {
        return __('broadcast::broadcast.navigation');
    }

    public static function getLabel(): ?string
    {
        return __('broadcast::broadcast.label');
    }

    public static function canAccess(): bool
    {
        return user_can(BroadcastPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['roles', 'users']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components(BroadcastForm::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Broadcast $record): string => ViewBroadcast::getUrl([$record->id]))
            ->columns([
                TextColumn::make('title')
                    ->label(__('broadcast::broadcast.column.title'))
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->limit(40),

                TextColumn::make('type')
                    ->label(__('broadcast::broadcast.column.type'))
                    ->badge()
                    ->color(fn (mixed $state): string => match (BroadcastType::tryFrom($state instanceof BroadcastType ? $state->value : $state) ?? BroadcastType::Info) {
                        BroadcastType::Info => 'info',
                        BroadcastType::Warning => 'warning',
                        BroadcastType::Success => 'success',
                        BroadcastType::Error => 'danger',
                    })
                    ->formatStateUsing(fn (mixed $state): string => __('broadcast::broadcast.type.'.($state instanceof BroadcastType ? $state->value : $state))),

                TextColumn::make('status')
                    ->label(__('broadcast::broadcast.column.status'))
                    ->badge()
                    ->color(fn (mixed $state): string => match (BroadcastStatus::tryFrom($state instanceof BroadcastStatus ? $state->value : $state) ?? BroadcastStatus::Draft) {
                        BroadcastStatus::Draft => 'gray',
                        BroadcastStatus::Scheduled => 'warning',
                        BroadcastStatus::Sent => 'success',
                    })
                    ->formatStateUsing(fn (mixed $state): string => __('broadcast::broadcast.status.'.($state instanceof BroadcastStatus ? $state->value : $state))),

                TextColumn::make('channels')
                    ->label(__('broadcast::broadcast.column.channels'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("broadcast::broadcast.channel.{$state}")),

                TextColumn::make('roles.name')
                    ->label(__('broadcast::broadcast.column.roles'))
                    ->badge()
                    ->formatStateUsing(function (mixed $state, Broadcast $record): array {
                        if ($record->roles->isEmpty() && $record->users->isEmpty()) {
                            return [__('broadcast::broadcast.all_users')];
                        }

                        return (array) $state;
                    }),

                TextColumn::make('users.name')
                    ->label(__('broadcast::broadcast.column.users'))
                    ->badge(),

                TextColumn::make('send_at')
                    ->label(__('broadcast::broadcast.column.send_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->placeholder(__('broadcast::broadcast.now')),

                TextColumn::make('sent_at')
                    ->label(__('broadcast::broadcast.column.sent_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->placeholder(__('broadcast::broadcast.now')),

                TextColumn::make('created_at')
                    ->label(__('broadcast::broadcast.column.created_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('broadcast::broadcast.column.status'))
                    ->options(collect(BroadcastStatus::cases())->mapWithKeys(
                        fn (BroadcastStatus $status): array => [$status->value => __("broadcast::broadcast.status.{$status->value}")]
                    )->all()),

                SelectFilter::make('type')
                    ->label(__('broadcast::broadcast.column.type'))
                    ->options(collect(BroadcastType::cases())->mapWithKeys(
                        fn (BroadcastType $type): array => [$type->value => __("broadcast::broadcast.type.{$type->value}")]
                    )->all()),

                SelectFilter::make('roles')
                    ->label(__('broadcast::broadcast.column.roles'))
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('send')
                    ->label(__('broadcast::broadcast.actions.send'))
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(fn (Broadcast $record): bool => $record->isDraft() && user_can(BroadcastPermission::Create))
                    ->requiresConfirmation()
                    ->action(function (Broadcast $record): void {
                        SendBroadcast::run($record);

                        Notification::make()
                            ->title(__('broadcast::broadcast.notifications.send.title'))
                            ->body(__('broadcast::broadcast.notifications.send.body'))
                            ->success()
                            ->send();
                    }),

                ReplicateAction::make('resend')
                    ->label(__('broadcast::broadcast.actions.resend'))
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (Broadcast $record): bool => $record->isSent() && user_can(BroadcastPermission::Create))
                    ->excludeAttributes(['status', 'send_at', 'sent_at', 'created_at', 'updated_at'])
                    ->schema(ResendForm::schema())
                    ->fillForm(fn (Broadcast $record): array => [
                        'title' => $record->title,
                        'body' => $record->body,
                        'type' => $record->type?->value ?? BroadcastType::Info->value,
                        'channels' => $record->channels ?: [BroadcastChannel::Database->value],
                        'roles' => $record->roles->modelKeys(),
                        'users' => $record->users->modelKeys(),
                    ])
                    ->after(function (Broadcast $replica, array $data): void {
                        $replica->roles()->sync($data['roles'] ?? []);
                        $replica->users()->sync($data['users'] ?? []);

                        SendBroadcast::run($replica);
                    })
                    ->successNotificationTitle(__('broadcast::broadcast.notifications.resend.title'))
                    ->successNotificationMessage(__('broadcast::broadcast.notifications.resend.body')),

                EditAction::make()
                    ->visible(fn (Broadcast $record): bool => $record->isDraft() && user_can(BroadcastPermission::Edit)),

                DeleteAction::make()
                    ->visible(fn (Broadcast $record): bool => $record->isDraft() && user_can(BroadcastPermission::Delete)),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBroadcasts::route('/'),
            'create' => CreateBroadcast::route('/create'),
            'edit' => EditBroadcast::route('/{record}/edit'),
            'view' => ViewBroadcast::route('/{record}'),
        ];
    }
}
