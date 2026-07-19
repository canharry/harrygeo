<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use App\Services\SlugService;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

/**
 * 分类资源管理
 * 提供文章分类在 Filament 后台的增删改查界面
 */
class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = '分类';

    protected static ?string $pluralModelLabel = '分类';
    protected static ?string $modelLabel = '分类';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return auth()->user()->is_admin ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        // 分类名称
                        Forms\Components\TextInput::make('name')
                            ->label('名称')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', SlugService::make($state, 'category'));
                            }),

                        // URL 别名
                        Forms\Components\TextInput::make('slug')
                            ->label('URL 别名')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        // 描述
                        Forms\Components\Textarea::make('description')
                            ->label('描述')
                            ->rows(3)
                            ->maxLength(500),

                        // 颜色
                        Forms\Components\ColorPicker::make('color')
                            ->label('颜色')
                            ->default('#6366f1'),

                        // 排序
                        Forms\Components\TextInput::make('sort_order')
                            ->label('排序')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        // 是否显示
                        Forms\Components\Toggle::make('is_visible')
                            ->label('是否显示')
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
                    ->label('名称')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('别名')
                    ->searchable(),

                Tables\Columns\ColorColumn::make('color')
                    ->label('颜色'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('is_visible')
                    ->label('显示')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('显示状态')
                    ->placeholder('全部')
                    ->trueLabel('显示')
                    ->falseLabel('隐藏'),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
