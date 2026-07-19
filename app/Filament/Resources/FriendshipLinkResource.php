<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FriendshipLinkResource\Pages;
use App\Models\FriendshipLink;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

/**
 * 友情链接资源管理
 * 管理员可在后台增删改查首页侧边栏的友情链接
 */
class FriendshipLinkResource extends Resource
{
    protected static ?string $model = FriendshipLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = '友情链接';

    protected static ?string $pluralModelLabel = '友情链接';

    protected static ?string $modelLabel = '友情链接';

    protected static ?string $navigationGroup = '站点';

    public static function canViewAny(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('链接名称')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('url')
                            ->label('链接地址')
                            ->url()
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-globe-alt'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('排序')
                            ->numeric()
                            ->default(0)
                            ->helperText('数字越小越靠前'),

                        Forms\Components\Toggle::make('is_show')
                            ->label('是否展示')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('链接名称')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('url')
                    ->label('链接地址')
                    ->limit(40)
                    ->url(fn ($record) => $record->url, true),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('is_show')
                    ->label('展示'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_show')
                    ->label('展示状态')
                    ->placeholder('全部')
                    ->trueLabel('展示')
                    ->falseLabel('隐藏'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFriendshipLinks::route('/'),
            'create' => Pages\CreateFriendshipLink::route('/create'),
            'edit'   => Pages\EditFriendshipLink::route('/{record}/edit'),
        ];
    }
}
