<form method="post" action="{{ instance_route('store', [], $instanceId) }}">
    카테고리

    {!! editor($instanceId, [
        'content' => Request::old('content'),
        'cover' => true,
    ]) !!}

    태그

    <button type="submit" class="xe-btn">저장</button>
</form>
