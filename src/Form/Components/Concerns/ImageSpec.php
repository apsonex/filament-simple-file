<?php

namespace Apsonex\FilamentSimpleFile\Form\Components\Concerns;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

trait ImageSpec
{
    protected string|Closure|null $aspectRatio = 'video';

    public function squareLayout(): static
    {
        $this->aspectRatio = 'square';
        return $this;
    }

    public function videoLayout(): static
    {
        $this->aspectRatio = 'video';
        return $this;
    }

    public function getAspectRatio(): ?string
    {
        if (is_string($this->aspectRatio)) return $this->aspectRatio;

        return $this->evaluate($this->aspectRatio);
    }

    protected string|Closure|null $defaultImageUrl = null;

    public function defaultImageUrl(string|Closure|null $defaultImageUrl): static
    {
        $this->defaultImageUrl = $defaultImageUrl;
        return $this;
    }

    public function getDefaultImageUrl(): ?string
    {
        if (is_string($this->defaultImageUrl) || is_null($this->defaultImageUrl)) return $this->defaultImageUrl;

        return $this->evaluate($this->defaultImageUrl);
    }
}
