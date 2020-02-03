{{ XeFrontend::css('plugins/xe_blog/assets/block-editor-dynamic-fields.css')->load() }}
{{ XeFrontend::js('plugins/xe_blog/assets/js/boldjournal-block-style.js')->load() }}

<form method="post" action="{{ route('blog.update') }}">
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
        <p>배경 컬러</p>
        <input type="text" class="xe-form-control" name="background_color" value="{{ $metaDataHandler->getBackgroundColor($blog) }}">

        <hr>
        <p>태그</p>
        {!! uio('uiobject/board@tag', [
            'tags' => $blog->tags->toArray()
        ]) !!}

        <hr>
        <p>Slug</p>
        <input type="text" class="xe-form-control" name="slug" @if ($blog->slug !== null) value="{{ $blog->slug['slug'] }}" @endif>
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
