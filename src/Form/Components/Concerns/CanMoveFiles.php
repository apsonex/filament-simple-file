<?php

namespace Apsonex\FilamentSimpleFile\Form\Components\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Apsonex\FilamentSimpleFile\SVG;
use Illuminate\Support\Stringable;
use Illuminate\Support\Facades\File;
use Illuminate\Http\File as HttpFile;
use Symfony\Component\Mime\MimeTypes;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait CanMoveFiles
{

    protected bool | Closure | null $deleteOldFile = null;

    public function deleteOldFile(bool | Closure | null $deleteOldFile): static
    {
        $this->deleteOldFile = $deleteOldFile;

        return $this;
    }

    protected function moveFileToDesiredLocation(TemporaryUploadedFile $file): string
    {
        $fileData = pathinfo($file->getClientOriginalName());

        $basename = str($fileData['filename'])->slug()->append('-' . md5(Str::uuid()->toString()))->toString();

        $extension = (new MimeTypes())->getExtensions($file->getMimeType())[0] ?? 'png';

        $method = $this->visibility === 'public' ? 'storePubliclyAs' : 'storeAs';

        $directory = $this->getDirectory();

        $mime = $file->getMimeType();

        if ($mime === 'image/svg+xml') {
            $options = [
                'Content-type' => $mime
            ];
            if ($this->visibility === 'public') {
                $options['visibility'] = 'public';
            }

            $temporaryDirectory = (new TemporaryDirectory())->create();
            $tempPath = $temporaryDirectory->path(Str::random() . '.' . $extension);
            File::put($tempPath, SVG::sanitize($file->get()));
            $this->getDisk()->putFileAs(
                $this->getDirectory(),
                new HttpFile($tempPath),
                "{$basename}.{$extension}",
                $options
            );
            $temporaryDirectory->delete();
        } else {
            $stream = $file->readStream();
            $path = $this->getDirectory() . '/' . "{$basename}.{$extension}";
            $this->getDisk()->writeStream($path, $stream);
            if ($this->visibility === 'public') {
                $this->getDisk()->setVisibility($path, 'public');
            }
            fclose($stream);
        }

        $file->delete();

        $this->processDeleteOldFile();

        return "{$directory}/{$basename}.{$extension}";
    }

    protected function processDeleteOldFile(): void
    {
        if (method_exists($this, 'getRecord') && ($model = $this->getRecord()) && $this->getDeleteOldFile()) {
            $path = Arr::get(
                $model->toArray(),
                str($this->getStatePath())->whenStartsWith('data.', fn (Stringable $str) => $str->replaceFirst('data.', ''))->toString()
            );
            if ($path) {
                $this->getDisk()->delete($path);
            }
        }
    }

    public function getDeleteOldFile(): bool
    {
        return $this->evaluate($this->deleteOldFile) ?? false;
    }
}
