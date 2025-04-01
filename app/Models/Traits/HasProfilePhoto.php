<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Storage;

trait HasProfilePhoto
{
    /**
     * Get the URL to the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path
            ? Storage::url($this->profile_photo_path)
            : null;
    }

    /**
     * Get the avatar alias to the profile photo URL.
     */
    public function getAvatarAttribute(): ?string
    {
        return $this->profile_photo_url;
    }

}
