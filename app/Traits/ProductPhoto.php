<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait ProductPhoto
{
    /**
     * Update the product_photo.
     *
     * @param  \Illuminate\Http\UploadedFile  $photo
     * @param  string  $storagePath
     * @return void
     */
    public function updateProductPhoto(UploadedFile $file, $storagePath = 'product_photos')
    {
        tap($this->product_photo, function ($previous) use ($file, $storagePath) {
            $this->forceFill([
                'product_photo' => $file->storePublicly(
                    $storagePath, ['disk' => $this->productPhotoDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->productPhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete product_photo.
     *
     * @return void
     */
    public function deleteProductPhoto()
    {

        if (is_null($this->product_photo)) {
            return;
        }

        Storage::disk($this->productPhotoDisk())->delete($this->product_photo);

        $this->forceFill([
            'product_photo' => null,
        ])->save();
    }

    /**
     * Get the URL to the product_photo.
     */
    public function productPhotoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->product_photo
                ? Storage::disk($this->productPhotoDisk())->url($this->product_photo)
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
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the disk that product_photos should be stored on.
     *
     * @return string
     */
    protected function productPhotoDisk()
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : 'public';
    }
}
