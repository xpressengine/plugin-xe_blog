setting

<div>
    <span>Taxonomy 추가</span>
    <form method="post" action="{{ route('blog.setting.store_taxonomy') }}">
        {!! csrf_field() !!}

        {!! uio('langText', ['name'=>'name']) !!}
        <button type="submit" class="xe-btn">생성</button>
    </form>
</div>
