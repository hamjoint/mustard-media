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

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MustardMediaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->integer('photo_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->boolean('processed');
            $table->boolean('primary')->unsigned();

            $table->primary('photo_id');
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->integer('video_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->boolean('processed');
            $table->boolean('primary')->unsigned();

            $table->primary('video_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('photos');

        Schema::drop('videos');
    }
}
