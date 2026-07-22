<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiVisitResource\Pages;
use App\Models\AiVisit;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

/**
 * AI 访问记录资源管理
 * 展示各 AI 平台访问文章的明细列表，仅管理员可见
 */
class AiVisitResource extends Resource
{
    protected static ?string $model = AiVisit::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe';

    protected static ?string $navigationLabel = 'AI 访问记录';

    protected static ?string $pluralModelLabel = 'AI 访问记录';
    protected static ?string $modelLabel = 'AI 访问记录';

    protected static ?string $recordTitleAttribute = 'ai_name';

    public static function canViewAny(): bool
    {
        return auth()->user()->is_admin ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('post_id')
                    ->label('所属文章')
                    ->relationship('post', 'title')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('ai_name')
                    ->label('AI 平台')
                    ->required()
                    ->maxLength(50),

                Forms\Components\TextInput::make('ip_address')
                    ->label('IP 地址')
                    ->maxLength(45),

                Forms\Components\Textarea::make('user_agent')
                    ->label('User-Agent')
                    ->rows(3),

                Forms\Components\TextInput::make('page_url')
                    ->label('访问页面')
                    ->maxLength(500),

                Forms\Components\DateTimePicker::make('visited_at')
                    ->label('访问时间')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ai_name')
                    ->label('AI 平台')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('post.title')
                    ->label('文章')
                    ->limit(30)
                    ->searchable()
                    ->sortable()
                    ->url(fn (AiVisit $record) => $record->post ? route('posts.show', $record->post->slug) : null, true)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP 地址')
                    ->sortable(),

                Tables\Columns\TextColumn::make('page_url')
                    ->label('访问页面')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('visited_at')
                    ->label('访问时间')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('visited_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('ai_name')
                    ->label('AI 平台')
                    ->options(fn () => AiVisit::query()
                        ->select('ai_name')
                        ->distinct()
                        ->pluck('ai_name', 'ai_name')
                        ->toArray()
                    ),

                Tables\Filters\Filter::make('visited_at')
                    ->label('访问时间')
                    ->form([
                        Forms\Components\DatePicker::make('visited_from')->label('开始日期'),
                        Forms\Components\DatePicker::make('visited_until')->label('结束日期'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['visited_from'] ?? null, fn ($q) => $q->whereDate('visited_at', '>=', $data['visited_from']))
                            ->when($data['visited_until'] ?? null, fn ($q) => $q->whereDate('visited_at', '<=', $data['visited_until']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiVisits::route('/'),
            'create' => Pages\CreateAiVisit::route('/create'),
            'edit' => Pages\EditAiVisit::route('/{record}/edit'),
        ];
    }
}
