<?php

namespace App\Filament\Admin\Resources\OutgoingLetters\Pages;

use App\Filament\Admin\Resources\OutgoingLetters\OutgoingLetterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOutgoingLetter extends CreateRecord
{
    protected static string $resource = OutgoingLetterResource::class;
}
