<?php

namespace App\Filament\Admin\Resources\Customers\Pages;

use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Imports\CustomersImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('استيراد Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->schema([
                    FileUpload::make('file')
                        ->label('ملف Excel')
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    Excel::import(new CustomersImport, storage_path('app/private/'.$data['file']));
                    Notification::make()->title('تم الاستيراد بنجاح')->success()->send();
                }),
            CreateAction::make()->label('عميل جديد'),
        ];
    }
}
