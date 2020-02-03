@section('page_title')
    <h2>{{ xe_trans($taxonomy->name) }}</h2>
@endsection

{{ XeFrontend::css('/assets/core/settings/css/admin_menu.css')->before('/assets/core/settings/css/admin.css')->load() }}

<div class="row">
    <div class="col-sm-12">
        <div class="panel-group">
            <div class="panel">
                <div class="panel-collapse collapse in">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">Taxonomy 설정</h3>
                        </div>

                        <div class="pull-right">
                            <form method="post" action="{{ route('blog.setting.delete_taxonomy') }}">
                                <input type="hidden" name="taxonomyId" value="{{ $taxonomy->id }}">

                                {!! csrf_field() !!}

                                <button type="submit" class="xe-btn xe-btn-danger">삭제</button>
                            </form>
                        </div>
                    </div>

                    <form method="post" action="{{ route('blog.setting.update_taxonomy_config') }}">
                        <input type="hidden" name="taxonomyId" value="{{ $taxonomy->id }}">

                        {!! csrf_field() !!}
                        <div class="panel-body">
                            <div class="panel">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="clearfix">
                                                <label>글 작성 시 필수 여부</label>
                                            </div>
                                            <select name="require" class="form-control">
                                                <option value="true" @if ($taxonomyConfig->get('require', false) === true) selected @endif>{{ xe_trans('xe::use') }}</option>
                                                <option value="false" @if ($taxonomyConfig->get('require', false) === false) selected @endif>{{ xe_trans('xe::disuse') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="clearfix"><label>Taxonomy slug 사용 여부</label></div>
                                            <div class="col-sm-2">
                                                <select name="use_slug" class="form-control">
                                                    <option value="true" @if ($taxonomyConfig->get('use_slug', false) === true) selected @endif>{{ xe_trans('xe::use') }}</option>
                                                    <option value="false" @if ($taxonomyConfig->get('use_slug', false) === false) selected @endif>{{ xe_trans('xe::disuse') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-10">
                                                <input type="text" name="slug_url" class="form-control" placeholder="Slug" value="{{ $taxonomyConfig->get('slug_url') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel">
                                <button class="xe-btn xe-btn-positive">저장</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="__xe_category-tree-container" class="panel board-category">
</div>

<script type="text/javascript">
    $(function () {
        Category.init({
            load: '{{ route('manage.category.edit.item.children', ['id' => $taxonomy->id]) }}',
            add: '{{ route('manage.category.edit.item.store', ['id' => $taxonomy->id]) }}',
            modify: '{{ route('manage.category.edit.item.update', ['id' => $taxonomy->id]) }}',
            remove: '{{ route('manage.category.edit.item.destroy', ['id' => $taxonomy->id, 'force' => false]) }}',
            removeAll: '{{ route('manage.category.edit.item.destroy', ['id' => $taxonomy->id, 'force' => true]) }}',
            move: '{{ route('manage.category.edit.item.move', ['id' => $taxonomy->id]) }}'
        });
    });
</script>
