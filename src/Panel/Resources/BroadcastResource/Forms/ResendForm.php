<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Panel\Resources\BroadcastResource\Forms;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Panelis\Broadcast\Enums\BroadcastChannel;
use Panelis\Broadcast\Enums\BroadcastType;

class ResendForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        $roleModel = config('permission.models.role');
        $userModel = config('auth.providers.users.model');

        return [
            Section::make(__('broadcast::broadcast.section.message'))
                ->schema([
                    TextInput::make('title')
                        ->label(__('broadcast::broadcast.form.title'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    MarkdownEditor::make('body')
                        ->label(__('broadcast::broadcast.form.body'))
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make(__('broadcast::broadcast.section.audience'))
                ->schema([
                    Select::make('roles')
                        ->label(__('broadcast::broadcast.form.roles'))
                        ->options($roleModel::query()->pluck('name', 'id')->all())
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->helperText(__('broadcast::broadcast.form.recipients_helper'))
                        ->columnSpanFull(),

                    Select::make('users')
                        ->label(__('broadcast::broadcast.form.users'))
                        ->multiple()
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search): array => $userModel::query()
                            ->where('name', 'like', "%{$search}%")
                            ->limit(20)
                            ->pluck('name', 'id')
                            ->all())
                        ->getOptionLabelsUsing(fn (array $values): array => $userModel::query()
                            ->whereIn('id', $values)
                            ->pluck('name', 'id')
                            ->all())
                        ->helperText(__('broadcast::broadcast.form.recipients_helper'))
                        ->columnSpanFull(),

                    Select::make('type')
                        ->label(__('broadcast::broadcast.form.type'))
                        ->options(collect(BroadcastType::cases())->mapWithKeys(
                            fn (BroadcastType $type): array => [$type->value => __("broadcast::broadcast.type.{$type->value}")]
                        )->all())
                        ->required(),

                    Select::make('channels')
                        ->label(__('broadcast::broadcast.form.channel'))
                        ->options(collect(BroadcastChannel::cases())->mapWithKeys(
                            fn (BroadcastChannel $channel): array => [$channel->value => __("broadcast::broadcast.channel.{$channel->value}")]
                        )->all())
                        ->multiple()
                        ->required(),
                ])
                ->columns(2),
        ];
    }
}
