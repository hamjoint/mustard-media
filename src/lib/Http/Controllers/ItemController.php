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
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Process and store several photos.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddPhotos(Request $request)
    {
        if (!$request->hasFile('photos')) {
            return response('', 400);
        }

        foreach ($request->file('photos') as $file) {
            $photo = Photo::upload($file->getRealPath());

            session()->push('photos', [
                'photo_id' => $photo->getKey(),
                'filename' => $file->getClientOriginalName(),
            ]);
        }
    }

    /**
     * Delete a photo.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postDeletePhoto(Request $request)
    {
        if (session()->has('photos')) {
            $photos = session('photos');

            foreach ($photos as $index => $photo) {
                if (in_array($request->input('file'), $photo)) {
                    $photo = Photo::find($photo['photo_id']);

                    $photo->delete();

                    unset($photos[$index]);

                    session('photos', $photos);

                    return response('', 200);
                }
            }
        }

        return response('', 400);
    }
}
