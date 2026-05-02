<?php

namespace App\Filament\Admin\Resources\Activities;

use App\Filament\Admin\Resources\Activities\Pages\ListActivities;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;
use UnitEnum;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'النظام';

    protected static ?int $navigationSort = 110;

    public static function getModelLabel(): string { return 'نشاط'; }
    public static function getPluralModelLabel(): string { return 'سجل الأنشطة'; }
    public static function getNavigationLabel(): string { return 'سجل الأنشطة'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function canCreate(): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('التاريخ')->dateTime('Y-m-d H:i')->sortable(),
                TextColumn::make('causer.name')->label('بواسطة')->placeholder('—'),
                BadgeColumn::make('event')->label('الإجراء')
                    ->colors(['success' => 'created', 'warning' => 'updated', 'danger' => 'deleted'])
                    ->formatStateUsing(fn (?string $state): string => ['created'=>'إنشاء','updated'=>'تعديل','deleted'=>'حذف'][$state] ?? ($state ?? '—')),
                TextColumn::make('subject_type')->label('النموذج')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—'),
                TextColumn::make('subject_id')->label('المعرف')->toggleable(),
                TextColumn::make('description')->label('الوصف')->limit(60)->toggleable(),
                TextColumn::make('properties')->label('التغييرات')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '—';
                        $data = is_array($state) ? $state : json_decode($state, true);
                        if (!is_array($data)) return '—';
                        $out = [];
                        foreach (($data['attributes'] ?? []) as $k => $v) {
                            $old = $data['old'][$k] ?? null;
                            $out[] = "{$k}: ".($old ?? '∅')." → ".(is_scalar($v) ? $v : '...');
                        }
                        return implode(' | ', $out) ?: '—';
                    })
                    ->wrap()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')->label('الإجراء')->options(['created'=>'إنشاء','updated'=>'تعديل','deleted'=>'حذف']),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
        ];
    }
}
