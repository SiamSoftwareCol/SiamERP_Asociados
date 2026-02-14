<?php

namespace App\Filament\Resources\CertificadoRetencionResource\Pages;

use App\Filament\Resources\CertificadoRetencionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCertificadoRetencion extends EditRecord
{
    protected static string $resource = CertificadoRetencionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
