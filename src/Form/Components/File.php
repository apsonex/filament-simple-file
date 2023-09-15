<?php

namespace Apsonex\FilamentSimpleFile\Form\Components;

use Closure;
use Filament\Forms\Components\Field;
use Apsonex\FilamentSimpleFile\Form\Components\Concerns\HasDisk;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Apsonex\FilamentSimpleFile\Form\Components\Concerns\CanMoveFiles;
use Apsonex\FilamentSimpleFile\Form\Components\Concerns\FileRules;

class File extends Field
{
    use HasDisk;
    use FileRules;
    use CanMoveFiles;

    protected string $view = "filament-simple-file::components.image";

    protected bool|null $profilePhoto = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateUpdated(function (Image $component, null|string|TemporaryUploadedFile $state) {
            if(blank($state)) {
                return;
            }

            if($state instanceof TemporaryUploadedFile) {
                $component->validateFile($state);
            }
        });

        $this->dehydrateStateUsing(static function ($component, null|string|TemporaryUploadedFile $state): ?string {
            if (blank($state)) {
                $component->processDeleteOldFile();
                return null;
            }

            if ($state instanceof TemporaryUploadedFile) {
                return $component->moveFileToDesiredLocation($state);
            }

            return $state;
        });
    }
}
