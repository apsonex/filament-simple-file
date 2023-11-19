<?php

namespace Apsonex\FilamentSimpleFile;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSimpleFileServiceProvider extends PackageServiceProvider
{

    const PACKAGE_NAME = "apsonex/filament-simple-file";

    /**
     * @url https://github.com/spatie/laravel-package-tools
     * @param Package $package
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-simple-file')
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            // AlpineComponent::make(id:'filament-simple-file-js-plugin', path:__DIR__ . '/../resources/dist/plugin.js')->loadedOnRequest(),
            Css::make(id:'filament-simple-file-css-plugin', path:__DIR__ . '/../resources/dist/plugin.css')->loadedOnRequest(),
        ], static::PACKAGE_NAME);
    }
}
