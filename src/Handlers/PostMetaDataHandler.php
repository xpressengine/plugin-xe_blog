<?php

namespace Xpressengine\Plugins\Post\Handlers;

use Xpressengine\Plugins\Post\Models\MetaData;

class PostMetaDataHandler
{
    public function saveMetaData($post, $inputs)
    {
        $this->saveSubTitle($post, $inputs);
    }

    protected function saveSubTitle($post, $inputs)
    {
        if (isset($inputs['sub_title']) === true) {
            $subTitle = $post->metaData()->where('type', MetaData::TYPE_SUB_TITLE)->get()->first();

            if ($subTitle === null) {
                $subTitle = new MetaData();
                $subTitle->fill([
                    'post_id' => $post->id,
                    'type' => MetaData::TYPE_SUB_TITLE,
                    'meta_data' => $inputs['sub_title']
                ]);
            } else {
                $subTitle['meta_data'] = $inputs['sub_title'];
            }

            $subTitle->save();
        }
    }
}
