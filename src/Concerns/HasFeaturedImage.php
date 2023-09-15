<?php

namespace Apsonex\FilamentSimpleFile\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

trait HasFeaturedImage
{
    /**
     * Update the user's profile photo.
     */
    public function updateFeaturedImage($path): void
    {
        tap($this->featured_image_path, function ($previous) use ($path) {
            $this->forceFill([
                'featured_image_path' => $path,
            ])->save();

            if ($previous) {
                Storage::disk($this->featuredImageDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteFeaturedImage()
    {
        if (is_null($this->featured_image_path)) {
            return;
        }

        Storage::disk($this->featuredImageDisk())->delete($this->featured_image_path);

        $this->forceFill([
            'featured_image_path' => null,
        ])->save();
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function featuredImageUrl(): string
    {
        return $this->featured_image_path
            ? Storage::disk($this->featuredImageDisk())->url($this->featured_image_path)
            : $this->defaultFeaturedImageUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     */
    public function defaultFeaturedImageUrl(): string
    {
        return disk()->public()->url('assets/social/social-media-share.jpg');
    }

    /**
     * Get the disk that profile photos should be stored on.
     */
    protected function featuredImageDisk(): string
    {
        return disk()->publicDiskName();
    }
}
