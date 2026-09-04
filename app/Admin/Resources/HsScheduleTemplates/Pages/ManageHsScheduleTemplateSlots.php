<?php

namespace App\Admin\Resources\HsScheduleTemplates\Pages;

use App\Admin\Resources\HsDoctors\HsDoctorResource;
use App\Admin\Resources\HsScheduleTemplates\HsScheduleTemplateResource;
use App\Models\HsScheduleTemplate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ManageHsScheduleTemplateSlots extends ManageRelatedRecords
{
    protected static string $resource = HsScheduleTemplateResource::class;

    protected static string $relationship = 'slots';

    protected static ?string $relationshipTitle = '号源时段模板';

    protected static ?string $title = '号源时段模板';

    protected static ?string $breadcrumb = '号源时段模板';

    public function getTitle(): string|Htmlable
    {
        /** @var HsScheduleTemplate $template */
        $template = $this->getRecord();

        $doctor = $template->doctor?->name ?? '医生';

        return '号源时段 · '.$doctor.' · '.$template->adminLabel();
    }

    /**
     * @return array<string, mixed>
     */
    public function getBreadcrumbs(): array
    {
        /** @var HsScheduleTemplate $template */
        $template = $this->getRecord();
        $doctor = $template->doctor;

        $breadcrumbs = [
            HsDoctorResource::getUrl() => HsDoctorResource::getBreadcrumb(),
        ];

        if ($doctor) {
            $breadcrumbs[HsDoctorResource::getUrl('edit', ['record' => $doctor])] = $doctor->name;
        }

        $breadcrumbs[] = '号源时段模板';

        return $breadcrumbs;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TimePicker::make('start_time')->label('开始时间')->seconds(false)->required(),
            TimePicker::make('end_time')->label('结束时间')->seconds(false)->required()->after('start_time'),
            TextInput::make('total')->label('号源总量')->numeric()->default(0)->required(),
            TextInput::make('sort')->label('排序')->numeric()->default(0)->required(),
            Toggle::make('is_enabled')->label('可约')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->defaultSort('sort')->columns([
            TextColumn::make('start_time')->label('开始'),
            TextColumn::make('end_time')->label('结束'),
            TextColumn::make('total')->label('总量'),
            TextColumn::make('sort')->label('排序')->sortable(),
            IconColumn::make('is_enabled')->label('可约')->boolean(),
        ])->headerActions([
            CreateAction::make(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}
