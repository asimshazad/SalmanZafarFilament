<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait SettingPhoto
{

    /**
     * Get the URL to the setting_photo.
     */
    public function siteLogoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->site_logo
                ? Storage::disk($this->settingPhotoDisk())->url($this->site_logo)
                : $this->defaultProfilePhotoUrl();
        });
    }
    public function siteFooterLogoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->footer_logo
                ? Storage::disk($this->settingPhotoDisk())->url($this->footer_logo)
                : $this->defaultProfilePhotoUrl();
        });
    }
    public function faviconUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->favicon
                ? Storage::disk($this->settingPhotoDisk())->url($this->favicon)
                : $this->defaultFaviconPhotoUrl();
        });
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->site_title))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }

    protected function defaultFaviconPhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->site_title))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=000000&background=E3E322&size=16';
    }

    /**
     * Get the disk that setting_photos should be stored on.
     *
     * @return string
     */
    protected function settingPhotoDisk()
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : 'public';
    }
}
