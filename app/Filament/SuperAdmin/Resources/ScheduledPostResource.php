<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\ScheduledPostResource\Pages;
use App\Models\ScheduledPost;
use Filament\Forms;
use Filament\Resources\Resource;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ScheduledPostResource extends Resource
{
    protected static ?string $model = ScheduledPost::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Scheduled Posts';

    protected static ?string $pluralLabel = 'Scheduled Posts';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Post Info')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->required(),
                        Forms\Components\Select::make('video_id')
                            ->label('Video')
                            ->relationship('video', 'title')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('ai_content_id')
                            ->label('AI Content')
                            ->relationship('aiContent', 'id')
                            ->searchable(),
                        Forms\Components\Select::make('platform')
                            ->options([
                                'facebook' => 'Facebook',
                                'youtube' => 'YouTube',
                                'tiktok' => 'TikTok',
                            ])
                            ->required(),
                        Forms\Components\Select::make('facebook_account_id')
                            ->label('Facebook Account')
                            ->relationship('facebookAccount', 'name')
                            ->searchable(),
                        Forms\Components\TextInput::make('facebook_page_id')
                            ->label('Page ID'),
                        Forms\Components\TextInput::make('platform_page_id')
                            ->label('Platform Page ID'),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('scheduled_time')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'published' => 'Published',
                                'failed' => 'Failed',
                            ]),
                        Forms\Components\TextInput::make('facebook_post_id')
                            ->label('Facebook Post ID'),
                        Forms\Components\TextInput::make('platform_post_id')
                            ->label('Platform Post ID'),
                        Forms\Components\DateTimePicker::make('published_at'),
                        Forms\Components\Textarea::make('error_message')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('platform')
                    ->badge()
                    ->colors([
                        'primary' => 'facebook',
                        'danger' => 'youtube',
                        'gray' => 'tiktok',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('video.title')
                    ->label('Video')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('scheduled_time')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'processing',
                        'success' => 'published',
                        'danger' => 'failed',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->options([
                        'facebook' => 'Facebook',
                        'youtube' => 'YouTube',
                        'tiktok' => 'TikTok',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'published' => 'Published',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScheduledPosts::route('/'),
            'create' => Pages\CreateScheduledPost::route('/create'),
            'edit' => Pages\EditScheduledPost::route('/{record}/edit'),
        ];
    }
}
