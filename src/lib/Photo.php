<?php

/*

This file is part of Mustard.

Mustard is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Mustard is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Mustard.  If not, see <http://www.gnu.org/licenses/>.

*/

namespace Hamjoint\Mustard\Media;

use Cache;
use DomainException;
use Hamjoint\Mustard\Item;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\ImageManager;
use Queue;
use RuntimeException;
use Storage;

class Photo extends \Hamjoint\Mustard\NonSequentialIdModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'photos';

    /**
     * The database key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'photo_id';

    /**
     * Return the filesystem path for the photo.
     *
     * @param string $suffix
     * @return string
     */
    public function getPathAttribute($suffix = '')
    {
        if (!$this->exists) return public_path() . '/images/no-photo.gif';

        if (!$this->processed) return public_path() . '/images/processing-photo.gif';

        return config('mustard.storage.photo.dir', 'mustard/photos') . "/{$this->photoId}$suffix.jpg";
    }

    /**
     * Return the filesystem path for the photo's small version.
     *
     * @return string
     */
    public function getPathSmallAttribute()
    {
        return $this->getPathAttribute('_s');
    }

    /**
     * Return the filesystem path for the photo's large version.
     *
     * @return string
     */
    public function getPathLargeAttribute()
    {
        return $this->getPathAttribute('_l');
    }

    /**
     * Return the public URL for the photo.
     *
     * @param string $suffix
     * @return string
     */
    public function getUrlAttribute($suffix = '')
    {
        if (!$this->exists) {
            return Cache::rememberForever('missing_photo', function () {
                return static::placeholder('No photo provided');
            });
        }

        if (!$this->processed) {
            return Cache::rememberForever('processing_photo', function () {
                return static::placeholder(
                    "This photo is\nbeing processed\nand will appear\nshortly"
                );
            });
        }

        return "/photo/{$this->photoId}$suffix.jpg";
    }

    /**
     * Return the public URL for the photo's large version.
     *
     * @return string
     */
    public function getUrlSmallAttribute()
    {
        return $this->getUrlAttribute('_s');
    }

    /**
     * Return the public URL for the photo's large version.
     *
     * @return string
     */
    public function getUrlLargeAttribute()
    {
        return $this->getUrlAttribute('_l');
    }

    /**
     * Delete the record and data from the filesystem.
     *
     * @return void
     */
    public function delete()
    {
        $disk = Storage::disk(config('mustard.storage.disk', 'local'));

        $disk->delete($this->getPath());
        $disk->delete($this->getSmallPath());
        $disk->delete($this->getLargePath());

        parent::delete();
    }

    /**
     * Relationship to an item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Return a placeholder image containing the provided text.
     *
     * @param string $text
     * @return string
     */
    private static function placeholder($text)
    {
        $image_manager = new ImageManager(['driver' => 'imagick']);

        $image = $image_manager->canvas(640, 480, '#efefef');

        $image->text($text, 320, 240 - (70 * substr_count($text, "\n")), function ($font) {
            $font->file(base_path('vendor/webfontkit/open-sans/fonts/opensans-regular.woff'));
            $font->size(60);
            $font->color('#cbcbcb');
            $font->align('center');
            $font->valign('center');
        });

        $image_data = (string) $image->encode('gif');

        return 'data:image/gif;base64,' . base64_encode($image_data);
    }

    /**
     * Process a photo and create a record.
     *
     * @param string $file
     * @return self
     */
    public static function upload($file)
    {
        $photo = Photo::create();

        $image_manager = new ImageManager(['driver' => 'imagick']);

        $disk = Storage::disk(config('mustard.storage.disk', 'local'));

        if (!file_exists($file)) {
            RuntimeException("File $file does not exist");
        }

        $dest_dir = config('mustard.storage.photo.dir', 'mustard/photos');
        $quality = config('mustard.storage.photo.quality', 90);

        $disk->makeDirectory($dest_dir);

        $photo_id = $photo->getKey();

        Queue::push(function($job) use ($file, $quality, $dest_dir, $photo_id)
        {
            if (!file_exists($file)) RuntimeException("File $file no longer exists");

            $image_manager = new ImageManager(['driver' => 'imagick']);

            $disk = Storage::disk(config('mustard.storage.disk', 'local'));

            if ($file != "$dest_dir/$photo_id.jpg") {
                $image = $image_manager->make($file);

                $disk->put("$dest_dir/$photo_id.jpg", (string) $image->encode('jpg', $quality));

                $image->destroy();
            }

            $image = $image_manager->make($file)->heighten(83);

            $disk->put("$dest_dir/{$photo_id}_s.jpg", (string) $image->encode('jpg', $quality));

            $image->destroy();

            $image = $image_manager->make($file)->widen(500);

            $disk->put("$dest_dir/{$photo_id}_l.jpg", (string) $image->encode('jpg', $quality));

            $image->destroy();

            $photo = Photo::find($photo_id);

            $photo->processed = true;

            $photo->save();

            $job->delete();
        });

        return $photo;
    }
}
