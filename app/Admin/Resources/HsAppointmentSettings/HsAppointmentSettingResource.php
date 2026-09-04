<?php

namespace App\Admin\Resources\HsAppointmentSettings;

use App\Admin\Resources\HsAppointmentSettings\Pages\CreateHsAppointmentSetting;
use App\Admin\Resources\HsAppointmentSettings\Pages\EditHsAppointmentSetting;
use App\Admin\Resources\HsAppointmentSettings\Pages\ListHsAppointmentSettings;
use App\Models\HsAppointmentSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class HsAppointmentSettingResource extends Resource
{
    protected static ?string $model = HsAppointmentSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = '预约规则';

    protected static ?string $modelLabel = '预约规则';

    protected static ?string $pluralModelLabel = '预约规则';

    protected static string|UnitEnum|null $navigationGroup = '就诊服务';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'key';

    public static function canCreate(): bool
    {
        return parent::canCreate()
            && ! HsAppointmentSetting::query()->where('key', HsAppointmentSetting::DEFAULT_KEY)->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('挂号规则')->columnSpanFull()->columns(2)->schema([
                Toggle::make('registration_enabled')->label('开放在线挂号')->default(true),
                TextInput::make('advance_days')->label('提前放号天数')->numeric()->default(7)->required()->suffix('天'),
                TextInput::make('cancel_hours_before')->label('可取消时限')->numeric()->default(2)->required()->suffix('小时前'),
                TextInput::make('no_show_limit')->label('爽约上限')->numeric()->default(3)->required()->suffix('次'),
                TextInput::make('no_show_ban_days')->label('爽约限制天数')->numeric()->default(30)->required()->suffix('天'),
                Textarea::make('notice')->label('预约须知')->rows(4)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('key')->label('配置标识'),
            IconColumn::make('registration_enabled')->label('开放挂号')->boolean(),
            TextColumn::make('advance_days')->label('提前放号')->suffix(' 天'),
            TextColumn::make('cancel_hours_before')->label('取消时限')->suffix(' 小时'),
            TextColumn::make('no_show_limit')->label('爽约上限'),
            TextColumn::make('updated_at')->label('更新时间')->dateTime(),
        ])->recordActions([
            EditAction::make(),
        ])->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHsAppointmentSettings::route('/'),
            'create' => CreateHsAppointmentSetting::route('/create'),
            'edit' => EditHsAppointmentSetting::route('/{record}/edit'),
        ];
    }
}
