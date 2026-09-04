<?php

namespace App\Admin\Resources\HsCheckupSettings;

use App\Admin\Resources\HsCheckupSettings\Pages\CreateHsCheckupSetting;
use App\Admin\Resources\HsCheckupSettings\Pages\EditHsCheckupSetting;
use App\Admin\Resources\HsCheckupSettings\Pages\ListHsCheckupSettings;
use App\Models\HsCheckupSetting;
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

class HsCheckupSettingResource extends Resource
{
    protected static ?string $model = HsCheckupSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = '体检规则';

    protected static ?string $modelLabel = '体检规则';

    protected static ?string $pluralModelLabel = '体检规则';

    protected static string|UnitEnum|null $navigationGroup = '体检中心';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'key';

    public static function canCreate(): bool
    {
        return parent::canCreate()
            && ! HsCheckupSetting::query()->where('key', HsCheckupSetting::DEFAULT_KEY)->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('体检预约规则')->columnSpanFull()->columns(2)->schema([
                Toggle::make('booking_enabled')->label('开放在线体检预约')->default(true),
                TextInput::make('advance_days')->label('提前可约天数')->numeric()->default(14)->required()->suffix('天'),
                TextInput::make('cancel_hours_before')->label('可取消时限')->numeric()->default(24)->required()->suffix('小时前'),
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
            IconColumn::make('booking_enabled')->label('开放预约')->boolean(),
            TextColumn::make('advance_days')->label('提前可约')->suffix(' 天'),
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
            'index' => ListHsCheckupSettings::route('/'),
            'create' => CreateHsCheckupSetting::route('/create'),
            'edit' => EditHsCheckupSetting::route('/{record}/edit'),
        ];
    }
}
