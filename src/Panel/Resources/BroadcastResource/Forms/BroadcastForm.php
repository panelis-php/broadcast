<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Panel\Resources\BroadcastResource\Forms;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Panelis\Broadcast\Enums\BroadcastChannel;
use Panelis\Broadcast\Enums\BroadcastType;

class BroadcastForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make(__('broadcast::broadcast.section.message'))
                ->description(__('broadcast::broadcast.section.message_description'))
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

                    TextInput::make('url')
                        ->label(__('broadcast::broadcast.form.url'))
                        ->helperText(__('broadcast::broadcast.form.url_helper'))
                        ->url()
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder('https://…'),

                    TextInput::make('label')
                        ->label(__('broadcast::broadcast.form.label'))
                        ->helperText(__('broadcast::broadcast.form.label_helper'))
                        ->nullable()
                        ->maxLength(255)
                        ->placeholder(__('broadcast::broadcast.form.label_placeholder')),
                ])
                ->columns(2),

            Section::make(__('broadcast::broadcast.section.audience'))
                ->description(__('broadcast::broadcast.section.audience_description'))
                ->schema([
                    Select::make('roles')
                        ->label(__('broadcast::broadcast.form.roles'))
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->helperText(__('broadcast::broadcast.form.recipients_helper'))
                        ->columnSpanFull(),

                    Select::make('users')
                        ->label(__('broadcast::broadcast.form.users'))
                        ->relationship('users', 'name')
                        ->multiple()
                        ->searchable()
                        ->helperText(__('broadcast::broadcast.form.recipients_helper'))
                        ->columnSpanFull(),

                    Select::make('type')
                        ->label(__('broadcast::broadcast.form.type'))
                        ->options(collect(BroadcastType::cases())->mapWithKeys(
                            fn (BroadcastType $type): array => [$type->value => __("broadcast::broadcast.type.{$type->value}")]
                        )->all())
                        ->default(BroadcastType::Info->value)
                        ->required(),

                    Select::make('channels')
                        ->label(__('broadcast::broadcast.form.channel'))
                        ->options(collect(BroadcastChannel::cases())->mapWithKeys(
                            fn (BroadcastChannel $channel): array => [$channel->value => __("broadcast::broadcast.channel.{$channel->value}")]
                        )->all())
                        ->multiple()
                        ->default([BroadcastChannel::Database->value])
                        ->required(),
                ])
                ->columns(2),

            Section::make(__('broadcast::broadcast.section.schedule'))
                ->description(__('broadcast::broadcast.section.schedule_description'))
                ->schema([
                    Checkbox::make('save_as_draft')
                        ->label(__('broadcast::broadcast.form.save_as_draft'))
                        ->live()
                        ->columnSpanFull(),

                    Checkbox::make('send_now')
                        ->label(__('broadcast::broadcast.form.send_now'))
                        ->default(true)
                        ->live()
                        ->hidden(fn (Get $get): bool => (bool) $get('save_as_draft'))
                        ->columnSpanFull(),

                    DateTimePicker::make('send_at')
                        ->label(__('broadcast::broadcast.form.send_at'))
                        ->helperText(__('broadcast::broadcast.form.send_at_helper'))
                        ->hidden(fn (Get $get): bool => (bool) $get('save_as_draft') || (bool) $get('send_now'))
                        ->columnSpanFull(),
                ]),
        ];
    }
}
