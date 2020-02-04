@php
    use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
@endphp

@section('page_title')
    <div class="clearfix">
        <h2 class="pull-left">{{ xe_trans('xe_blog::manageBlog') }}</h2>
        <a href="{{ route('blog.create') }}" class="xu-button xu-button--primary pull-right">새글추가</a>
    </div>
@endsection

<div class="row">
    <div class="col-sm-12">
        <div class="admin-tab-info">
            <ul class="admin-tab-info-list">
                @foreach ($stateTypeCounts as $stateType => $count)
                    <li @if (Request::get('stateType', 'all') === $stateType) class="on" @endif>
                        <a href="{{ route('blog.setting.blogs', ['stateType' => $stateType]) }}" class="__plugin-install-link admin-tab-info-list__link">{{ xe_trans('xe_blog::' . $stateType) }} <span class="admin-tab-info-list__count">{{ $count }}</span></a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="clearfix">
            @if (Request::get('stateType', 'all') !== 'trash')
                <div class="pull-left">
                    <div class="xu-form-group" style="display: inline-block;">
                        <label class="xu-form-group__label" for="selectJob" style="margin-right: 8px;">일괄작업</label>
                        <div class="xu-form-group__box xu-form-group__box--icon-right" style="display: inline-block;">
                            <select class="xu-form-group__control __target-type-select" id="selectJob">
                                <option disabled="disabled" selected="selected" value="">선택하세요.</option>
                                <option value="trash">휴지통으로 이동</option>
                                <option value="public">발행</option>
                                <option value="private">비공개</option>
                                <option value="temp">임시글</option>
                            </select>
                            <span class="xu-form-group__icon">
                                <i class="xi-angle-down-min"></i>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="xu-button xu-button--primary __blog-state-apply-button">적용</button>
                </div>
            @else
                <div class="pull-left">
                    <div class="xu-form-group" style="display: inline-block;">
                        <label class="xu-form-group__label" for="selectTrashJob" style="margin-right: 8px;">일괄작업</label>
                        <div class="xu-form-group__box xu-form-group__box--icon-right" style="display: inline-block;">
                            <select class="xu-form-group__control __target-type-select" id="selectTrashJob">
                                <option disabled="disabled" selected="selected" value="">선택하세요.</option>
                                <option value="restore">복구</option>
                                <option value="force_delete">영구 삭제</option>
                            </select>
                            <span class="xu-form-group__icon">
                                <i class="xi-angle-down-min"></i>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="xu-button xu-button--primary __blog-state-apply-button">적용</button>
                    <button id="trashClearButton" type="button" class="xu-button xu-button--danger">휴지통 비우기</button>
                </div>
            @endif

            <div class="pull-right">
                <form method="get" action="{{ route('blog.setting.blogs') }}">
                    <div class="xu-form-group float-right" style="display: inline-block;">
                        <div class="xu-form-group__box xu-form-group__box--icon-left">
                            <input type="text" name="titleWithContent" class="xu-form-group__control" value="{{ Request::get('titleWithContent') }}">
                            <button type="button" class="xu-form-group__icon">
                                <i class="xi-search"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="xu-button xu-button--primary">검색</button>
                </form>
            </div>
        </div>

        <div class="admin-table-list-box">
            <form id="stateBlogForm" method="post" action="{{ route('blog.setting.set_blog_state') }}">
                {!! csrf_field() !!}
                <input id="targetState" type="hidden" name="state_type">

                <ul class="admin-table-list" style="">
                    <li class="admin-table-list__item-title">
                        <div class="item-content">
                            <div class="item-content__item item-content__item--checkbox">
                                <label class="xu-label-checkradio">
                                    <input type="checkbox" class="__blog-id-all-checkbox">
                                    <span class="xu-label-checkradio__helper"></span>
                                    <span class="xu-label-checkradio__text blind">전체 선택</span>
                                </label>
                            </div>
                            <div class="item-content__item item-content__item--title">제목</div>
                            <div class="item-content__item">작성자</div>
                            @foreach ($taxonomies as $taxonomy)
                                <div class="item-content__item">{{ xe_trans($taxonomy->name) }}</div>
                            @endforeach
                            <div class="item-content__item item-content__item--date">일자</div>
                        </div>
                    </li>

                    @if ($blogs->count() > 0)
                        @foreach ($blogs as $blog)
                            <li @if ($blog->isPublic() === true) class="on" @endif>
                                <div class="item-content">
                                    <div class="item-content__item item-content__item--checkbox">
                                        <label class="xu-label-checkradio">
                                            <input type="checkbox" class="__blog-id-checkbox" name="blogIds[]" value="{{ $blog->id }}">
                                            <span class="xu-label-checkradio__helper"></span>
                                            <span class="xu-label-checkradio__text blind">글선택</span>
                                        </label>
                                    </div>
                                    <div class="item-content__item item-content__item--title">
                                        <span class="item-content__item-info-text">
                                            @if ($blog->isTemp() === true)
                                                임시글
                                            @endif
                                            @if ($blog->isPublishReserved() === true)
                                                예약됨
                                            @endif
                                            @if ($blog->isPrivate() === true)
                                                비공개
                                            @endif
                                        </span>
                                        <a href="{{ route('blog.show', ['blogId' => $blog->id]) }}" class="item-content__item-link" target="_blank">{{ $blog->title }}</a>
                                        <div class="item-content__item-meta">
                                            <a href="{{ route('blog.edit', ['blogId' => $blog->id]) }}" class="item-content__item-meta-link" target="_blank">편집</a>
                                            @if (Request::get('stateType', 'all') !== 'trash')
                                                <a href="#" class="item-content__item-meta-link item-content__item-meta-link--color-danger  __set-blog-trash">휴지통</a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="item-content__item">
                                        <div class="item-content__item-inner">
                                            <span class="item-content__item-text-item">작성자</span>
                                            <span class="item-content__item-text">
                                                @if ($blog->user !== null)
                                                    {{ $blog->user->getDisplayName() }}
                                                @else
                                                    Guest
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    @foreach ($taxonomies as $taxonomy)
                                        <div class="item-content__item">
                                            <div class="item-content__item-inner">
                                                <span class="item-content__item-text-item">{{ xe_trans($taxonomy->name) }}</span>
                                                <span class="item-content__item-text">
                                                    @if ($taxonomyHandler->getBlogTaxonomyItem($blog, $taxonomy->id) !== null)
                                                        {{ xe_trans($taxonomyHandler->getBlogTaxonomyItem($blog, $taxonomy->id)->word) }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="item-content__item item-content__item--date">
                                        <div class="item-content__item-inner">
                                            <span class="item-content__item-text-item">일자</span>

                                            <span class="item-content__item-text">
                                                @if ($blog->isPublished() === true)
                                                    <span class="item-content__item-text-date">최근 수정일</span>
                                                    <span class="item-content__item-text-date" @if ($blog->updated_at->getTimestamp() > strtotime('-1 days')) data-xe-timeago="{{ $blog->updated_at }}" @endif>{{ $blog->updated_at->format('Y-m-d H:i:s') }}</span>
                                                @else
                                                    <span class="item-content__item-text-date">예약됨</span>
                                                    <span class="item-content__item-text-date" @if ($blog->published_at->getTimestamp() < strtotime('-1 days')) data-xe-timeago="{{ $blog->published_at }}" @endif>{{ $blog->published_at->format('Y-m-d H:i:s') }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @else
                        <li>
                            <p class="no-data">등록된 글이 없습니다.</p>
                        </li>
                    @endif
                </ul>
            </form>
        </div>

        @if ($blogs->count() > 0)
        <div class="panel-footer">
            <div class="text-center" style="padding: 24px 0;">
                <nav>
                    {!! $blogs->render() !!}
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

<form id="trashClearForm" method="post" action="{{ route('blog.setting.trash_clear') }}">
    {!! csrf_field() !!}
</form>

<script>
    $(function () {
        $('.__blog-id-all-checkbox').click(function () {
            $('.__blog-id-checkbox').prop('checked', $(this).prop('checked'))
        })

        $('.__blog-id-checkbox').click(function () {
            if ($(this).prop('checked') == false) {
                $('.__blog-id-all-checkbox').prop('checked', false)
            } else {
                var isAllCheck = true;
                $('.__blog-id-checkbox').each(function (index, item) {
                    if ($(item).prop('checked') == false) {
                        isAllCheck = false;
                    }
                })
                $('.__blog-id-all-checkbox').prop('checked', isAllCheck)
            }
        })

        $('.__target-type-select').change(function () {
            $('#targetState').val($(this).val())
        })

        $('.__blog-state-apply-button').click(function () {
            if ($('#targetState').val() == '') {
                alert('작업을 선택하세요.')
                return
            }

            if (confirm('일괄작업을 진행하시겠습니까?') == false) {
                return;
            }

            $('#stateBlogForm').submit()
        })

        $('#trashClearButton').click(function () {
            if (confirm('휴지통의 모든 글을 삭제하시겠습니까?') == false) {
                return
            }

            $('#trashClearForm').submit()
        })

        $('.__set-blog-trash').click(function () {
            if (confirm('휴지통으로 이동하시겠습니까?') == false) {
                return
            }

            $('.__blog-id-all-checkbox').prop('checked', false)
            $('.__blog-id-checkbox').prop('checked', false)
            $(this).closest('.item-content').find('.__blog-id-checkbox').prop('checked', true)

            $('#targetState').val('trash')
            $('#stateBlogForm').submit()
        })
    })
</script>
