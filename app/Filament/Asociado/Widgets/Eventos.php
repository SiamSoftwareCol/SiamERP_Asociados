<?php

namespace App\Filament\Asociado\Widgets;

use Filament\Widgets\Widget;

class Eventos extends Widget
{
    protected static string $view = 'filament.asociado.widgets.eventos';
    protected static ?int $sort = 4;
    protected static ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 'full';
}
