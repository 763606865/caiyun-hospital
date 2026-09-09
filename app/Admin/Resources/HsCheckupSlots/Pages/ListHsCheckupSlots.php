<?php

namespace App\Admin\Resources\HsCheckupSlots\Pages;

use App\Admin\Resources\HsCheckupSlots\HsCheckupSlotResource;
use App\Admin\Support\EnumOptions;
use App\Enums\HsSchedulePeriod;
use App\Enums\HsWeekday;
use App\Models\HsCheckupPackage;
use App\Services\Hospital\CheckupSlotGenerationService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Throwable;

class ListHsCheckupSlots extends ListRecords
{
    protected static string $resource = HsCheckupSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateSlots')
                ->label('一键生成场次')
                ->icon(Heroicon::OutlinedSparkles)
                ->modalHeading('一键生成体检场次')
                ->modalDescription('将按套餐、日期、星期和场次批量创建；已存在的场次会自动跳过，不会覆盖已有名额。')
                ->form([
                    Select::make('package_ids')
                        ->label('体检套餐')
                        ->options(fn (): array => HsCheckupPackage::query()
                            ->enabled()
                            ->with('campus:id,name')
                            ->orderBy('sort')
                            ->get()
                            ->mapWithKeys(fn (HsCheckupPackage $package): array => [
                                $package->id => $package->name.($package->campus?->name ? "（{$package->campus->name}）" : ''),
                            ])
                            ->all())
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->required(),
                    DatePicker::make('from')
                        ->label('开始日期')
                        ->default(fn () => today())
                        ->native(false)
                        ->required(),
                    DatePicker::make('to')
                        ->label('结束日期')
                        ->default(fn () => today()->addDays(6))
                        ->native(false)
                        ->required(),
                    CheckboxList::make('weekdays')
                        ->label('适用星期')
                        ->options(EnumOptions::from(HsWeekday::cases()))
                        ->default(array_map(fn (HsWeekday $weekday): int => $weekday->value, HsWeekday::cases()))
                        ->columns(4)
                        ->required(),
                    CheckboxList::make('periods')
                        ->label('场次')
                        ->options(EnumOptions::from(HsSchedulePeriod::cases()))
                        ->default([HsSchedulePeriod::Morning->value, HsSchedulePeriod::Afternoon->value])
                        ->columns(3)
                        ->required(),
                    TextInput::make('total')
                        ->label('每场名额')
                        ->numeric()
                        ->minValue(1)
                        ->default(50)
                        ->required(),
                ])
                ->action(function (array $data, CheckupSlotGenerationService $generator): void {
                    try {
                        $result = $generator->generate(
                            $data['package_ids'],
                            $data['from'],
                            $data['to'],
                            $data['weekdays'],
                            $data['periods'],
                            (int) $data['total'],
                        );
                    } catch (Throwable $exception) {
                        Notification::make()
                            ->title('生成失败')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('体检场次已生成')
                        ->body(sprintf(
                            '覆盖 %d 个日期，新建 %d 个场次，跳过已存在 %d 个。',
                            $result['days'],
                            $result['created_slots'],
                            $result['skipped_existing'],
                        ))
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
