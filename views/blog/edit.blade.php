{{ XeFrontend::css('plugins/xe_blog/assets/block-editor-dynamic-fields.css')->load() }}
{{ XeFrontend::js('plugins/xe_blog/assets/js/boldjournal-block-style.js')->load() }}

<form method="post" action="{{ route('blog.update') }}" enctype="multipart/form-data">
    {!! csrf_field() !!}
    <input type="hidden" name="blogId" value="{{ $blog->id }}">

    <fieldset>
        <legend>제목 및 부제목 영역 (이동 예정)</legend>
        <div class="xe-form-group">
            <label for="f-title">제목</label>
            <input type="text" id="f-title" class="xe-form-control" name="title" value="{{ $blog->title }}" placeholder="title">
        </div>

        <div class="xe-form-group">
            <label for="f-title">부제목</label>
            <input type="text" id="f-sub-title" class="xe-form-control" name="sub_title" value="{{ $metaDataHandler->getSubTitle($blog) }}" placeholder="sub_title">
        </div>
    </fieldset>

    {!! editor('xe_blog', [
        'content' => $blog->content,
        'cover' => true,
    ]) !!}

    <fieldset style="margin: 40px;">
        <legend>Metadata (에디터 사이드바로 이동 예정)</legend>
        <input type="text" class="xe-form-control" name="published_at" value="{{ $blog->published_at }}" placeholder="예약 발행(Y-m-d H:i:s)">

        <hr>
        <p>공개 속성</p>
        <select name="blog_status">
            <option value="public" @if ($blog->isPublic() === true) selected @endif>공개</option>
            <option value="private" @if ($blog->isPrivate() === true) selected @endif>비공개</option>
            <option value="temp" @if ($blog->isTemp() === true) selected @endif>임시</option>
        </select>

        <hr>
        <p>썸네일</p>
        <input type="file" name="thumbnail">

        <hr>
        <p>커버 이미지</p>
        <input type="file" name="cover_image">

        <hr>
        <p>배경 컬러</p>
        <input type="text" class="xe-form-control" name="background_color" value="{{ $metaDataHandler->getBackgroundColor($blog) }}">

        <hr>
        <p>태그</p>
        {!! uio('uiobject/board@tag', [
            'tags' => $blog->tags->toArray()
        ]) !!}

        <hr>
        <p>Taxonomy</p>
        @foreach ($taxonomies as $taxonomy)
            {!! uio('uiobject/board@select', [
                'name' => app('xe.blog.taxonomyHandler')->getTaxonomyItemAttributeName($taxonomy->id),
                'label' => xe_trans($taxonomy->name),
                'items' => app('xe.blog.taxonomyHandler')->getTaxonomyItems($taxonomy->id),
                'value' => app('xe.blog.taxonomyHandler')->getBlogTaxonomyItem($blog, $taxonomy->id)['id']
            ]) !!}
        @endforeach

        <hr>
        <p>Slug</p>
        <input type="text" class="xe-form-control" name="slug" @if ($blog->slug !== null) value="{{ $blog->slug['slug'] }}" @endif>

        <hr>
        <p>Gallery Banner Group ID</p>
        <input type="text" class="xe-form-control" name="gallery_group_id" value="{{ $metaDataHandler->getGalleryGroupId($blog) }}">
    </fieldset>

    <section class="section-blog-block-editor-field">
        <div class="blog-block-editor-field__title-box">
            <h2 class="blog-block-editor-field__title">{{ xe_trans('xe::dynamicField') }}</h2>
        </div>
        <div class="blog-block-editor-filed-content">
            <div class="inner">
                @foreach ($dynamicFields as $dynamicField)
                    @if ($dynamicField->getConfig()->get('use') === true)
                        {!! $dynamicField->getSkin()->edit($blog->getAttributes()) !!}
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <div style="margin: 40px;">
        <button type="submit" class="pull-right xe-btn xe-btn-lg xe-btn-primary"> 저장 </button>
    </div>
</form>
