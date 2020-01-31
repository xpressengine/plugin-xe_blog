{{ XeFrontend::css('plugins/xe_blog/assets/block-editor-dynamic-fields.css')->load() }}

<form method="post" action="{{ route('blog.store') }}" enctype="multipart/form-data" style="padding-bottom: 40px;">
    <fieldset>
        <legend>제목 및 부제목 영역 (이동 예정)</legend>
        <div class="xe-form-group">
            <label for="f-title">제목</label>
            <input type="text" id="f-title" class="xe-form-control" name="title" value="{{ Request::old('title') }}" placeholder="title">
        </div>

        <div class="xe-form-group">
            <label for="f-title">부제목</label>
            <input type="text" id="f-sub-title" class="xe-form-control" name="sub_title" value="{{ Request::old('sub_title') }}" placeholder="sub_title">
        </div>
    </fieldset>

    {!! editor('xe_blog', [
        'content' => Request::old('content'),
        'cover' => true,
    ]) !!}


    <fieldset style="margin: 40px;">
        <legend>Metadata (에디터 사이드바로 이동 예정)</legend>
        <input type="text" class="xe-form-control" name="published_at" value="{{ Request::old('published_at') }}" placeholder="예약 발행(Y-m-d H:i:s)">

        <hr>
        <p>썸네일</p>
        <input type="file" name="thumbnail">

        <hr>
        <p>커버 이미지</p>
        <input type="file" name="cover_image">

        <hr>
        <p>배경 컬러</p>
        <input type="text" class="xe-form-control" name="background_color">

        <hr>
        <p>태그</p>
        {!! uio('uiobject/board@tag') !!}

        <hr>
        <p>Taxonomy</p>
        @foreach ($taxonomies as $taxonomy)
            {!! uio('uiobject/board@select', [
                'name' => app('xe.blog.taxonomyHandler')->getTaxonomyItemAttributeName($taxonomy->id),
                'label' => xe_trans($taxonomy->name),
                'items' => app('xe.blog.taxonomyHandler')->getTaxonomyItems($taxonomy->id),
            ]) !!}
        @endforeach

        <hr>
        <p>Slug</p>
        <input type="text" class="xe-form-control" name="slug">
    </fieldset>

    <section class="section-blog-block-editor-field" style="margin: 40px;">
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

    <div style="margin: 40px;">
        <button type="submit" class="pull-right xe-btn xe-btn-lg xe-btn-primary"> 저장 </button>
    </div>
</form>

{{ XeFrontend::js('assets/core/xe-ui-component/js/xe-page.js')->load() }}
<fieldset  style="margin: 40px;">
        <legend>템플릿 관리 (에디터 사이드바로 이동 예정)</legend>
        <div class="form-group">
            <span>템플릿 저장</span>
            <div>
                <input id="template_title" type="text" name="template_title" placeholder="템플릿 이름">
                <button id="btn_template_store" type="button">템플릿 저장</button>
            </div>

            <div>
                <button id="btn_template_index" type="button" data-url="{{ route('blog.template.get_items') }}">템플릿 보기</button>
                <div id="template_index"></div>
            </div>
        </div>
</fieldset>

<script>
    $(function () {
        $('#btn_template_store').click(function () {
            var content = window.wp.data.select('core/editor').getEditedPostContent()
            var title = $('#template_title').val()

            XE.post('/xe_blog/template/store', {
                'title': title,
                'content': content
            }).then(response => {
                if (response.data.result == true) {
                    alert('템플릿이 저장되었습니다.')
                }
            })
        })

        $('#btn_template_index').click(function () {
            var url = $(this).data('url')

            XE.get(url).then(response => {
                console.log(response)
            })
        })
    })
</script>
