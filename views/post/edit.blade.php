<form method="post" action="{{ route('post.update') }}">
    {!! csrf_field() !!}

    <input type="hidden" name="postId" value="{{ $post->id }}">
    <input type="text" name="title" value="{{ $post->title }}">
    <input type="text" name="sub_title" value="{{ $metaDataHandler->getSubTitle($post) }}">

    {!! editor('post', [
        'content' => $post->content,
        'cover' => true,
    ]) !!}

    <input type="text" name="published_at" value="{{ $post->published_at }}" placeholder="예약 발행(Y-m-d H:i:s)">

    <hr>
    <span>배경 컬러</span>
    <input type="text" name="background_color" value="{{ $metaDataHandler->getBackgroundColor($post) }}">

    <hr>
    <span>태그</span>
    {!! uio('uiobject/board@tag', [
        'tags' => $post->tags->toArray()
    ]) !!}

    <button type="submit" class="xe-btn">저장</button>
</form>
