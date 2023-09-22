<?php

namespace Apsonex\FilamentSimpleFile\Form\Components;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Filament\Forms\Components\Concerns;
use Illuminate\Support\Facades\Storage;
use Filament\Support\Concerns\HasAlignment;
use Filament\Forms\Components\BaseFileUpload;
use League\Flysystem\UnableToCheckFileExistence;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Apsonex\FilamentSimpleFile\Form\Components\Concerns\CanMoveFiles;

class File extends BaseFileUpload
{
    use CanMoveFiles;
    use HasAlignment;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;
    use Concerns\HasExtraInputAttributes;

    protected string $view = "filament-simple-file::components.file";

    protected function setup(): void
    {
        parent::setUp();

        $this->saveUploadedFileUsing(static function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
            try {
                if (!$file->exists()) {
                    return null;
                }
            } catch (UnableToCheckFileExistence $exception) {
                return null;
            }

            if ($component->shouldMoveFiles() && ($component->getDiskName() == invade($file)->disk)) {
                $newPath = trim($component->getDirectory() . '/' . $component->getUploadedFileNameForStorage($file), '/');

                $component->getDisk()->move($file->path(), $newPath);

                return $newPath;
            }

            $location = $component->getDirectory() . '/' . $component->getUploadedFileNameForStorage($file);

            if ($component->getVisibility() === 'public') {
                $res = $component->getDisk()->writeStream(
                    $location,
                    $file->readStream(),
                    ['public'],
                );
            } else {
                $res = $component->getDisk()->writeStream(
                    $location,
                    $file->readStream(),
                );
            }


            $file->delete();

            return $res ? $location : null;
        });
    }

    public function multiple(bool|Closure $condition = true): static
    {
        return $this;
    }
}
