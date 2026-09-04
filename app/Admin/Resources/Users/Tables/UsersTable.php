<?php

namespace App\Admin\Resources\Users\Tables;

use App\Admin\Support\EnumOptions;
use App\Enums\UserGender;
use App\Enums\UserStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                ImageColumn::make('avatar')->label('头像')->disk('public')->circular(),
                TextColumn::make('real_name')->label('真实姓名')->searchable()->sortable()->placeholder('-'),
                TextColumn::make('nick_name')->label('昵称')->searchable()->toggleable(),
                TextColumn::make('phone')->label('手机号')->searchable()->sortable(),
                TextColumn::make('email')->label('邮箱')->searchable()->toggleable(),
                TextColumn::make('gender')->label('性别')
                    ->formatStateUsing(fn (?UserGender $state) => $state?->label() ?? '-')
                    ->toggleable(),
                TextColumn::make('status')->label('状态')->badge()
                    ->formatStateUsing(fn (UserStatus $state) => $state->label())
                    ->color(fn (UserStatus $state): string => match ($state) {
                        UserStatus::Normal => 'success',
                        UserStatus::Disabled => 'danger',
                    }),
                TextColumn::make('patients_count')->label('就诊人')->counts('patients'),
                TextColumn::make('created_at')->label('注册时间')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('状态')->options(EnumOptions::from(UserStatus::cases())),
                SelectFilter::make('gender')->label('性别')->options(EnumOptions::from(UserGender::cases())),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
