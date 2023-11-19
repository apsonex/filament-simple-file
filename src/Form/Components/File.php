<?php

namespace Apsonex\FilamentSimpleFile\Form\Components;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\Renderless;
use Filament\Forms\Components\Concerns;
use Illuminate\Support\Facades\Storage;
use Filament\Support\Concerns\HasAlignment;
use Filament\Forms\Components\BaseFileUpload;
use League\Flysystem\UnableToCheckFileExistence;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Filament\Forms\Components\Concerns\HasPlaceholder;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Apsonex\FilamentSimpleFile\Form\Components\Concerns\HasImageSize;
use Apsonex\FilamentSimpleFile\Form\Components\Concerns\CanMoveFiles;

class File extends BaseFileUpload
{
    use CanMoveFiles;
    use HasImageSize;
    use HasAlignment;
    use HasPlaceholder;
    use HasExtraAlpineAttributes;
    use Concerns\HasExtraInputAttributes;

    protected string $view = "filament-simple-file::components.file";

    protected function setup(): void
    {
        parent::setUp();

        $this->saveUploadedFileUsing(static function (File $component, TemporaryUploadedFile $file): ?string {
            try {
                if (!$file->exists()) {
                    return null;
                }
            } catch (UnableToCheckFileExistence $exception) {
                return null;
            }

            return $component->moveFileToDesiredLocation($file);

            // if ($file->getMimeType() === 'image/svg+xml' || $file->extension() === 'svg') {
            //     // We need to clean svg before final use
            //     $temporaryDirectory = (new TemporaryDirectory())->name(Str::ulid()->__toString())->create();
            //     $tempPath = $temporaryDirectory->path($component->getUploadedFileNameForStorage($file));
            //     $localLocation = $component->getDisk()->writeStream(
            //         $tempPath,
            //         $file->readStream(),
            //         ['public'],
            //     );
            // }

            // dd($file->getMimeType(), $file->extension());

            // $shouldMoveFiles = invade($file)->disk === $component->getDiskName();

            // dd($localLocation);

            // if ($component->shouldMoveFiles() && ($component->getDiskName() == invade($file)->disk)) {
            //     $newPath = trim($component->getDirectory() . '/' . $component->getUploadedFileNameForStorage($file), '/');

            //     $component->getDisk()->move($file->path(), $newPath);
            //     dd('her', $newPath);
            //     return $newPath;
            // }

            // $location = $component->getDirectory() . '/' . $component->getUploadedFileNameForStorage($file);

            // dd('there', $location);

            // if ($component->getVisibility() === 'public') {
            //     $res = $component->getDisk()->writeStream(
            //         $location,
            //         $file->readStream(),
            //         ['public'],
            //     );
            // } else {
            //     $res = $component->getDisk()->writeStream(
            //         $location,
            //         $file->readStream(),
            //     );
            // }


            // $file->delete();

            // return $res ? $location : null;
        });
    }

    public function multiple(bool|Closure $condition = true): static
    {
        return $this;
    }

    public function getUploadedFile(string $statePath): ?array
    {
        $path = Arr::get($this->getLivewire()->data, str($statePath)->replaceFirst('data.', '')->toString());

        if (empty($path)) return [];

        return collect($path)
            ->mapWithKeys(fn ($v, $k) => [$k => ['url' => $this->getDisk()->url($v)]])->toArray();
    }


    public function removeUploadedFile(string $fileKey): string | TemporaryUploadedFile | null
    {
        $files = $this->getState();

        if (is_string($files)) {
            $file = $files;
        } else {
            $file = $files[$fileKey] ?? null;
        }

        if (!$file) {
            return null;
        }

        if (is_string($file)) {
            $this->getDisk()->delete($file);
        } elseif ($file instanceof TemporaryUploadedFile) {
            $file->delete();
        }

        if (!is_string($files)) {
            unset($files[$fileKey]);
        }

        $this->state([]);

        return $file;
    }

    /**
     * @return array<mixed>
     */
    public function getValidationRules(): array
    {
        $hasUploadedFiles = array_filter(Arr::wrap($this->getState()), fn (TemporaryUploadedFile | string $file): bool => $file instanceof TemporaryUploadedFile);

        if (empty($hasUploadedFiles)) return [];

        return parent::getValidationRules();
    }
}
