<div>
    <span>Taxonomy 추가</span>
    <form method="post" action="{{ route('blog.setting.store_taxonomy') }}">
        {!! csrf_field() !!}

        {!! uio('langText', ['name'=>'name']) !!}

        <span>slug 사용 여부</span>
        <input type="checkbox" name="use_slug">
        <input type="text" name="slug_url" placeholder="slug 주소">

        <button type="submit" class="xe-btn">생성</button>
    </form>
</div>
