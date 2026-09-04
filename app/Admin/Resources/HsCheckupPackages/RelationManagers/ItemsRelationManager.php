<?php

namespace App\Admin\Resources\HsCheckupPackages\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = '包含项目';

    protected static ?string $modelLabel = '检查项目';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sort')->label('排序')->numeric()->default(0)->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort')->defaultSort('sort')->columns([
            TextColumn::make('name')->label('项目')->searchable(),
            TextColumn::make('category')->label('分类'),
            TextColumn::make('sort')->label('排序')->sortable(),
        ])->headerActions([
            AttachAction::make()
                ->preloadRecordSelect()
                ->form(fn (AttachAction $action): array => [
                    $action->getRecordSelect(),
                    TextInput::make('sort')->label('排序')->numeric()->default(0)->required(),
                ]),
        ])->recordActions([
            DetachAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DetachBulkAction::make(),
            ]),
        ]);
    }
}
