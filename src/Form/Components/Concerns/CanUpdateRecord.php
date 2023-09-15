<?php

namespace Apsonex\FilamentSimpleFile\Form\Components\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

trait CanUpdateRecord
{
    protected Model|Closure|null $modelInstance = null;

    public function modelInstance(Model|Closure $modelInstance): static
    {
        $this->modelInstance = $modelInstance;
        return $this;
    }

    public function getModelInstance(): Model
    {
        if ($this->modelInstance instanceof Model) {
            return $this->modelInstance;
        }

        if ($this->modelInstance) {
            return $this->evaluate($this->modelInstance);
        }

        if (method_exists($this, 'getRecord')) {
            return $this->getRecord();
        }

        if (method_exists($this->getLivewire(), 'getRecord')) {
            return $this->getLivewire()->getRecord();
        }

        throw new \Exception("Model not found");
    }

    protected function updateRecord(string $targetPath): void
    {
        $model = $this->getModelInstance();

        $str = str($this->getStatePath())->replaceFirst('data.', '');

        $updateFunction = $str->replaceLast('_path', '')->prepend('Update ')->camel()->toString();

        if (method_exists($model, $updateFunction)) {
            $model->{$updateFunction}($targetPath);
            return;
        }

        $this->updateRecordWithPath($model, $this->propertyName(), $targetPath);
    }

    protected function updateRecordWithPath($model, $property, $path = null): void
    {
        tap($model->$property, function ($previous) use ($model, $property, $path) {
            $model->forceFill([$property => $path])->save();

            if ($previous) {
                $this->getDisk()->delete($previous);
            }
        });
    }

    protected function propertyName(): string
    {
        return str($this->getStatePath())->replaceFirst('data.', '')->toString();
    }

}
