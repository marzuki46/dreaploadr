<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\AiContentResource\Pages;
use App\Models\AiContent;
use Filament\Forms;
use Filament\Resources\Resource;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AiContentResource extends Resource
{
    protected static ?string $model = AiContent::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'AI Contents';

    protected static ?string $pluralLabel = 'AI Contents';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('AI Content')
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
                        Forms\Components\Textarea::make('original_text')
                            ->label('Original Text')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('ai_remake_text')
                            ->label('AI Remake Text')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('ai_provider')
                            ->options([
                                'gemini' => 'Gemini',
                                'openai' => 'OpenAI',
                            ]),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                            ]),
                    ]),
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
                Tables\Columns\TextColumn::make('video.title')
                    ->label('Video')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('ai_provider')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('ai_provider')
                    ->options([
                        'gemini' => 'Gemini',
                        'openai' => 'OpenAI',
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
            'index' => Pages\ListAiContents::route('/'),
            'create' => Pages\CreateAiContent::route('/create'),
            'edit' => Pages\EditAiContent::route('/{record}/edit'),
        ];
    }
}
