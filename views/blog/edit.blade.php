
<form method="post" action="{{ route('blog.update') }}">
    {!! csrf_field() !!}

    <input type="hidden" name="blogId" value="{{ $blog->id }}">
    <input type="text" name="title" value="{{ $blog->title }}">
    <input type="text" name="sub_title" value="{{ $metaDataHandler->getSubTitle($blog) }}">

    {!! editor('xe_blog', [
        'content' => $blog->content,
        'cover' => true,
    ]) !!}

    <input type="text" name="published_at" value="{{ $blog->published_at }}" placeholder="예약 발행(Y-m-d H:i:s)">

    <hr>
    <span>배경 컬러</span>
    <input type="text" name="background_color" value="{{ $metaDataHandler->getBackgroundColor($blog) }}">

    <hr>
    <span>태그</span>
    {!! uio('uiobject/board@tag', [
        'tags' => $blog->tags->toArray()
    ]) !!}

    <hr>
    <span>Slug</span>
    <input type="text" name="slug" @if ($blog->slug !== null) value="{{ $blog->slug['slug'] }}" @endif>

    @foreach ($dynamicFields as $dynamicField)
        @if ($dynamicField->getConfig()->get('use') === true)
            {!! $dynamicField->getSkin()->edit($blog->getAttributes()) !!}
        @endif
    @endforeach

    <button type="submit" class="xe-btn">저장</button>
</form>

<!-- 개발 적용 후 삭제 필요 -->
<form>
    <!-- blog 플러그인 -->
    <section class="section-blog-block-editor-field">
        <div class="blog-block-editor-field__title-box">
            <h2 class="blog-block-editor-field__title">사용자 정의 필드</h2>
        </div>
        <div class="blog-block-editor-filed-content">
            <div class="inner">
                <!-- 다이나믹 필드 추가 영역 (스타일은 boldjournal의 Skins/DynamicField/WriterInfomationSkin/assets 에 있음) -->

                <div class="bold-block-editor-field">
                    <div class="bold-block-editor-field-content">
                        <table class="bold-block-editor-field-table">
                            <colgroup>
                                <col style="width: 33.3333%">
                                <col style="width: 66.6666%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>이름</th>
                                    <th>값</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="bold-vertical-top">
                                        <label class="blind" for="item1test1">이름</label>
                                        <input type="text" id="item1test1" class="bold-block-editor-field__input" placeholder="이름을 입력하세요." value="subtitle" />
                                        <div class="bold-block-editor-field__button-box">
                                            <button type="button" class="bold-block-editor-field__button">삭제</button>
                                        </div>
                                    </td>
                                    <td class="bold-vertical-top">
                                        <label class="blind" for="item1test2">값</label>
                                        <input type="text" id="item1test2" class="bold-block-editor-field__input" placeholder="내용을 입력하세요." value="남편이 게임중독이라구요?" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bold-vertical-top">
                                        <label class="blind" for="item2test1">이름</label>
                                        <input type="text" id="item2test1" class="bold-block-editor-field__input" placeholder="이름을 입력하세요." value="subtitle" />
                                        <div>
                                            <button type="button" class="bold-block-editor-field__button">삭제</button>
                                        </div>
                                    </td>
                                    <td class="bold-vertical-top">
                                        <label class="blind" for="item2test2">이름</label>
                                        <input type="text" id="item2test2" class="bold-block-editor-field__input" placeholder="내용을 입력하세요." value="" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="bold-block-editor-field__more-button-box">
                        <button type="button" class="bold-block-editor-field__button bold-block-editor-field__button--more">
                            <svg aria-hidden="true" role="img" focusable="false" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" class="dashicon dashicons-insert"><path d="M10 1c-5 0-9 4-9 9s4 9 9 9 9-4 9-9-4-9-9-9zm0 16c-3.9 0-7-3.1-7-7s3.1-7 7-7 7 3.1 7 7-3.1 7-7 7zm1-11H9v3H6v2h3v3h2v-3h3V9h-3V6z"></path></svg>
                            <span class="bold-block-editor-field__button--more-text">사용자 정의 필드추가</span>
                        </button>
                    </div>
                </div>

                <!-- //다이나믹 필드 추가 영역 -->
            </div>
        </div>
    </section>
    <!-- //blog 플러그인 -->
</form>
<!-- //개발 적용 후 삭제 필요 -->
