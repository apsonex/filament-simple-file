<?php

namespace Apsonex\FilamentImage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Apsonex\FilamentImage\FilamentImage
 */
class FilamentImage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Apsonex\FilamentImage\FilamentImage::class;
    }
}
