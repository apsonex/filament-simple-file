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
use Closure;

class File extends BaseFileUpload
{
    use CanMoveFiles;
    use HasAlignment;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;
    use Concerns\HasExtraInputAttributes;

    protected string $view = "filament-simple-file::components.file";

    public function multiple(bool|Closure $condition = true): static
    {
        return $this;
    }
}
