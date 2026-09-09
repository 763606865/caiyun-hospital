<?php

namespace App\Admin\Resources\Visits;

use App\Admin\Resources\Visits\Pages\CreateVisit;
use App\Admin\Resources\Visits\Pages\EditVisit;
use App\Admin\Resources\Visits\Pages\ListVisits;
use App\Admin\Support\TenantResource;
use App\Models\Visit;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VisitResource extends TenantResource
{
    protected static ?string $model = Visit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = '接诊';

    protected static ?string $modelLabel = '接诊';

    protected static ?string $pluralModelLabel = '接诊';

    protected static string|UnitEnum|null $navigationGroup = '门诊业务';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('branch_id')->label('门店')->relationship('branch', 'name')->searchable()->preload()->required(),
            Select::make('patient_id')->label('患者')->relationship('patient', 'name')->searchable()->preload()->required(),
            Select::make('doctor_id')->label('医生')->relationship('doctor', 'name')->searchable()->preload()->nullable(),
            TextInput::make('visit_no')->label('接诊号')->required()->maxLength(255),
            TextInput::make('queue_no')->label('排队号')->maxLength(255),
            Select::make('status')->label('状态')->options(['active' => '启用', 'inactive' => '停用', 'draft' => '草稿', 'waiting' => '候诊', 'in_progress' => '接诊中', 'completed' => '已完成', 'pending' => '待处理', 'paid' => '已支付', 'cancelled' => '已取消'])->required(),
            Textarea::make('chief_complaint')->label('主诉')->rows(3)->columnSpanFull(),
            DateTimePicker::make('registered_at')->label('登记时间'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('branch.name')->label('门店')->sortable(),
            TextColumn::make('patient.name')->label('患者')->sortable(),
            TextColumn::make('doctor.name')->label('医生')->sortable(),
            TextColumn::make('visit_no')->label('接诊号')->searchable()->sortable(),
            TextColumn::make('queue_no')->label('排队号')->sortable(),
            TextColumn::make('status')->label('状态')->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisits::route('/'),
            'create' => CreateVisit::route('/create'),
            'edit' => EditVisit::route('/{record}/edit'),
        ];
    }
}
