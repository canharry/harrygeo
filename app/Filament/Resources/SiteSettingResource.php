<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

/**
 * 站点设置资源
 * 管理员可在后台维护站点级文案与配置。
 */
class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationLabel = '站点设置';

    protected static ?string $pluralModelLabel = '站点设置';

    protected static ?string $modelLabel = '设置项';

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
                Forms\Components\TextInput::make('key')
                    ->label('键名')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('程序读取时使用的唯一标识，请勿随意修改'),

                Forms\Components\TextInput::make('label')
                    ->label('显示名称')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('group')
                    ->label('分组')
                    ->required()
                    ->default('general')
                    ->maxLength(255),

                Forms\Components\Textarea::make('value')
                    ->label('值')
                    ->rows(4)
                    ->maxLength(2000)
                    ->helperText('支持 HTML，留空则不显示'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('显示名称')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('key')
                    ->label('键名')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('group')
                    ->label('分组')
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('值')
                    ->limit(50)
                    ->wrap(),
            ])
            ->defaultSort('group')
            ->filters([
                //
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
            'index' => Pages\ManageSiteSettings::route('/'),
        ];
    }
}
