<div class="row">
    <div class="col-sm-12">
        <div class="panel-group">
            <div class="panel">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h3 class="panel-title">{{ xe_trans('xe_blog::manageBlog') }}</h3>
                    </div>

                    <div class="pull-right">
                        <a href="{{ route('blog.create') }}" class="xe-btn xe-btn-positive" target="_blank">생성</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>제목</th>
                            <th>작성자</th>
                            @foreach ($taxonomies as $taxonomy)
                                <th>{{ xe_trans($taxonomy->name) }}</th>
                            @endforeach
                            <th>생성일</th>
                            <th>수정일</th>
                            <th>발행일</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($blogs as $blog)
                            <tr>
                                <td>
                                    <a href="{{ route('blog.show', ['id' => $blog->id]) }}" target="_blank">{{ $blog->title }}</a>
                                </td>
                                <td>
                                    @if ($blog->user !== null)
                                        {{ $blog->user->getDisplayName() }}
                                    @else
                                        guest
                                    @endif
                                </td>
                                @foreach ($taxonomies as $taxonomy)
                                    @if ($taxonomyHandler->getBlogTaxonomyItem($blog, $taxonomy->id) !== null)
                                        <td>{{ xe_trans($taxonomyHandler->getBlogTaxonomyItem($blog, $taxonomy->id)->word) }}</td>
                                    @else
                                        <td></td>
                                    @endif
                                @endforeach
                                <td>{{ $blog->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $blog->updated_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $blog->published_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="admin-tab-info">
            <ul class="admin-tab-info-list">
                <li class="on">
                    <a href="#" class="__plugin-install-link admin-tab-info-list__link" data-type="sale_type" data-value="">전체 <span class="admin-tab-info-list__count">21</span></a>
                </li>
                <li class="">
                    <a href="#" class="__plugin-install-link admin-tab-info-list__link" data-type="sale_type" data-value="">발행됨 <span class="admin-tab-info-list__count">2</span></a>
                </li>
                <li class="">
                    <a href="#" class="__plugin-install-link admin-tab-info-list__link" data-type="sale_type" data-value="">발행예정 <span class="admin-tab-info-list__count">0</span></a>
                </li>
                <li class="">
                    <a href="#" class="__plugin-install-link admin-tab-info-list__link" data-type="sale_type" data-value="">임시글 <span class="admin-tab-info-list__count">21</span></a>
                </li>
                <li class="">
                    <a href="#" class="__plugin-install-link admin-tab-info-list__link" data-type="sale_type" data-value="">비공개 <span class="admin-tab-info-list__count">2</span></a>
                </li>
                <li class="">
                    <a href="#" class="__plugin-install-link admin-tab-info-list__link" data-type="sale_type" data-value="">휴지통 <span class="admin-tab-info-list__count">0</span></a>
                </li>
            </ul>
        </div>

        <div class="admin-table-list-box">
            <ul class="admin-table-list" style="">
                <!-- 타이틀 영역 -->
                <li class="admin-table-list__item-title">
                    <div class="item-content">
                        <div class="item-content__item item-content__item--checkbox">
                            <label class="xu-label-checkradio">
                                <input type="checkbox" name="all-check" checked="">
                                <span class="xu-label-checkradio__helper"></span>
                                <span class="xu-label-checkradio__text blind">전체 선택</span>
                            </label>
                        </div>
                        <div class="item-content__item item-content__item--title">제목</div>
                        <div class="item-content__item">작성자</div>
                        <div class="item-content__item">텍소노미명</div>
                        <div class="item-content__item">텍소노미명</div>
                        <div class="item-content__item">텍소노미명</div>
                        <div class="item-content__item">텍소노미명</div>
                        <div class="item-content__item item-content__item--date">일자</div>
                    </div>
                </li>
                <!-- //타이틀 영역 -->
                <li>
                    <div class="item-content">
                        <div class="item-content__item item-content__item--checkbox">
                            <label class="xu-label-checkradio">
                                <input type="checkbox" name="text1" checked="">
                                <span class="xu-label-checkradio__helper"></span>
                                <span class="xu-label-checkradio__text blind">글선택</span>
                            </label>
                        </div>
                        <div class="item-content__item item-content__item--title">
                            <span class="item-content__item-info-text">임시글, 비공개</span>
                            <a href="#" class="item-content__item-link">제목제목제목제목제목제목제목제목제목제목제목제목제목제목제목제목제목제목제목제목제목</a>
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
                                <span class="item-content__item-text">adminadmin</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">column</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미2
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">issue</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미3
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">web only</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미3
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">web only</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    일자
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">
                                    <span class="item-content__item-text-date">최근 수정됨</span>
                                    <span class="item-content__item-text-date">2020-01-31</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item-content">
                        <div class="item-content__item item-content__item--checkbox">
                            <label class="xu-label-checkradio">
                                <input type="checkbox" name="text1" checked="">
                                <span class="xu-label-checkradio__helper"></span>
                                <span class="xu-label-checkradio__text blind">글선택</span>
                            </label>
                        </div>
                        <div class="item-content__item item-content__item--title">
                            <span class="item-content__item-info-text">임시글, 비공개</span>
                            <a href="#" class="item-content__item-link">제목제목제목제목제목제목제목제</a>
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
                                <span class="item-content__item-text">adminadmin</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">column</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미2
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">issue</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미3
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">web only</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미3
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">web only</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    일자
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">
                                    <span class="item-content__item-text-date">최근 수정됨</span>
                                    <span class="item-content__item-text-date">2020-01-31</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item-content">
                        <div class="item-content__item item-content__item--checkbox">
                            <label class="xu-label-checkradio">
                                <input type="checkbox" name="text1" checked="">
                                <span class="xu-label-checkradio__helper"></span>
                                <span class="xu-label-checkradio__text blind">글선택</span>
                            </label>
                        </div>
                        <div class="item-content__item item-content__item--title">
                            <span class="item-content__item-info-text">임시글, 비공개</span>
                            <a href="#" class="item-content__item-link">제목제목제목</a>
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
                                <span class="item-content__item-text">adminadmin</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">column</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미2
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">issue</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미3
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">web only</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    텍소노미3
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">web only</span>
                            </div>
                        </div>
                        <div class="item-content__item">
                            <div class="item-content__item-inner">
                                <!-- 항목 명 -->
                                <span class="item-content__item-text-item">
                                    일자
                                </span>
                                <!-- //항목 명 -->
                                <span class="item-content__item-text">
                                    <span class="item-content__item-text-date">최근 수정됨</span>
                                    <span class="item-content__item-text-date">2020-01-31</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- <div class="setting-area-group">
            <section class="setting-area">

            </section>
        </div> -->

    </div>
</div>
