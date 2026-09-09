<?php

namespace App\Admin\Resources\Prescriptions;

use App\Admin\Resources\Prescriptions\Pages\CreatePrescription;
use App\Admin\Resources\Prescriptions\Pages\EditPrescription;
use App\Admin\Resources\Prescriptions\Pages\ListPrescriptions;
use App\Admin\Support\TenantResource;
use App\Models\Prescription;
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

class PrescriptionResource extends TenantResource
{
    protected static ?string $model = Prescription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static ?string $navigationLabel = '处方';

    protected static ?string $modelLabel = '处方';

    protected static ?string $pluralModelLabel = '处方';

    protected static string|UnitEnum|null $navigationGroup = '门诊业务';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('branch_id')->label('门店')->relationship('branch', 'name')->searchable()->preload()->required(),
            Select::make('visit_id')->label('接诊记录')->relationship('visit', 'visit_no')->searchable()->preload()->nullable(),
            Select::make('patient_id')->label('患者')->relationship('patient', 'name')->searchable()->preload()->required(),
            Select::make('doctor_id')->label('医生')->relationship('doctor', 'name')->searchable()->preload()->nullable(),
            TextInput::make('prescription_no')->label('处方号')->required()->maxLength(255),
            TextInput::make('type')->label('处方类型')->required(),
            Select::make('status')->label('状态')->options(['active' => '启用', 'inactive' => '停用', 'draft' => '草稿', 'waiting' => '候诊', 'in_progress' => '接诊中', 'completed' => '已完成', 'pending' => '待处理', 'paid' => '已支付', 'cancelled' => '已取消'])->required(),
            TextInput::make('dose_count')->label('剂数')->numeric()->default(0),
            TextInput::make('administration')->label('用法')->maxLength(255),
            TextInput::make('frequency')->label('频次')->maxLength(255),
            Textarea::make('usage_instructions')->label('用药说明')->rows(3)->columnSpanFull(),
            DateTimePicker::make('issued_at')->label('开方时间'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('branch.name')->label('门店')->sortable(),
            TextColumn::make('visit.visit_no')->label('接诊记录')->sortable(),
            TextColumn::make('patient.name')->label('患者')->sortable(),
            TextColumn::make('doctor.name')->label('医生')->sortable(),
            TextColumn::make('prescription_no')->label('处方号')->searchable()->sortable(),
            TextColumn::make('type')->label('处方类型')->sortable(),
            TextColumn::make('status')->label('状态')->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrescriptions::route('/'),
            'create' => CreatePrescription::route('/create'),
            'edit' => EditPrescription::route('/{record}/edit'),
        ];
    }
}
