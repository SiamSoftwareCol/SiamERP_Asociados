<?php

namespace App\helper;

use Swis\Filament\Backgrounds\Contracts\ProvidesImages;
use Swis\Filament\Backgrounds\Image;

class MyImages implements ProvidesImages
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getImage(): Image
    {
        return new Image(
                    asset('images/backgrounds/fondep_background.jpg'),
                    'Siam ERP Background'
                );
            }
}
