<?php

namespace App\Filament\Admin\Resources\Tasks\Schemas;

use App\Models\Task;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات المهمة')->columns(2)->schema([
                TextInput::make('title')->label('العنوان')->required()->columnSpanFull(),
                Textarea::make('description')->label('الوصف')->columnSpanFull(),
                Select::make('assigned_to')->label('المسؤول')->options(User::pluck('name', 'id'))->searchable(),
                DatePicker::make('due_date')->label('تاريخ الاستحقاق')->native(false),
                Select::make('priority')->label('الأولوية')->options(Task::PRIORITIES)->default('normal')->required(),
                Select::make('status')->label('الحالة')->options(Task::STATUSES)->default('todo')->required(),
            ]),
        ]);
    }
}
