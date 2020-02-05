{{ XeFrontend::css('plugins/xe_blog/assets/block-editor-dynamic-fields.css')->load() }}
{{ XeFrontend::js('plugins/xe_blog/assets/js/boldjournal-block-style.js')->load() }}

<form method="post" action="{{ route('blog.store') }}" enctype="multipart/form-data" style="padding-bottom: 40px;">
    {!! editor('xe_blog', [
        'content' => Request::old('content'),
        'cover' => true,
    ]) !!}

    <br>
    <br>

    <fieldset style="margin: 40px;">
        <legend>문서 설정 (사이드바로 이동 예정)</legend>

        <div class="xe-form-group">
            <label for="f-title">제목</label>
            <input type="text" id="f-title" class="xe-form-control" name="title" value="{{ Request::old('title') }}" placeholder="title">
        </div>

        <div class="xe-form-group">
            <label for="f-sub-title">부제목</label>
            <input type="text" id="f-sub-title" class="xe-form-control" name="sub_title" value="{{ Request::old('sub_title') }}" placeholder="sub_title">
        </div>

        <input type="text" class="xe-form-control" name="published_at" value="{{ Request::old('published_at') }}" placeholder="예약 발행(Y-m-d H:i:s)">

        <div class="xe-form-group">
            <label>공개 속성</label>
            <select name="blog_status" class="xe-form-control">
                <option value="public">공개</option>
                <option value="private">비공개</option>
                <option value="temp">임시</option>
            </select>
        </div>

        <div class="xe-form-group">
            <label>썸네일</label>
            <input class="xe-form-control" type="file" name="thumbnail">
        </div>

        <div class="xe-form-group">
            <label>커버 이미지</label>
            <input class="xe-form-control" type="file" name="cover_image">
        </div>

        <div class="xe-form-group">
            <label>배경 컬러</label>
            <input type="text" class="xe-form-control" name="background_color">
        </div>

        <div class="xe-form-group">
            <label>태그</label>
            {!! uio('uiobject/board@tag') !!}
        </div>

        <div class="xe-form-group">
            <label>Taxonomy</label>
            <div class="xe-row">
                @foreach ($taxonomies as $taxonomy)
                <div class="xe-col-md-2">
                    @if (app('xe.blog.taxonomyHandler')->getTaxonomyInstanceConfig($taxonomy->id)->get('require', false) === true)
                        <span style="color: red;">(필수)</span>
                    @else
                        <span>(선택)</span>
                    @endif
                    {!! uio('uiobject/board@select', [
                        'name' => app('xe.blog.taxonomyHandler')->getTaxonomyItemAttributeName($taxonomy->id),
                        'label' => xe_trans($taxonomy->name),
                        'items' => app('xe.blog.taxonomyHandler')->getTaxonomyItems($taxonomy->id),
                    ]) !!}
                </div>
                @endforeach
            </div>
        </div>

        <div class="xe-form-group">
            <label>Slug</label>
            <input type="text" class="xe-form-control" name="slug">
        </div>

        <div class="xe-form-group">
            <label>Gallery Banner Group ID</label>
            <input type="text" class="xe-form-control" name="gallery_group_id">
        </div>
    </fieldset>

    <section class="section-blog-block-editor-field" style="margin: 40px 0;">
        <div class="blog-block-editor-field__title-box">
            <h2 class="blog-block-editor-field__title">{{ xe_trans('xe::dynamicField') }}</h2>
        </div>

        <div class="blog-block-editor-filed-content">
            <div class="inner">
                @foreach ($dynamicFields as $dynamicField)
                    @if ($dynamicField->getConfig()->get('use') === true)
                        {!! df_create($dynamicField->getConfig()->get('group'), $dynamicField->getConfig()->get('id'), Request::all()) !!}
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <div style="padding: 40px;">
        <button type="submit" class="pull-right xe-btn xe-btn-lg xe-btn-primary"> 저장 </button>
    </div>
</form>

<script>
    $(function () {
        wp.data.dispatch('core/edit-post').toggleFeature('welcomeGuide')
        $('.editor-post-publish-button, .editor-post-trash').hide()
    })
</script>
