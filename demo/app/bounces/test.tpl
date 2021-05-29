<body>
  <h1>Remix framework</h1>

  from bounce.php : {{ $var }}<br />
  get parameter 'some' : {{ $some }}<br />

  {{foreach ($arr as $item) }}
    {{if $item == 1}}
      <b>{{ $item }}</b>
    {{elseif $item == 3}}
      <span style="color: red;">{{ $item }}</span>
    {{else}}
      {{ $item }}
    {{endif}}
  {{endforeach}}

  <div>escaped : {{ $escaped }}</div>
  <div>unescaped : {{ $unescaped }}</div>

  <div>
    object : <a href="{{ $vinyl->urlApi() }}">{{ $vinyl->title }}</a><br />
    arr : {{ $arr[0] }}
  </div>
</body>
