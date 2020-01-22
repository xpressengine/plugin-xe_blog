<form method="post" action="{{ route('blog.update') }}">
    {!! csrf_field() !!}

    <input type="hidden" name="blogId" value="{{ $blog->id }}">
    <input type="text" name="title" value="{{ $blog->title }}">
    <input type="text" name="sub_title" value="{{ $metaDataHandler->getSubTitle($blog) }}">

    {!! editor('xe_blog', [
        'content' => $blog->content,
        'cover' => true,
    ]) !!}

    <input type="text" name="published_at" value="{{ $blog->published_at }}" placeholder="예약 발행(Y-m-d H:i:s)">

    <hr>
    <span>배경 컬러</span>
    <input type="text" name="background_color" value="{{ $metaDataHandler->getBackgroundColor($blog) }}">

    <hr>
    <span>태그</span>
    {!! uio('uiobject/board@tag', [
        'tags' => $blog->tags->toArray()
    ]) !!}

    <hr>
    <span>Slug</span>
    <input type="text" name="slug" @if ($blog->slug !== null) value="{{ $blog->slug['slug'] }}" @endif>

    <button type="submit" class="xe-btn">저장</button>
</form>
