<?php

namespace Apsonex\FilamentSimpleFile\Form\Components\Concerns;

use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Validation\ValidationException;
use Apsonex\FilamentSimpleFile\Form\Components\Image;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait FileRules
{
    protected array|Closure|null $acceptedFileTypes = null;

    protected int|Closure|null $maxSize = null;

    protected int|Closure|null $minSize = null;

    protected bool|Closure $required = false;

    public function acceptedFileTypes(array | Arrayable | Closure $types): static
    {
        $this->acceptedFileTypes = $types;

        $this->rule(static function (Image $component) {
            $types = implode(',', ($component->getAcceptedFileTypes() ?? []));

            return "mimetypes:{$types}";
        });

        return $this;
    }

    public function getAcceptedFileTypes(): ?array
    {
        $types = $this->evaluate($this->acceptedFileTypes);

        if ($types instanceof Arrayable) {
            $types = $types->toArray();
        }

        return $types;
    }

    public function image(): static
    {
        $this->acceptedFileTypes = ['image/*'];
        return $this;
    }

    public function maxSize(int | Closure | null $size): static
    {
        $this->maxSize = $size;

        $this->rule(static function (Image $component): string {
            $size = $component->getMaxSize();

            return "max:{$size}";
        });

        return $this;
    }

    public function getMaxSize(): int|null
    {
        return $this->evaluate($this->maxSize);
    }

    public function minSize(int | Closure | null $size): static
    {
        $this->minSize = $size;

        $this->rule(static function (Image $component): string {
            $size = $component->getMinSize();

            return "min:{$size}";
        });

        return $this;
    }

    public function getMinSize(): ?int
    {
        return $this->evaluate($this->minSize);
    }

    public function required(bool|Closure $condition = true): static
    {
        $this->required = $condition;
        return $this;
    }

    public function getRequired(): ?bool
    {
        return $this->evaluate($this->required) === true;
    }

    public function getFileValidationRules()
    {
        return array_filter([
            $this->getRequired() === true ? 'required' : 'nullable',
            is_array($this->getAcceptedFileTypes()) ? 'mimetypes:' . implode(',', $this->getAcceptedFileTypes()) : null,
            (($minSize = $this->getMinSize()) && is_int($minSize)) ? 'min:' . $minSize : null,
            (($maxSize = $this->getMaxSize()) && is_int($maxSize)) ? 'max:' . $maxSize : null,
        ]);
    }

    public function getRules(): array
    {
        return [
            $this->getRequired() === true ? 'required' : 'nullable',
            'string'
        ];
    }

    protected function validateFile(TemporaryUploadedFile $file)
    {
        $fieldName = str($this->getStatePath())
            ->replaceFirst('data.', '')
            ->replace('.', '_')
            ->toString();

        $validator = Validator::make(
            [$fieldName => $file],
            [$fieldName => $this->getFileValidationRules()],
            [],
            [$fieldName => $this->getLabel()]
        );

        if ($validator->fails()) {
            $file->delete();

            // $this->evaluate($this->getOnError(), ['validator' => $validator]);

            throw ValidationException::withMessages([
                $this->getStatePath() => $validator->errors()->get($fieldName)
            ]);
        }
    }

    public function getValidationRules(): array
    {
        return $this->getFileValidationRules();
    }
}
