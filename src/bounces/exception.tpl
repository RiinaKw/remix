<html lang="ja">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
      .source {
        margin: 0;
        padding: 0.5rem;
        list-style-type: none;
        background-color: #cccccc;
        font-family: monospace;
      }
      .source li {
        margin: 0;
        padding: 0.2rem;
        white-space: pre-wrap;
      }
      .source .current {
        font-weight: bold;
        color: #ffffff;
        background-color: #333333;
      }
    </style>
  </head>
  <body>
    <h1>Exception {{ $status }} : {{ $message }}</h1>
    <p><strong>{{ $file }}</strong>, line <strong>{{ $line }}</strong></p>
    <ol class="source">
{{ foreach ($target as $line) }}
      <li class="{{ $line['class'] ?? '' }}">{{ $line['line'] }} : {{ $line['source'] }}</li>
{{ endforeach }}
    </ol>

    <h2>trace</h2>
    <ol class="trace">
{{ foreach ($trace as $item) }}
      <li>
          <strong>{{ $item['file'] }}</strong>, line <strong>{{ $item['line'] }}</strong>
          / {{ $item['class'] }}::{{ $item['function'] }}()
      </li>
{{ endforeach }}
    </ol>
  </body>
</html>
