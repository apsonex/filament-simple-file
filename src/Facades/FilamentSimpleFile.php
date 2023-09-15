<?php

namespace Apsonex\FilamentSimpleFile\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Apsonex\FilamentSimpleFile\FilamentSimpleFile
 */
class FilamentSimpleFile extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Apsonex\FilamentSimpleFile\FilamentSimpleFile::class;
    }
}
