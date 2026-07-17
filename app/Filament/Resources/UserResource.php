<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * 用户资源管理
 * 提供后台管理员账号的增删改查界面
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = '用户';

    protected static ?string $pluralModelLabel = '用户';
    protected static ?string $modelLabel = '用户';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return auth()->user()->is_admin ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user->is_admin || $record->id === $user->id;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->is_admin ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (! auth()->user()->is_admin) {
            $query->where('id', auth()->id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        // 用户昵称
                        Forms\Components\TextInput::make('name')
                            ->label('昵称')
                            ->required()
                            ->maxLength(255),

                        // 登录邮箱
                        Forms\Components\TextInput::make('email')
                            ->label('邮箱')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        // 个性签名
                        Forms\Components\Textarea::make('signature')
                            ->label('个性签名')
                            ->rows(3)
                            ->maxLength(255)
                            ->helperText('将显示在首页与文章详情页的博主卡片中'),

                        // 头像
                        Forms\Components\FileUpload::make('avatar')
                            ->label('头像')
                            ->avatar()
                            ->disk('public')
                            ->directory(fn ($record) => 'avatars/' . ($record?->id ?? auth()->id()))
                            ->getUploadedFileUrlUsing(fn (string $file): ?string => asset('storage/' . ltrim($file, '/')))
                            ->maxSize(2048)
                            ->helperText('支持 jpg、png、gif、webp，最大 2MB；上传新头像会自动替换旧头像'),

                        // 登录密码
                        Forms\Components\TextInput::make('password')
                            ->label('密码')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->confirmed()
                            ->helperText('编辑用户时留空表示不修改密码'),

                        // 确认密码
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('确认密码')
                            ->password()
                            ->dehydrated(false)
                            ->helperText('修改密码时需要再次输入'),

                        // 管理员权限（仅管理员可见）
                        Forms\Components\Toggle::make('is_admin')
                            ->label('管理员')
                            ->default(false)
                            ->inline(false)
                            ->helperText('开启后该用户可登录 Filament 后台')
                            ->hidden(fn () => ! auth()->user()->is_admin)
                            ->disabled(fn () => ! auth()->user()->is_admin),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('昵称')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('邮箱')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('is_admin')
                    ->label('管理员')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('管理员')
                    ->placeholder('全部')
                    ->trueLabel('是')
                    ->falseLabel('否'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
