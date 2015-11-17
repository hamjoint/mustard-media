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

namespace Hamjoint\Mustard\Media\Http\Controllers;

use Hamjoint\Mustard\Http\Controllers\Controller;
use Hamjoint\Mustard\Media\Photo;
use Illuminate\Http\Response;

class MediaController extends Controller
{
    /**
     * Return a photo as a response.
     *
     * @param integer $photoId
     * @param string $size
     * @return \Illuminate\Http\Response
     */
    public function getPhoto($photoId, $size = 'large')
    {
        $photo = Photo::findOrFail($photoId);

        switch ($size) {
            case 'large':
                return response()->download(storage_path() . '/app/' . $photo->pathLarge);
            case 'small':
                return response()->download(storage_path() . '/app/' . $photo->pathSmall);
        }

        abort(404);
    }
}
