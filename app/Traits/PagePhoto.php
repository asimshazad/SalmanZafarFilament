<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait PagePhoto
{
    /**
     * Update the featured_image.
     *
     * @param  \Illuminate\Http\UploadedFile  $photo
     * @param  string  $storagePath
     * @return void
     */
    public function updatePagePhoto(UploadedFile $file, $storagePath = 'page-photo')
    {
        tap($this->featured_image, function ($previous) use ($file, $storagePath) {
            $this->forceFill([
                'featured_image' => $file->storePublicly(
                    $storagePath, ['disk' => $this->pagePhotoDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->pagePhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete featured_image.
     *
     * @return void
     */
    public function deletePagePhoto()
    {

        if (is_null($this->featured_image)) {
            return;
        }

        Storage::disk($this->pagePhotoDisk())->delete($this->featured_image);

        $this->forceFill([
            'featured_image' => null,
        ])->save();
    }

    /**
     * Get the URL to the featured_image.
     */
    public function featuredImageUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->featured_image
                ? Storage::disk($this->pagePhotoDisk())->url($this->featured_image)
                : $this->defaultProfilePhotoUrl();
        });
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->title))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the disk that featured_images should be stored on.
     *
     * @return string
     */
    protected function pagePhotoDisk()
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : 'public';
    }
}
