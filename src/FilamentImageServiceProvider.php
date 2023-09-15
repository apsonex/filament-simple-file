<?php

namespace Apsonex\FilamentImage;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentImageServiceProvider extends PackageServiceProvider
{

    const PACKAGE_NAME = "apsonex/filament-image";

    /**
     * @url https://github.com/spatie/laravel-package-tools
     * @param Package $package
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-image')
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        //
    }
}
