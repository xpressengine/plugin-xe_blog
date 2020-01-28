<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Plugins\XeBlog\Models\BlogTemplate;

class BlogTemplateHandler
{
    const DEFAULT_PER_PAGE = 10;

    public function store($attributes)
    {
        $newTemplate = new BlogTemplate();
        $newTemplate->fill($attributes);
        $newTemplate->save();
    }

    public function getItem($templateId)
    {
        return BlogTemplate::find($templateId);
    }

    public function getItems($attributes)
    {
        $query = new BlogTemplate();

        if (isset($attributes['title']) === true) {
            $query = $query->where('title', 'like', '%' . $attributes['title'] . '%');
        }

        if (isset($attributes['user_id']) === true) {
            $query = $query->where('user_id', $attributes['user_id']);
        }

        $perPage = self::DEFAULT_PER_PAGE;
        if (isset($attributes['per_page']) === true) {
            $perPage = $attributes['per_page'];
        }

        return $query->paginate($perPage, ['*'], 'page')->appends(array_except($attributes, 'page'));
    }

    public function drop($templateItem)
    {
        $templateItem->delete();
    }
}
