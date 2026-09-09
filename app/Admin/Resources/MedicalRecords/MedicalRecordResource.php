<?php

namespace App\Admin\Resources\MedicalRecords;

use App\Admin\Resources\MedicalRecords\Pages\CreateMedicalRecord;
use App\Admin\Resources\MedicalRecords\Pages\EditMedicalRecord;
use App\Admin\Resources\MedicalRecords\Pages\ListMedicalRecords;
use App\Admin\Support\TenantResource;
use App\Models\MedicalRecord;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class MedicalRecordResource extends TenantResource
{
    protected static ?string $model = MedicalRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = '电子病历';

    protected static ?string $modelLabel = '电子病历';

    protected static ?string $pluralModelLabel = '电子病历';

    protected static string|UnitEnum|null $navigationGroup = '门诊业务';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('branch_id')->label('门店')->relationship('branch', 'name')->searchable()->preload()->required(),
            Select::make('visit_id')->label('接诊记录')->relationship('visit', 'visit_no')->searchable()->preload()->nullable(),
            Select::make('patient_id')->label('患者')->relationship('patient', 'name')->searchable()->preload()->required(),
            Select::make('doctor_id')->label('医生')->relationship('doctor', 'name')->searchable()->preload()->nullable(),
            Textarea::make('present_illness')->label('现病史')->rows(3)->columnSpanFull(),
            Textarea::make('tcm_inspection')->label('望诊')->rows(3)->columnSpanFull(),
            Textarea::make('tcm_auscultation_olfaction')->label('闻诊')->rows(3)->columnSpanFull(),
            Textarea::make('tcm_inquiry')->label('问诊')->rows(3)->columnSpanFull(),
            Textarea::make('tcm_palpation')->label('切诊')->rows(3)->columnSpanFull(),
            Textarea::make('tcm_syndrome')->label('中医辨证')->rows(3)->columnSpanFull(),
            Textarea::make('tcm_treatment_principle')->label('治则治法')->rows(3)->columnSpanFull(),
            Textarea::make('treatment_plan')->label('诊疗计划')->rows(3)->columnSpanFull(),
            Textarea::make('medical_advice')->label('医嘱')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('branch.name')->label('门店')->sortable(),
            TextColumn::make('visit.visit_no')->label('接诊记录')->sortable(),
            TextColumn::make('patient.name')->label('患者')->sortable(),
            TextColumn::make('doctor.name')->label('医生')->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedicalRecords::route('/'),
            'create' => CreateMedicalRecord::route('/create'),
            'edit' => EditMedicalRecord::route('/{record}/edit'),
        ];
    }
}
