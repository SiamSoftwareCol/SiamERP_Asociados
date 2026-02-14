<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ParametrosAsociados extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string    $navigationGroup = 'Configuración General';
    protected static ?int       $navigationSort = 8;
}
