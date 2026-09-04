<?php

namespace App\Admin\Resources\HsCheckupSlots;

use App\Admin\Resources\HsCheckupSlots\Pages\CreateHsCheckupSlot;
use App\Admin\Resources\HsCheckupSlots\Pages\EditHsCheckupSlot;
use App\Admin\Resources\HsCheckupSlots\Pages\ListHsCheckupSlots;
use App\Admin\Support\EnumOptions;
use App\Enums\HsSchedulePeriod;
use App\Models\HsCheckupPackage;
use App\Models\HsCheckupSlot;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class HsCheckupSlotResource extends Resource
{
    protected static ?string $model = HsCheckupSlot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = '体检场次';

    protected static ?string $modelLabel = '体检场次';

    protected static ?string $pluralModelLabel = '体检场次';

    protected static string|UnitEnum|null $navigationGroup = '体检中心';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('场次信息')->columnSpanFull()->columns(2)->schema([
                Select::make('package_id')->label('套餐')
                    ->relationship('package', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        if (! filled($state)) {
                            return;
                        }
                        $package = HsCheckupPackage::query()->find($state);
                        if ($package) {
                            $set('campus_id', $package->campus_id);
                        }
                    }),
                Select::make('campus_id')->label('院区')
                    ->relationship('campus', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('slot_date')->label('日期')->required()->native(false),
                Select::make('period')->label('场次')
                    ->options(EnumOptions::from(HsSchedulePeriod::cases()))
                    ->required()
                    ->default(HsSchedulePeriod::Morning->value),
                TextInput::make('total')->label('总量')->numeric()->default(0)->required()->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set, $get): void {
                        if ($get('remaining') === null || $get('remaining') === '' || (int) $get('remaining') === 0) {
                            $set('remaining', $state);
                        }
                    }),
                TextInput::make('remaining')->label('剩余')->numeric()->default(0)->required(),
                Toggle::make('is_enabled')->label('可约')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('slot_date', 'desc')->columns([
            TextColumn::make('package.name')->label('套餐')->searchable(),
            TextColumn::make('campus.name')->label('院区'),
            TextColumn::make('slot_date')->label('日期')->date()->sortable(),
            TextColumn::make('period')->label('场次')
                ->formatStateUsing(fn (HsSchedulePeriod $state) => $state->label()),
            TextColumn::make('total')->label('总量'),
            TextColumn::make('remaining')->label('剩余'),
            IconColumn::make('is_enabled')->label('可约')->boolean(),
        ])->filters([
            SelectFilter::make('package_id')->label('套餐')->relationship('package', 'name'),
            SelectFilter::make('campus_id')->label('院区')->relationship('campus', 'name'),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHsCheckupSlots::route('/'),
            'create' => CreateHsCheckupSlot::route('/create'),
            'edit' => EditHsCheckupSlot::route('/{record}/edit'),
        ];
    }
}
