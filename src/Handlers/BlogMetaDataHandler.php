<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use XeMedia;
use XeStorage;
use Xpressengine\Plugins\XeBlog\Interfaces\Jsonable;
use Xpressengine\Plugins\XeBlog\Interfaces\Searchable;
use Xpressengine\Plugins\XeBlog\Models\Blog;
use Xpressengine\Plugins\XeBlog\Models\BlogMetaData;

class BlogMetaDataHandler implements Searchable, Jsonable
{
    const UPLOAD_PATH = 'public/blog';

    public function getItems($query, array $attributes)
    {
        return $query;
    }

    public function getTypeName()
    {
        return 'meta_data';
    }

    public function getJsonData(Blog $blog)
    {
        $data = [];
        if ($subTitle = $this->getSubTitle($blog)) {
            $data['sub_title'] = $subTitle;
        }

        if ($summary = $this->getSummary($blog)) {
            $data['summary'] = $summary;
        }

        if ($thumbnail = $this->getThumbnail($blog)) {
            $data['thumbnail_url'] = $thumbnail->url();
        }

        if ($coverImage = $this->getCoverImage($blog)) {
            $data['cover_image_url'] = $coverImage->url();
        }

        if ($backgroundColor = $this->getBackgroundColor($blog)) {
            $data['background_color'] = $backgroundColor;
        }

        return $data;
    }

    public function getSubTitle($blog)
    {
        $subTitleMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_SUB_TITLE)->get()->first();
        if ($subTitleMetaData === null) {
            return '';
        }

        return $subTitleMetaData['meta_data'];
    }

    public function getSummary($blog)
    {
        $summaryMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_SUMMARY)->get()->first();
        if ($summaryMetaData === null) {
            return '';
        }

        return $summaryMetaData['meta_data'];
    }

    public function getThumbnail($blog, $thumbnailType = 'spill', $dimension = 'L')
    {
        $thumbnailMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_THUMBNAIL)->get()->first();
        if ($thumbnailMetaData === null || !array_get($thumbnailMetaData, 'meta_data', false)) {
            return null;
        }

        $file = XeStorage::find($thumbnailMetaData['meta_data']);

        $thumbnail = null;
        if (XeMedia::is($file) === true) {
            $thumbnail = XeMedia::images()->getThumbnail(XeMedia::make($file), $thumbnailType, $dimension);
        }

        return $thumbnail;
    }

    public function getCoverImage($blog)
    {
        $coverMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_IMAGE)->get()->first();

        if ($coverMetaData === null || !array_get($coverMetaData, 'meta_data', false)) {
            return null;
        }

        $coverImageFile = XeStorage::find($coverMetaData['meta_data']);
        $coverImage = XeMedia::make($coverImageFile);

        return $coverImage;
    }

    public function getBackgroundColor($blog)
    {
        $backgroundColorMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_BACKGROUND_COLOR)->get()->first();
        if ($backgroundColorMetaData === null) {
            return '';
        }

        return $backgroundColorMetaData['meta_data'];
    }

    public function getGalleryGroupId($blog)
    {
        $galleryGroupIdMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_GALLERY_GROUP_ID)->get()->first();
        if ($galleryGroupIdMetaData === null) {
            return '';
        }

        return $galleryGroupIdMetaData['meta_data'];
    }

    public function saveMetaData($blog, $inputs)
    {
        $this->saveSubTitle($blog, $inputs);
        $this->saveSummary($blog, $inputs);
        $this->saveThumbnail($blog, $inputs);
        $this->saveCoverImage($blog, $inputs);
        $this->saveBackgroundColor($blog, $inputs);
        $this->saveGalleryGroupId($blog, $inputs);
    }

    protected function saveSubTitle($blog, $inputs)
    {
        if (isset($inputs['sub_title']) === true) {
            $subTitle = $blog->getMetaDataQuery(BlogMetaData::TYPE_SUB_TITLE)->get()->first();

            if ($subTitle === null) {
                $subTitle = new BlogMetaData();
                $subTitle->fill([
                    'blog_id' => $blog->id,
                    'type' => BlogMetaData::TYPE_SUB_TITLE,
                    'meta_data' => $inputs['sub_title']
                ]);
            } else {
                $subTitle['meta_data'] = $inputs['sub_title'];
            }

            $subTitle->save();
        }
    }

    protected function saveSummary($blog, $inputs)
    {
        if (isset($inputs['summary']) === true) {
            $summary = $blog->getMetaDataQuery(BlogMetaData::TYPE_SUMMARY)->get()->first();

            if ($summary === null) {
                $summary = new BlogMetaData();
                $summary->fill([
                    'blog_id' => $blog->id,
                    'type' => BlogMetaData::TYPE_SUMMARY,
                    'meta_data' => $inputs['summary']
                ]);
            } else {
                $summary['meta_data'] = $inputs['summary'];
            }

            $summary->save();
        }
    }

    protected function saveGalleryGroupId($blog, $inputs)
    {
        //TODO 갤러리 삭제 
        if (isset($inputs['gallery_group_id']) === true && $inputs['gallery_group_id'] !== '') {
            $galleryGroup = $blog->getMetaDataQuery(BlogMetaData::TYPE_GALLERY_GROUP_ID)->get()->first();

            if ($galleryGroup === null) {
                $galleryGroup = new BlogMetaData();
                $galleryGroup->fill([
                    'blog_id' => $blog->id,
                    'type' => BlogMetaData::TYPE_GALLERY_GROUP_ID,
                    'meta_data' => $inputs['gallery_group_id']
                ]);
            } else {
                $galleryGroup['meta_data'] = $inputs['gallery_group_id'];
            }

            $galleryGroup->save();
        }
    }

    protected function saveThumbnail($blog, $inputs, $thumbnailType = 'spill')
    {
        if (isset($inputs['thumbnail']) === true) {
            $thumbnailFile = $inputs['thumbnail'];

            // $file = XeStorage::upload($thumbnailFile, self::UPLOAD_PATH);

            // if (XeMedia::is($file) === true) {
            //     $media = XeMedia::make($file);
            //     $thumbnail = XeMedia::createThumbnails($media, $thumbnailType);
            // }

            $thumbnailMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_THUMBNAIL)->get()->first();
            if ($thumbnailMetaData === null) {
                $thumbnailMetaData = new BlogMetaData();
                $thumbnailMetaData->fill([
                    'blog_id' => $blog->id,
                    'type' => BlogMetaData::TYPE_COVER_THUMBNAIL,
                    'meta_data' => $thumbnailFile
                ]);
                $thumbnailMetaData->save();

                $file = XeStorage::find($thumbnailFile);
                if ($file !== null) {
                    XeStorage::bind($thumbnailMetaData->id, $file);
                }
            } else {
                $file = XeStorage::find($thumbnailFile);
                if ($file !== null && $thumbnailMetaData['meta_data'] !== $thumbnailFile) {
                    XeStorage::bind($thumbnailMetaData->id, $file);

                    $oldFile = XeStorage::find($thumbnailMetaData['meta_data']);
                    if ($oldFile !== null) {
                        XeStorage::unbind($thumbnailMetaData->id, $oldFile, true);
                    }
                }

                $thumbnailMetaData['meta_data'] = $thumbnailFile;
                $thumbnailMetaData->save();
            }
        }
    }

    protected function saveCoverImage($blog, $inputs)
    {
        if (isset($inputs['cover_image']) === true) {
            $coverImageFile = $inputs['cover_image'];

            $coverImageMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_IMAGE)->get()->first();
            if ($coverImageMetaData === null) {
                $coverImageMetaData = new BlogMetaData();
                $coverImageMetaData->fill([
                    'blog_id' => $blog->id,
                    'type' => BlogMetaData::TYPE_COVER_IMAGE,
                    'meta_data' => $coverImageFile
                ]);
                $coverImageMetaData->save();

                $file = XeStorage::find($coverImageFile);
                if ($file !== null) {
                    XeStorage::bind($coverImageMetaData->id, $file);
                }
            } else {
                $file = XeStorage::find($coverImageFile);
                if ($file !== null && $coverImageMetaData['meta_data'] !== $coverImageFile) {
                    XeStorage::bind($coverImageMetaData->id, $file);

                    $oldFile = XeStorage::find($coverImageMetaData['meta_data']);
                    if ($oldFile !== null) {
                        XeStorage::unbind($coverImageMetaData->id, $oldFile, true);
                    }
                }

                $coverImageMetaData['meta_data'] = $coverImageFile;
                $coverImageMetaData->save();
            }
        }
    }

    protected function saveBackgroundColor($blog, $inputs)
    {
        if (isset($inputs['background_color']) === true) {
            $backgroundColorMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_BACKGROUND_COLOR)->get()->first();

            if ($backgroundColorMetaData === null) {
                $backgroundColorMetaData = new BlogMetaData();
                $backgroundColorMetaData->fill([
                    'blog_id' => $blog->id,
                    'type' => BlogMetaData::TYPE_BACKGROUND_COLOR,
                    'meta_data' => $inputs['background_color']
                ]);
            } else {
                $backgroundColorMetaData['meta_data'] = $inputs['background_color'];
            }

            $backgroundColorMetaData->save();
        }
    }

    public function deleteMetaData($blog)
    {
        $this->deleteSubTitle($blog);
        $this->deleteSummary($blog);
        $this->deleteThumbnail($blog);
        $this->deleteCoverImage($blog);
        $this->deleteBackgroundColor($blog);
        $this->deleteGroupId($blog);
    }

    protected function deleteSubTitle($blog)
    {
        $blog->getMetaDataQuery(BlogMetaData::TYPE_SUB_TITLE)->delete();
    }

    protected function deleteSummary($blog)
    {
        $blog->getMetaDataQuery(BlogMetaData::TYPE_SUMMARY)->delete();
    }

    protected function deleteThumbnail($blog)
    {
        $thumbnailMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_THUMBNAIL)->get()->first();
        if ($thumbnailMetaData === null) {
            return;
        }

        $file = XeStorage::find($thumbnailMetaData['meta_data']);
        if ($file !== null) {
            XeStorage::unbind($thumbnailMetaData->id, $file, true);
        }

        $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_THUMBNAIL)->delete();
    }

    protected function deleteCoverImage($blog)
    {
        $coverImageMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_IMAGE)->get()->first();
        if ($coverImageMetaData === null) {
            return;
        }

        $file = XeStorage::find($coverImageMetaData['meta_data']);
        if ($file !== null) {
            XeStorage::unbind($coverImageMetaData->id, $file, true);
        }

        $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_IMAGE)->delete();
    }

    protected function deleteBackgroundColor($blog)
    {
        $blog->getMetaDataQuery(BlogMetaData::TYPE_BACKGROUND_COLOR)->delete();
    }

    protected function deleteGroupId($blog)
    {
        $blog->getMetaDataQuery(BlogMetaData::TYPE_GALLERY_GROUP_ID)->delete();
    }
}
