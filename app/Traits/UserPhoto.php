<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UserPhoto
{
    /**
     * Update the user_photo.
     *
     * @param  \Illuminate\Http\UploadedFile  $photo
     * @param  string  $storagePath
     * @return void
     */
    public function updateUserPhoto(UploadedFile $file, $storagePath = 'user_photos')
    {
        tap($this->user_photo, function ($previous) use ($file, $storagePath) {
            $this->forceFill([
                'user_photo' => $file->storePublicly(
                    $storagePath, ['disk' => $this->userPhotoDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->userPhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete user_photo.
     *
     * @return void
     */
    public function deleteUserPhoto()
    {

        if (is_null($this->user_photo)) {
            return;
        }

        Storage::disk($this->userPhotoDisk())->delete($this->user_photo);

        $this->forceFill([
            'user_photo' => null,
        ])->save();
    }

    /**
     * Get the URL to the user_photo.
     */
    public function userPhotoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->user_photo
                ? Storage::disk($this->userPhotoDisk())->url($this->user_photo)
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
     * Get the disk that user_photos should be stored on.
     *
     * @return string
     */
    protected function userPhotoDisk()
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : 'public';
    }
}
