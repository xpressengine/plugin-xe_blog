<?php

namespace Xpressengine\Plugins\Post;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Migrations
{
    const META_TABLE_NAME = 'post_meta_data';
    const FAVORITE_TABLE_NAME = 'post_favorites';
    const TAXONOMY_TABLE_NAME = 'blog_taxonomy';

    public function checkInstalled()
    {
        if ($this->checkExistMetaTable() === false) {
            return false;
        }

        if ($this->checkExistFavoriteTable() === false) {
            return false;
        }

        if ($this->checkExistTaxonomyTable() === false) {
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

        if ($this->checkExistTaxonomyTable() === false) {
            $this->createTaxonomyTable();
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
        Schema::create(self::FAVORITE_TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');

            $table->string('post_id', 36);
            $table->string('user_id', 36);

            $table->timestamps();

            $table->index(['post_id', 'user_id']);
        });
    }

    protected function checkExistTaxonomyTable()
    {
        return Schema::hasTable(self::TAXONOMY_TABLE_NAME);
    }

    protected function createTaxonomyTable()
    {
        Schema::create(self::TAXONOMY_TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');

            $table->string('blog_id', 36);
            $table->integer('taxonomy_item_id');

            $table->index('blog_id');
        });
    }
}
