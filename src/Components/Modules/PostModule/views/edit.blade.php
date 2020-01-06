<form method="post" action="{{ instance_route('update', [], $instanceId) }}">
    <input type="hidden" name="postId" value="{{ $item->id }}">
    <input type="text" name="title" value="{{ $item->title }}">

    {!! editor($instanceId, [
        'content' => $item->content,
        'cover' => true,
    ]) !!}

    <input type="text" name="published_at" value="{{ $item->published_at }}" placeholder="예약 발행(Y-m-d H:i:s)">

    <button type="submit" class="xe-btn">저장</button>
</form>
