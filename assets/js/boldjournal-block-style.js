// 블럭 에디터 블럭 스타일 추가 (글쓰기, 수정)
$(function() {
  // 텍스트 좌우 간격
  wp.blocks.registerBlockStyle('core/paragraph', {
    name: 'boldjournal__text-indent', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '좌우여백 스타일' // 스타일의 이름
  })

  // 리드문
  wp.blocks.registerBlockStyle('core/paragraph', {
    name: 'boldjournal__text-lead', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '리드 텍스트 스타일 (여백없음)' // 스타일의 이름
  })

  wp.blocks.registerBlockStyle('core/paragraph', {
    name: 'boldjournal__text-lead--indent', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '리드 텍스트 스타일 (여백있음)' // 스타일의 이름
  })

  // 인용문
  wp.blocks.registerBlockStyle('core/quote', {
    name: 'boldjournal__text-quote', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '인용문 스타일 (여백없음)' // 스타일의 이름
  })

  wp.blocks.registerBlockStyle('core/quote', {
    name: 'boldjournal__text-quote--indent', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '인용문 스타일 (여백있음)' // 스타일의 이름
  })

  // 인용문 (글쓴이 영역)
  wp.blocks.registerBlockStyle('core/quote', {
    name: 'boldjournal__text-quote--writer', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '글 저자 스타일 (여백없음)' // 스타일의 이름
  })

  wp.blocks.registerBlockStyle('core/quote', {
    name: 'boldjournal__text-quote--writer-indent', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '글 저자 스타일 (여백있음)' // 스타일의 이름
  })

  // 구분선
  wp.blocks.registerBlockStyle('core/separator', {
    name: 'boldjournal__text-separator', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '구분선 스타일 (여백없음)' // 스타일의 이름
  })

  wp.blocks.registerBlockStyle('core/separator', {
    name: 'boldjournal__text-separator--indent', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '구분선 스타일 (여백있음)' // 스타일의 이름
  })

  // heading tag, 타이틀, 서브타이틀 (이텔릭체 적용)
  wp.blocks.registerBlockStyle('core/heading', {
    name: 'boldjournal__text-type--subtitle', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '헤딩 타이틀, 서브타이틀 스타일 (여백없음)' // 스타일의 이름
  })

  wp.blocks.registerBlockStyle('core/heading', {
    name: 'boldjournal__text-type--subtitle-indent', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '헤딩 타이틀, 서브타이틀 스타일 (여백있음)' // 스타일의 이름
  })

  // 그룹박스
  wp.blocks.registerBlockStyle('core/group', {
    name: 'boldjournal__group-type--tip', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '그룹박스 팁 스타일 (여백없음)' // 스타일의 이름
  })

  wp.blocks.registerBlockStyle('core/group', {
    name: 'boldjournal__group-type--tip-indent', // 클래스명. 'is-style-bold-p-style'로 변경됨. `is-style-`가 prefix로 붙음
    label: '그룹박스 팁 스타일 (여백있음)' // 스타일의 이름
  })
});
