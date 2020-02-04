@php
    use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
@endphp

@section('page_title')
    <a href="{{ route('blog.create') }}" class="xe-btn xe-btn-positive" target="_blank">생성</a>
@endsection

<form method="get" action="{{ route('blog.setting.blogs') }}">
    <div>
        <input type="text" name="titleWithContent" value="{{ Request::get('titleWithContent') }}">
    </div>

    <button type="submit" class="xe-btn">검색</button>
</form>

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
            <div class="pull-left">
                <div class="xu-form-group" style="display: inline-block;">
                    <label class="xu-form-group__label" for="selectSize1" style="margin-right: 8px;">일괄작업</label>
                    <div class="xu-form-group__box xu-form-group__box--icon-right" style="display: inline-block;">
                        <!-- <span class="xu-form-group__box-text">선택하세요.</span> -->
                        <select class="xu-form-group__control" id="selectSize1">
                        <option disabled="disabled" selected="selected">선택하세요.</option>
                        <option>휴지통으로 이동</option>
                        <option>발행</option>
                        <option>비공개</option>
                        <option>임시글</option>
                        </select>
                        <span class="xu-form-group__icon">
                            <i class="xi-angle-down-min"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="xu-button xu-button--primary">적용</button>
            </div>

            <div class="pull-left">
                <div class="xu-form-group" style="display: inline-block;">
                    <label class="xu-form-group__label" for="selectSize1" style="margin-right: 8px;">일괄작업</label>
                    <div class="xu-form-group__box xu-form-group__box--icon-right" style="display: inline-block;">
                        <!-- <span class="xu-form-group__box-text">선택하세요.</span> -->
                        <select class="xu-form-group__control" id="selectSize1">
                        <option disabled="disabled" selected="selected">선택하세요.</option>
                        <option>복구</option>
                        <option>영구 삭제</option>
                        </select>
                        <span class="xu-form-group__icon">
                            <i class="xi-angle-down-min"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="xu-button xu-button--primary">적용</button>
                <button type="button" class="xu-button xu-button--danger">휴지통 비우기</button>
            </div>

            <div class="pull-right">
                <div class="xu-form-group float-right" style="display: inline-block; width: 300px;">
                    <div class="xu-form-group__box xu-form-group__box--icon-left">
                        <input type="text" name="inputSize7" class="xu-form-group__control" placeholder="size large icon left">
                        <button type="button" class="xu-form-group__icon">
                            <i class="xi-search"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="xu-button xu-button--primary">검색</button>
            </div>
        </div>

        <div class="admin-table-list-box">
            <ul class="admin-table-list" style="">
                <li class="admin-table-list__item-title">
                    <div class="item-content">
                        <div class="item-content__item item-content__item--checkbox">
                            <label class="xu-label-checkradio">
                                <input type="checkbox" name="all-check">
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

                @foreach ($blogs as $blog)
                    <li>
                        <div class="item-content">
                            <div class="item-content__item item-content__item--checkbox">
                                <label class="xu-label-checkradio">
                                    <input type="checkbox" name="text1">
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
                                <a href="#" class="item-content__item-link">{{ $blog->title }}</a>
                                <div class="item-content__item-meta">
                                    <a href="#" class="item-content__item-meta-link">미리보기</a>,
                                    <a href="#" class="item-content__item-meta-link">편집</a>,
                                    <a href="#" class="item-content__item-meta-link item-content__item-meta-link--color-danger">휴지통</a>
                                </div>
                            </div>
                            <div class="item-content__item">
                                <div class="item-content__item-inner">
                                    <!-- 항목 명 -->
                                    <span class="item-content__item-text-item">
                                        작성자
                                    </span>
                                    <!-- //항목 명 -->
                                    <span class="item-content__item-text">
                                        @if ($blog->user !== null)
                                            {{ $blog->user->getDisplayName() }}
                                        @else
                                            guest
                                        @endif
                                    </span>
                                </div>
                            </div>

                            @foreach ($taxonomies as $taxonomy)
                                <div class="item-content__item">
                                    <div class="item-content__item-inner">
                                        <span class="item-content__item-text-item">텍소노미</span>
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
            </ul>
        </div>

        <div class="panel-footer">
            <div class="pull-left">
                <nav>
                    {!! $blogs->render() !!}
                </nav>
            </div>
        </div>
    </div>
</div>
