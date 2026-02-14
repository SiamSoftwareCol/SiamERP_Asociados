<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ReportesSuperSolidaria extends Cluster
{
    protected static ?string $navigationGroup = 'Informes de Cumplimiento';
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Reportes Supersolidaria-SICSES';
    protected static ?string $modelLabel = 'Reportes Supersolidaria-SICSES';
    protected static ?int $navigationSort = 99;
}
