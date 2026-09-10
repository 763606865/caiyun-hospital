<?php

namespace App\Admin\Resources\ConsultationRequests;

use App\Admin\Resources\ConsultationRequests\Pages\EditConsultationRequest;
use App\Admin\Resources\ConsultationRequests\Pages\ListConsultationRequests;
use App\Models\ConsultationRequest;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ConsultationRequestResource extends Resource
{
    protected static ?string $model = ConsultationRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = '咨询线索';

    protected static ?string $modelLabel = '咨询线索';

    protected static ?string $pluralModelLabel = '咨询线索';

    protected static string|UnitEnum|null $navigationGroup = 'SaaS 运营';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('姓名')->disabled(),
            TextInput::make('contact')->label('联系方式')->disabled(),
            TextInput::make('organization_name')->label('机构名称')->disabled(),
            TextInput::make('organization_type')->label('机构类型')->disabled(),
            TextInput::make('request_type')->label('需求类型')->disabled(),
            Select::make('status')->label('跟进状态')->options([
                'pending' => '待跟进',
                'contacted' => '已联系',
                'qualified' => '有效线索',
                'converted' => '已转化',
                'closed' => '已关闭',
            ])->required(),
            Textarea::make('requirements')->label('需求说明')->disabled()->rows(5)->columnSpanFull(),
            Textarea::make('follow_up_notes')->label('跟进备注')->rows(5)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            TextColumn::make('name')->label('姓名')->searchable(),
            TextColumn::make('contact')->label('联系方式')->searchable()->copyable(),
            TextColumn::make('organization_name')->label('机构')->searchable(),
            TextColumn::make('request_type')->label('需求')->badge()->formatStateUsing(fn (string $state): string => $state === 'trial' ? '申请试用' : '预约咨询'),
            TextColumn::make('status')->label('状态')->badge()->formatStateUsing(fn (string $state): string => match ($state) {
                'pending' => '待跟进',
                'contacted' => '已联系',
                'qualified' => '有效线索',
                'converted' => '已转化',
                'closed' => '已关闭',
                default => $state,
            }),
            TextColumn::make('created_at')->label('提交时间')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('request_type')->label('需求')->options(['consultation' => '预约咨询', 'trial' => '申请试用']),
            SelectFilter::make('status')->label('状态')->options([
                'pending' => '待跟进',
                'contacted' => '已联系',
                'qualified' => '有效线索',
                'converted' => '已转化',
                'closed' => '已关闭',
            ]),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsultationRequests::route('/'),
            'edit' => EditConsultationRequest::route('/{record}/edit'),
        ];
    }
}
