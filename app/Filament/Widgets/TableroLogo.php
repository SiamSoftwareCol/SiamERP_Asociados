<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TableroLogo extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 2;

    /**
     * @var view-string
     */
    protected static string $view = 'custom.widgets.tablero';
}
