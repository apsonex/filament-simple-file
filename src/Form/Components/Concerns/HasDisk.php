<?php

namespace Apsonex\FilamentSimpleFile\Form\Components\Concerns;

use Closure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;

trait HasDisk
{
    protected string|Closure|null $directory = null;

    protected string|Closure|null $visibility = 'public';

    protected string|Closure|null $diskName = 'public';

    protected string|Filesystem|Closure|null $disk = null;

    protected string|Closure|null $urlPrefix = null;

    public function urlPrefix(string|Closure $urlPrefix): static
    {
        $this->urlPrefix = $urlPrefix;
        return $this;
    }

    public function directory(string|Closure|null $directory): static
    {
        $this->directory = $directory;
        return $this;
    }

    public function diskName(string|Closure|null $diskName): static
    {
        $this->diskName = $diskName;
        return $this;
    }

    public function disk(string|Filesystem|Closure|null $disk): static
    {
        if (is_string($disk)) {
            $this->diskName($disk);
            $this->disk = null;
            return $this;
        }

        if ($disk instanceof Filesystem) {
            $this->disk = $disk;
            return $this;
        }

        $this->disk = $disk;
        return $this;
    }

    public function getDiskName(): ?string
    {
        return $this->evaluate($this->diskName) ?? config('filament.default_filesystem_disk');
    }

    public function getDirectory(): ?string
    {
        return $this->evaluate($this->directory);
    }

    public function getDisk(): Filesystem
    {
        if ($this->disk instanceof Filesystem) {
            return $this->disk;
        }

        if ($this->disk instanceof Closure) {
            return $this->evaluate($this->disk);
        }

        return Storage::disk($this->getDiskName() ?: config('filesystems.default'));
    }

    public function private(): static
    {
        $this->visibility = 'private';
        return $this;
    }

    public function public(): static
    {
        $this->visibility = 'public';
        return $this;
    }

    public function visibility(string | Closure | null $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getVisibility(): string
    {
        return $this->evaluate($this->visibility);
    }

    public function getUrlPrefix(): ?string
    {
        if (is_string($this->urlPrefix)) return rtrim($this->urlPrefix, '/') . '/';

        if ($this->urlPrefix instanceof Closure) {
            return $this->evaluate($this->urlPrefix);
        }


        return explode('/placeholder', $this->getDisk()->url('/placeholder'))[0] . '/';
    }
}
