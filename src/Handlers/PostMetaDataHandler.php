<?php

namespace Xpressengine\Plugins\Post\Handlers;

use XeMedia;
use XeStorage;
use Xpressengine\Plugins\Post\Models\MetaData;

class PostMetaDataHandler
{
    const UPLOAD_PATH = 'public/post';
    const THUMBNAIL_UPLOAD_PATH = 'public/post/thumbnail';
    const COVER_UPLOAD_PATH = 'public/post/cover';

    public function getSubTitle($post)
    {
        $subTitleMetaData = $post->getMetaDataQuery(MetaData::TYPE_SUB_TITLE)->get()->first();
        if ($subTitleMetaData === null) {
            return '';
        }

        return $subTitleMetaData['meta_data'];
    }

    public function getThumbnail($post, $thumbnailType = 'spill', $dimension = 'L')
    {
        $thumbnailMetaData = $post->getMetaDataQuery(MetaData::TYPE_COVER_THUMBNAIL)->get()->first();
        if ($thumbnailMetaData === null) {
            return null;
        }

        $file = XeStorage::find($thumbnailMetaData['meta_data']);

        $thumbnail = null;
        if (XeMedia::is($file) === true) {
            $thumbnail = XeMedia::images()->getThumbnail(XeMedia::make($file), $thumbnailType, $dimension);
        }

        return $thumbnail;
    }

    public function saveMetaData($post, $inputs)
    {
        $this->saveSubTitle($post, $inputs);
        $this->saveThumbnail($post, $inputs);
    }

    protected function saveSubTitle($post, $inputs)
    {
        if (isset($inputs['sub_title']) === true) {
            $subTitle = $post->getMetaDataQuery(MetaData::TYPE_SUB_TITLE)->get()->first();

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

    protected function saveThumbnail($post, $inputs, $thumbnailType = 'spill')
    {
        if (isset($inputs['thumbnail']) === true) {
            $thumbnailFile = $inputs['thumbnail'];

            $file = XeStorage::upload($thumbnailFile, self::UPLOAD_PATH);

            if (XeMedia::is($file) === true) {
                $media = XeMedia::make($file);
                $thumbnail = XeMedia::createThumbnails($media, $thumbnailType);
            }

            $thumbnailMetaData = new MetaData();
            $thumbnailMetaData->fill([
                'post_id' => $post->id,
                'type' => MetaData::TYPE_COVER_THUMBNAIL,
                'meta_data' => $file->id
            ]);
            $thumbnailMetaData->save();
        }
    }
}
