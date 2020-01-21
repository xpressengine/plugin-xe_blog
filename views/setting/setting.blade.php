@php
 use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
@endphp

@section('page_title')
    <h2>{{ xe_trans('xe_blog::settingBlog') }}</h2>
@endsection

<ul class="nav nav-tabs">
    <li @if ($type === 'config') class="active" @endif><a href="{{ route('blog.setting.setting', ['type' => 'config']) }}">설정</a></li>
    <li @if ($type === 'taxonomy') class="active" @endif><a href="{{ route('blog.setting.setting', ['type' => 'taxonomy']) }}">Taxonomy</a></li>
    <li @if ($type === 'skin') class="active" @endif><a href="{{ route('blog.setting.setting', ['type' => 'skin']) }}">스킨</a></li>
</ul>

@if ($type === 'config')
    <form method="post" action="{{ route('blog.setting.setting') }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-12">
                <div class="panel-group">
                    <div class="panel">
                        <div class="panel-collapse collapse in">
                            <div class="panel-body">
                                <div class="panel">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="clearfix">
                                                    <label>글 정렬</label>
                                                </div>
                                                <select name="orderType" class="form-control">
                                                    <option value="{{ BlogConfigHandler::ORDER_TYPE_NEW  }}" @if ($config->get('orderType') === BlogConfigHandler::ORDER_TYPE_NEW) selected @endif>최신순</option>
                                                    <option value="{{ BlogConfigHandler::ORDER_TYPE_UPDATE  }}" @if ($config->get('orderType') === BlogConfigHandler::ORDER_TYPE_UPDATE) selected @endif>업데이트순</option>
                                                    <option value="{{ BlogConfigHandler::ORDER_TYPE_RECOMMEND  }}" @if ($config->get('orderType') === BlogConfigHandler::ORDER_TYPE_RECOMMEND) selected @endif>추천순</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="clearfix">
                                                    <label>새 글 표시</label>
                                                </div>
                                                <input type="text" name="newBlogTime" class="form-control" value="{{ $config->get('newBlogTime') }}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="clearfix">
                                                    <label>추천</label>
                                                </div>
                                                <select name="assent" class="form-control">
                                                    <option value=true @if ($config->get('assent') === true) selected @endif>사용함</option>
                                                    <option value=false @if ($config->get('assent') === false) selected @endif>사용안함</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="clearfix">
                                                    <label>비추천</label>
                                                </div>
                                                <select name="dissent" class="form-control">
                                                    <option value=true @if ($config->get('dissent') === true) selected @endif>사용함</option>
                                                    <option value=false @if ($config->get('dissent') === false) selected @endif>사용안함</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="clearfix">
                                                    <label>글 등록 알림 메일</label>
                                                </div>
                                                <input type="text" class="form-control" name="alertMail" value="{{ $config->get('alertMail') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="clearfix">
                                                    <label>글 삭제</label>
                                                </div>
                                                <select name="deleteToTrash" class="form-control">
                                                    <option value=true @if ($config->get('deleteToTrash') === true) selected @endif>휴지통으로 이동</option>
                                                    <option value=false @if ($config->get('deleteToTrash') === false) selected @endif>영구 삭제</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="xe-btn xe-btn-positive">저장</button>
    </form>
@elseif ($type === 'taxonomy')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel-group">
                <div class="panel">
                    <div class="panel-body">
                        <form method="post" action="{{ route('blog.setting.store_taxonomy') }}">
                            {!! csrf_field() !!}

                            {!! uio('langText', ['name'=>'name']) !!}

                            <span>slug 사용 여부</span>
                            <input type="checkbox" name="use_slug">
                            <input type="text" name="slug_url" placeholder="slug 주소">

                            <button type="submit" class="xe-btn">생성</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@elseif ($type === 'skin')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel-group">
                <div class="panel">
                    <div class="panel-body">
                        <div class="clearfix">
                            <label>상세보기 스킨</label>
                        </div>
                        {!! $skinSection !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
