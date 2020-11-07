<h1>Remix framework</h1>

from bounce.php : {{ $var }}

{{foreach ([1, 2, 3] as $item) }}
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
