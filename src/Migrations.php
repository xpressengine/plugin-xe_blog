<?php

namespace Xpressengine\Plugins\XeBlog;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Migrations
{
    const META_TABLE_NAME = 'blog_meta_data';
    const FAVORITE_TABLE_NAME = 'blog_favorites';
    const TAXONOMY_TABLE_NAME = 'blog_taxonomy';
    const SLUG_TABLE_NAME = 'blog_slug';
    const TEMPLATE_TABLE_NAME = 'blog_templates';

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

        if ($this->checkExistSlugTable() === false) {
            return false;
        }

        if ($this->checkExistTemplateTable() === false) {
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

        if ($this->checkExistSlugTable() === false) {
            $this->createSlugTable();
        }

        if ($this->checkExistTemplateTable() === false) {
            $this->createTemplateTable();
        }
    }

    protected function checkExistMetaTable()
    {
        return Schema::hasTable(self::META_TABLE_NAME);
    }

    protected function createMetaTable()
    {
        Schema::create(self::META_TABLE_NAME, function (Blueprint $table) {
            $table->string('id', 36);

            $table->string('blog_id', 36);
            $table->string('type', 100);
            $table->text('meta_data');

            $table->index(['blog_id', 'type']);
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

            $table->string('blog_id', 36);
            $table->string('user_id', 36);

            $table->timestamps();

            $table->index(['blog_id', 'user_id']);
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
            $table->integer('taxonomy_id');
            $table->integer('taxonomy_item_id');

            $table->index('blog_id');
        });
    }

    protected function checkExistSlugTable()
    {
        return Schema::hasTable(self::SLUG_TABLE_NAME);
    }

    protected function createSlugTable()
    {
        Schema::create(self::SLUG_TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');

            $table->string('target_id', 36);
            $table->string('instance_id', 36);
            $table->string('slug', 190);
            $table->string('title', 190);

            $table->unique('slug');
            $table->index('title');
            $table->index('target_id');
        });
    }

    protected function checkExistTemplateTable()
    {
        return Schema::hasTable(self::TEMPLATE_TABLE_NAME);
    }

    protected function createTemplateTable()
    {
        Schema::create(self::TEMPLATE_TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');

            $table->string('user_id', 36)->nullable();
            $table->string('title');
            $table->text('content');

            $table->timestamps();
        });
    }
}
