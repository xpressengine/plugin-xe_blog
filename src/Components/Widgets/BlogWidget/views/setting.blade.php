<div>
    <span>페이지 출력 수</span>
    <input type="text" name="perPage" @if (isset($config['perPage']) === true) value="{{ $config['perPage'] }}" @endif>
</div>
