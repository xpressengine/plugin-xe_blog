<form method="post" action="{{ instance_route('update', [], $instanceId) }}">
    <input type="hidden" name="postId" value="{{$item->id}}">

    카테고리

    {!! editor($instanceId, [
        'content' => $item->content,
        'cover' => true,
    ]) !!}

    태그

    <button type="submit" class="xe-btn">저장</button>
</form>
