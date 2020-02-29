{{ $blog->title }}
{{ $metaDataHandler->getSubTitle($blog) }}

@if ($metaDataHandler->getCoverImage($blog) !== null)
    <img src="{{ $metaDataHandler->getCoverImage($blog)->url() }}">
@endif

{!! $blog->content() !!}

@foreach ($blog->tags->toArray() as $tag)
    <span class="xe-btn xe-btn-danger-outline">#{{ $tag['word'] }}</span>
@endforeach

<a href="{{ route('blog.edit', ['blogId' => $blog->id]) }}" class="xe-btn xe-btn-positive">수정</a>
<form method="post" action="{{ route('blog.delete', ['blogId' => $blog->id]) }}">
    {!! csrf_field() !!}
    <button type="submit" class="xe-btn xe-btn-danger">삭제</button>
</form>
