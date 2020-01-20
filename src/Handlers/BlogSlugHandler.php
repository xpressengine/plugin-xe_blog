<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Plugins\XeBlog\Models\BlogSlug;

class BlogSlugHandler
{
    protected $reserved = [];

    public function setReserved($slug)
    {
        if (is_array($slug) === true) {
            $this->reserved = array_merge($this->reserved, $slug);
        } else {
            $this->reserved[] = $slug;
        }
    }

    public function storeSlug($blog, $inputs)
    {
        $title = $inputs['title'];
        if (isset($inputs['slug']) === true) {
            $title = $inputs['slug'];
        }

        $slug = $this->make($title, $blog->id);

        $newBlogSlug = new BlogSlug();
        $newBlogSlug->fill([
            'target_id' => $blog->id,
            'instance_id' => 'blog',
            'slug' => $slug,
            'title' => $title
        ]);
        $newBlogSlug->save();
    }

    public function make($slug, $id)
    {
        $slug = $this->convert($slug);

        $increment = 0;
        if (in_array($slug, $this->reserved) === true) {
            ++$increment;
        }

        while ($this->has($slug, $increment) === true) {
            $slugInfo = BlogSlug::where('slug', $this->makeIncrement($slug, $increment))->first();
            if ($slugInfo->id === $id) {
                break;
            }

            ++$increment;
        }

        return $this->makeIncrement($slug, $increment);
    }

    public function convert($title, $slug = null)
    {
        if ($slug !== null) {
            $title = $slug;
        }

        $title = trim($title);
        $title = str_replace(' ', '-', $title);

        $slug = '';
        $len = mb_strlen($title);
        for ($i = 0; $i < $len; $i++) {
            $ch = mb_substr($title, $i, 1);
            $code = $this->utf8Ord($ch);

            if (($code <= 47 && $code !== 45) ||
                ($code >= 58 && $code <= 64) ||
                ($code >= 91 && $code <= 96) ||
                ($code >= 123 && $code <= 127)) {
                continue;
            }

            $slug .= $ch;
        }

        $slug = str_replace('--', '-', $slug);

        return $slug;
    }

    public function has($slug, $increment = 0)
    {
        $slug = $this->makeIncrement($slug, $increment);

        $query = BlogSlug::where('slug', $slug);

        return $query->exists();
    }

    protected function makeIncrement($slug, $increment)
    {
        if ($increment > 0) {
            $slug .= '-' . $increment;
        }

        return $slug;
    }

    public function utf8Ord($ch)
    {
        $len = strlen($ch);
        if ($len <= 0) {
            return false;
        }
        $h = ord($ch[0]);
        if ($h <= 0x7F) {
            return $h;
        }
        if ($h < 0xC2) {
            return false;
        }
        if ($h <= 0xDF && $len>1) {
            return ($h & 0x1F) <<  6 | (ord($ch[1]) & 0x3F);
        }
        if ($h <= 0xEF && $len>2) {
            return ($h & 0x0F) << 12 | (ord($ch[1]) & 0x3F) << 6 | (ord($ch[2]) & 0x3F);
        }
        if ($h <= 0xF4 && $len>3) {
            return ($h & 0x0F) << 18 | (ord($ch[1]) & 0x3F) << 12 | (ord($ch[2]) & 0x3F) << 6 | (ord($ch[3]) & 0x3F);
        }
        return false;
    }
}
