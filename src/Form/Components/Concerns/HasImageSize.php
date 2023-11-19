<?php

namespace Apsonex\FilamentSimpleFile\Form\Components\Concerns;

use Closure;

trait HasImageSize
{
    protected int | Closure | null $imageHeight = null;

    public function imageHeight(int | Closure | null $imageHeight): static
    {
        $this->imageHeight = $imageHeight;

        return $this;
    }

    public function getImageHeight(): ?int
    {
        return $this->evaluate($this->imageHeight);
    }

    protected bool | Closure | null $aspectVideoView = null;

    public function aspectVideoView(bool | Closure | null $aspectVideoView = true): static
    {
        $this->aspectVideoView = $aspectVideoView;

        return $this;
    }

    public function getAspectVideoView(): ?bool
    {
        return $this->evaluate($this->aspectVideoView);
    }
}
