<?php

namespace App\Filament\Resources\CertificadoRetencionResource\Pages;

use App\Filament\Resources\CertificadoRetencionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCertificadoRetencions extends ListRecords
{
    protected static string $resource = CertificadoRetencionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
