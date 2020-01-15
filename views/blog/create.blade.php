<form method="post" action="{{ route('blog.store') }}" enctype="multipart/form-data">
    <input type="text" name="title" value="{{ Request::old('title') }}" placeholder="title">
    <input type="text" name="sub_title" value="{{ Request::old('sub_title') }}" placeholder="sub_title">

    {!! editor('xe_blog', [
        'content' => Request::old('content'),
        'cover' => true,
    ]) !!}

    <hr>
    <input type="text" name="published_at" value="{{ Request::old('published_at') }}" placeholder="예약 발행(Y-m-d H:i:s)">

    <hr>
    <span>썸네일</span>
    <input type="file" name="thumbnail">

    <hr>
    <span>커버 이미지</span>
    <input type="file" name="cover_image">

    <hr>
    <span>배경 컬러</span>
    <input type="text" name="background_color">

    <hr>
    <span>태그</span>
    {!! uio('uiobject/board@tag') !!}

    <button type="submit" class="xe-btn">저장</button>
</form>
