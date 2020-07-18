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
        white-space: pre-wrap;
      }
      .source li {
        margin: 0;
        padding: 0;
      }
      .source .current {
        font-weight: bold;
        color: #ffffff;
        background-color: #333333;
      }
    </style>
  </head>
  <body>
    <h1>Exception {{ $status }}</h1>
    <p><?php echo $this->params['message'] ?></p>
    <p><strong>{{ $file }}</strong>, line <strong>{{ $line }}</strong></p>
{{ $target }}
  </body>
</html>
