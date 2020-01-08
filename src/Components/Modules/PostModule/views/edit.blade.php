<form method="post" action="{{ instance_route('update', [], $instanceId) }}">
    <input type="hidden" name="postId" value="{{ $item->id }}">
    <input type="text" name="title" value="{{ $item->title }}">
    <input type="text" name="sub_title" value="{{ $metaDataHandler->getSubTitle($item) }}">

    {!! editor($instanceId, [
        'content' => $item->content,
        'cover' => true,
    ]) !!}

    <input type="text" name="published_at" value="{{ $item->published_at }}" placeholder="예약 발행(Y-m-d H:i:s)">

    <hr>
    <span>배경 컬러</span>
    <input type="text" name="background_color" value="{{ $metaDataHandler->getBackgroundColor($item) }}">

    <hr>
    <span>태그</span>
    {!! uio('uiobject/board@tag', [
        'tags' => $item->tags->toArray()
    ]) !!}

    <button type="submit" class="xe-btn">저장</button>
</form>
