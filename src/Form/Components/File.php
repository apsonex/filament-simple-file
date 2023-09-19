<?php

namespace Apsonex\FilamentSimpleFile\Form\Components;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Filament\Forms\Components\Concerns;
use Filament\Support\Concerns\HasAlignment;
use Filament\Forms\Components\BaseFileUpload;
use League\Flysystem\UnableToCheckFileExistence;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
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
    }

    // protected bool|null $profilePhoto = false;

    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     $this->afterStateHydrated(function (File $component, null|string|TemporaryUploadedFile $state) {
    //         if (blank($state)) {
    //             $component->state([]);
    //             return;
    //         }

    //         $files = collect(Arr::wrap($state))
    //             ->filter(static function (string $file) use ($component): bool {
    //                 try {
    //                     return blank($file) || $component->getDisk()->exists($file);
    //                 } catch (UnableToCheckFileExistence $exception) {
    //                     return false;
    //                 }
    //             })
    //             ->mapWithKeys(static fn (string $file): array => [((string) Str::uuid()) => $file])
    //             ->all();

    //         $component->state($files);

    //         // if ($state instanceof TemporaryUploadedFile) {
    //         //     // $component->validateFile($state);
    //         // }
    //     });



    //     $this->beforeStateDehydrated(static function (File $component): void {
    //         $component->saveUploadedFiles();
    //     });

    //     $this->afterStateHydrated(static function (File $component, string | array | null $state): void {
    //         if (blank($state)) {
    //             $component->state([]);

    //             return;
    //         }

    //         $files = collect(Arr::wrap($state))
    //             ->filter(static function (string $file) use ($component): bool {
    //                 try {
    //                     return blank($file) || $component->getDisk()->exists($file);
    //                 } catch (UnableToCheckFileExistence $exception) {
    //                     return false;
    //                 }
    //             })
    //             ->mapWithKeys(static fn (string $file): array => [((string) Str::uuid()) => $file])
    //             ->all();

    //         $component->state($files);
    //     });

    //     $this->afterStateUpdated(static function (File $component, $state) {
    //         if ($state instanceof TemporaryUploadedFile) {
    //             return;
    //         }

    //         if (blank($state)) {
    //             return;
    //         }

    //         if (is_array($state)) {
    //             return;
    //         }

    //         $component->state([(string) Str::uuid() => $state]);
    //     });

    //     $this->dehydrateStateUsing(static function (File $component, ?array $state): string | array | null | TemporaryUploadedFile {
    //         $files = array_values($state ?? []);

    //         if ($component->isMultiple()) {
    //             return $files;
    //         }

    //         return $files[0] ?? null;
    //     });

    //     // $this->dehydrateStateUsing(static function (File $component, ?string $state): string | array | null | TemporaryUploadedFile {
    //     //     return $state;
    //     // });

    //     // $this->dehydrateStateUsing(static function (File $component, null|string|TemporaryUploadedFile $state, Set $set): ?string {
    //     //     if (blank($state)) {
    //     //         $component->processDeleteOldFile();
    //     //         return null;
    //     //     }

    //     //     if ($state instanceof TemporaryUploadedFile) {
    //     //         $path = $component->moveFileToDesiredLocation($state);
    //     //         $component->state($path);
    //     //         return $path;
    //     //     }

    //     //     return $state;
    //     // });
    // }

    // public function saveUploadedFiles(): void
    // {
    //     dd($this->getState());
    //     $savedPath = $this->moveFileToDesiredLocation($this->getState());

    //     $this->state($savedPath);
    // }
}
