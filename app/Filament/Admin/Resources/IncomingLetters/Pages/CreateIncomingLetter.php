<?php

namespace App\Filament\Admin\Resources\IncomingLetters\Pages;

use App\Filament\Admin\Resources\IncomingLetters\IncomingLetterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIncomingLetter extends CreateRecord
{
    protected static string $resource = IncomingLetterResource::class;
}
