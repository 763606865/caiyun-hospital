<?php

namespace App\Admin\Resources\HsCheckupOrders;

use App\Admin\Resources\HsCheckupOrders\Pages\CreateHsCheckupOrder;
use App\Admin\Resources\HsCheckupOrders\Pages\EditHsCheckupOrder;
use App\Admin\Resources\HsCheckupOrders\Pages\ListHsCheckupOrders;
use App\Admin\Resources\HsPatients\HsPatientResource;
use App\Admin\Support\EnumOptions;
use App\Enums\HsCheckupOrderStatus;
use App\Enums\HsPaymentStatus;
use App\Enums\HsSchedulePeriod;
use App\Models\HsCheckupOrder;
use App\Models\HsCheckupSlot;
use App\Models\HsPatient;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use UnitEnum;

class HsCheckupOrderResource extends Resource
{
    protected static ?string $model = HsCheckupOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = '体检预约';

    protected static ?string $modelLabel = '体检预约单';

    protected static ?string $pluralModelLabel = '体检预约单';

    protected static string|UnitEnum|null $navigationGroup = '体检中心';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'order_no';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('预约信息')->columnSpan(2)->columns(2)->schema([
                TextInput::make('order_no')->label('预约单号')
                    ->default(fn (): string => 'CK'.now()->format('YmdHis').Str::upper(Str::random(4)))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(32),
                Select::make('user_id')->label('下单用户')
                    ->relationship('user', 'phone')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => HsPatientResource::userLabel($record))
                    ->searchable(['phone', 'real_name', 'nick_name'])
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('patient_id', null)),
                Select::make('patient_id')->label('体检人')
                    ->options(fn (Get $get): array => HsPatient::query()
                        ->when($get('user_id'), fn (Builder $query, $userId) => $query->where('user_id', $userId))
                        ->orderByDesc('is_default')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->required(),
                Select::make('slot_id')->label('场次')
                    ->options(fn (): array => HsCheckupSlot::query()
                        ->with(['package', 'campus'])
                        ->latest('id')
                        ->limit(200)
                        ->get()
                        ->mapWithKeys(fn (HsCheckupSlot $slot): array => [
                            $slot->id => ($slot->package?->name ?? '套餐').' '
                                .($slot->slot_date?->format('Y-m-d') ?? '').' '
                                .($slot->period?->label() ?? ''),
                        ])
                        ->all())
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        if (! filled($state)) {
                            return;
                        }
                        $slot = HsCheckupSlot::query()->with('package')->find($state);
                        if (! $slot) {
                            return;
                        }
                        $set('campus_id', $slot->campus_id);
                        $set('package_id', $slot->package_id);
                        $set('appointment_date', $slot->slot_date?->format('Y-m-d'));
                        $set('period', $slot->period?->value);
                        $set('price', $slot->package?->price ?? 0);
                    }),
                DatePicker::make('appointment_date')->label('到检日期')->required()->native(false),
                Select::make('period')->label('场次')
                    ->options(EnumOptions::from(HsSchedulePeriod::cases()))
                    ->required(),
                TextInput::make('price')->label('价格')->numeric()->prefix('¥')->default(0)->required(),
                Textarea::make('remark')->label('备注')->rows(2)->columnSpanFull(),
            ]),
            Section::make('状态')->columnSpan(1)->schema([
                Select::make('status')->label('状态')
                    ->options(EnumOptions::from(HsCheckupOrderStatus::cases()))
                    ->required()
                    ->default(HsCheckupOrderStatus::Pending->value)
                    ->live(),
                Select::make('payment_status')->label('支付状态')
                    ->options(EnumOptions::from(HsPaymentStatus::cases()))
                    ->required()
                    ->default(HsPaymentStatus::NotRequired->value),
                TextInput::make('paid_amount')->label('实付金额')->numeric()->prefix('¥')->default(0)->required(),
                DateTimePicker::make('paid_at')->label('支付时间')->seconds(false),
                TextInput::make('cancel_reason')->label('取消原因')
                    ->visible(fn (Get $get): bool => $get('status') === HsCheckupOrderStatus::Cancelled->value),
                DateTimePicker::make('cancelled_at')->label('取消时间')->seconds(false)
                    ->visible(fn (Get $get): bool => $get('status') === HsCheckupOrderStatus::Cancelled->value),
                DateTimePicker::make('completed_at')->label('完成时间')->seconds(false)
                    ->visible(fn (Get $get): bool => $get('status') === HsCheckupOrderStatus::Completed->value),
                Select::make('campus_id')->label('院区')->relationship('campus', 'name')->disabled()->dehydrated(),
                Select::make('package_id')->label('套餐')->relationship('package', 'name')->disabled()->dehydrated(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('order_no')->label('单号')->searchable()->copyable(),
            TextColumn::make('patient.name')->label('体检人')->searchable(),
            TextColumn::make('package.name')->label('套餐')->searchable(),
            TextColumn::make('campus.name')->label('院区'),
            TextColumn::make('appointment_date')->label('到检日')->date()->sortable(),
            TextColumn::make('period')->label('场次')
                ->formatStateUsing(fn (HsSchedulePeriod $state) => $state->label()),
            TextColumn::make('price')->label('价格')->money('CNY'),
            TextColumn::make('status')->label('状态')->badge()
                ->formatStateUsing(fn (HsCheckupOrderStatus $state) => $state->label())
                ->color(fn (HsCheckupOrderStatus $state): string => match ($state) {
                    HsCheckupOrderStatus::Pending => 'warning',
                    HsCheckupOrderStatus::Completed => 'success',
                    HsCheckupOrderStatus::Cancelled => 'gray',
                    HsCheckupOrderStatus::NoShow => 'danger',
                }),
            TextColumn::make('payment_status')->label('支付状态')->badge()
                ->formatStateUsing(fn (HsPaymentStatus $state) => $state->label())
                ->color(fn (HsPaymentStatus $state): string => match ($state) {
                    HsPaymentStatus::NotRequired, HsPaymentStatus::Closed => 'gray',
                    HsPaymentStatus::Unpaid => 'warning',
                    HsPaymentStatus::Paid => 'success',
                    HsPaymentStatus::Refunding => 'info',
                    HsPaymentStatus::Refunded => 'primary',
                }),
            TextColumn::make('created_at')->label('下单时间')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('status')->label('状态')->options(EnumOptions::from(HsCheckupOrderStatus::cases())),
            SelectFilter::make('payment_status')->label('支付状态')->options(EnumOptions::from(HsPaymentStatus::cases())),
            SelectFilter::make('package_id')->label('套餐')->relationship('package', 'name'),
            TrashedFilter::make(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHsCheckupOrders::route('/'),
            'create' => CreateHsCheckupOrder::route('/create'),
            'edit' => EditHsCheckupOrder::route('/{record}/edit'),
        ];
    }
}
