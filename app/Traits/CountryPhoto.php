<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait CountryPhoto
{
    /**
     * Update the country_photo.
     *
     * @param  \Illuminate\Http\UploadedFile  $photo
     * @param  string  $storagePath
     * @return void
     */
    public function updateCountryPhoto(UploadedFile $file, $storagePath = 'country-photo')
    {
        tap($this->country_photo, function ($previous) use ($file, $storagePath) {
            $this->forceFill([
                'country_photo' => $file->storePublicly(
                    $storagePath, ['disk' => $this->countryPhotoDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->countryPhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete country_photo.
     *
     * @return void
     */
    public function deleteCountryPhoto()
    {

        if (is_null($this->country_photo)) {
            return;
        }

        Storage::disk($this->countryPhotoDisk())->delete($this->country_photo);

        $this->forceFill([
            'country_photo' => null,
        ])->save();
    }

    /**
     * Get the URL to the country_photo.
     */
    public function countryPhotoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->country_photo
                ? Storage::disk($this->countryPhotoDisk())->url($this->country_photo)
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
     * Get the disk that country_photos should be stored on.
     *
     * @return string
     */
    protected function countryPhotoDisk()
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : 'public';
    }
}
