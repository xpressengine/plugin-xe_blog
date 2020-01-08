<?php

namespace Xpressengine\Plugins\Post;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Migrations
{
    const META_TABLE_NAME = 'post_meta_data';
    const FAVORITE_TABLE_NAME = 'post_favorites';

    public function checkInstalled()
    {
        if ($this->checkExistMetaTable() === false) {
            return false;
        }

        if ($this->checkExistFavoriteTable() === false) {
            return false;
        }

        return true;
    }

    public function install()
    {
        if ($this->checkExistMetaTable() === false) {
            $this->createMetaTable();
        }

        if ($this->checkExistFavoriteTable() === false) {
            $this->createFavoriteTable();
        }
    }

    protected function checkExistMetaTable()
    {
        return Schema::hasTable(self::META_TABLE_NAME);
    }

    protected function createMetaTable()
    {
        Schema::create(self::META_TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');

            $table->string('post_id', 36);
            $table->string('type');
            $table->text('meta_data');

            $table->index('post_id');
        });
    }

    protected function checkExistFavoriteTable()
    {
        return Schema::hasTable(self::FAVORITE_TABLE_NAME);
    }

    protected function createFavoriteTable()
    {
        return Schema::create(self::FAVORITE_TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');

            $table->string('post_id', 36);
            $table->string('user_id', 36);

            $table->timestamps();

            $table->index(['post_id', 'user_id']);
        });
    }
}
