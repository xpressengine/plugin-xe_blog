<div>
    <span>페이지 출력 수</span>
    <input type="text" name="blogPerPage" @if (isset($config['blogPerPage']) === true) value="{{ $config['blogPerPage'] }}" @endif>
</div>
