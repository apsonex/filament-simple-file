<?php

namespace Apsonex\FilamentImage\Form\Components\Concerns;

use Closure;

trait ImageHook
{
    protected Closure|null $onSuccess = null;

    protected Closure|null $onError = null;

    protected Closure|null $onDelete = null;

    public function onSuccess(Closure|null $onSuccess): static
    {
        $this->onSuccess = $onSuccess;
        return $this;
    }

    public function getOnSuccess(): Closure|null
    {
        return $this->onSuccess;
    }

    public function onError(Closure|null $onError): static
    {
        $this->onError = $onError;
        return $this;
    }

    public function getOnError(): Closure|null
    {
        return $this->onError;
    }

    public function onDelete(Closure|null $onDelete): static
    {
        $this->onDelete = $onDelete;
        return $this;
    }

    public function getOnDelete(): Closure|null
    {
        return $this->onDelete;
    }
}
