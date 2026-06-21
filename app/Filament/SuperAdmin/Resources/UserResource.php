<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $pluralLabel = 'Users';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Account Info')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Forms\Components\Select::make('role')
                            ->options([
                                'user' => 'User',
                                'admin' => 'Admin',
                                'super_admin' => 'Super Admin',
                            ])
                            ->required(),
                        Forms\Components\Select::make('subscription_plan')
                            ->options([
                                'free' => 'Free',
                                'basic' => 'Basic',
                                'premium' => 'Premium',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('subscription_ends_at'),
                        Forms\Components\Toggle::make('is_onboarding_completed')
                            ->inline(false),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Platform Permissions')
                    ->schema([
                        Forms\Components\Toggle::make('can_post_youtube')
                            ->label('Can Post to YouTube')
                            ->inline(false),
                        Forms\Components\Toggle::make('can_post_tiktok')
                            ->label('Can Post to TikTok')
                            ->inline(false),
                        Forms\Components\TextInput::make('youtube_access_token')
                            ->label('YouTube Access Token')
                            ->password()
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('youtube_refresh_token')
                            ->label('YouTube Refresh Token')
                            ->password()
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('tiktok_access_token')
                            ->label('TikTok Access Token')
                            ->password()
                            ->maxLength(65535),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'success' => 'super_admin',
                        'warning' => 'admin',
                        'gray' => 'user',
                    ])
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('subscription_plan')
                    ->colors([
                        'success' => 'premium',
                        'warning' => 'basic',
                        'gray' => 'free',
                    ])
                    ->sortable(),
                Tables\Columns\IconColumn::make('can_post_youtube')
                    ->label('YouTube')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('can_post_tiktok')
                    ->label('TikTok')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription_ends_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'user' => 'User',
                        'admin' => 'Admin',
                        'super_admin' => 'Super Admin',
                    ]),
                Tables\Filters\SelectFilter::make('subscription_plan')
                    ->options([
                        'free' => 'Free',
                        'basic' => 'Basic',
                        'premium' => 'Premium',
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
