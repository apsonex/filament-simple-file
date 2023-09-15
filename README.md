# Filament Image Field
 
 ### Usage

 ```php

use \Apsonex\FilamentSimpleFile\Form\Components\Image

Image::make('column')
    ->disk('s3')
    ->directory("storage/dir/location")
    ->visibility('public')
    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg'])
    ->helperText('Helper Text')
    ->maxSize(5 * 1024)
    ->label('Upload Logo');
 ```

 ### Delete previously stored file
To delete previously stored file, use `deleteOldFile(true)`. Make sure Form component implement `getRecord()` method.
 ```php
 Image::make('column')
    ->disk('s3')
    ->deleteOldFile(true)
 ```