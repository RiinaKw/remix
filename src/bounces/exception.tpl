<html lang="ja">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
      .remix-exception-source {
        margin: 0;
        padding: 0.5rem;
        list-style-type: none;
        background-color: #cccccc;
        font-family: monospace;
      }
      .remix-exception-source li {
        margin: 0;
        padding: 0.2rem;
        display: flex;
      }
      .remix-exception-source .line {
        width: 3em;
      }
      .remix-exception-source .code {
        white-space: pre-wrap;
      }
      .remix-exception-source .current {
        font-weight: bold;
        color: #ffffff;
        background-color: #333333;
      }
      #remix-exception-traces {
        margin: auto 2em;
      }
      #remix-exception-traces h3 a {
        font-weight: normal;
        cursor: pointer;
      }
      #remix-exception-traces ol {
        margin: auto 2em;
      }
      .remix-exception-trace-source {
        display: none;
      }
      .remix-exception-trace-source.show {
        display: block;
      }
    </style>
  </head>
  <body>
    <h1>Exception {{ $status }} : {{ $message }}</h1>
    <p><strong>{{ $file }}</strong>, line <strong>{{ $line }}</strong></p>
    <ol class="remix-exception-source">
{{ foreach ($target as $line) }}
      <li class="{{ $line['class'] ?? '' }}">
        <div class="line">{{ $line['line'] }}</div>
        <div class="code">{{ $line['source'] }}</div>
      </li>
{{ endforeach }}
    </ol>

    <h2>trace</h2>
    <section id="remix-exception-traces">
{{ foreach ($traces as $key => $item) }}
      <article>
        <header>
          <h3>
            <a href="#remix-exception-trace-source-{{ $key }}">
              <strong >{{ $item['trace']['file'] }}</strong>,
              line <strong>{{ $item['trace']['line'] }}</strong>
              <span>
                {{ $item['trace']['class'] }}::{{ $item['trace']['function'] }}()
              </span>
            </a>
          </h3>
        </header>

        <ol id="remix-exception-trace-source-{{ $key }}"
          class="remix-exception-source remix-exception-trace-source">
{{ foreach ($item['source'] as $line) }}
          <li class="{{ $line['class'] ?? '' }}">
            <div class="line">{{ $line['line'] }}</div>
            <div class="code">{{ $line['source'] }}</div>
          </li>
{{ endforeach }}
        </ol>
      </article>
{{ endforeach }}
    </section>
    <script>
      window.onload = function() {
        document.querySelectorAll('#remix-exception-traces a').forEach(elLabel => {
          elLabel.addEventListener('click', event => {
            const targetId = elLabel.getAttribute('href')
            document.querySelector(targetId).classList.toggle('show')
            event.preventDefault()
          })
        })
      }
    </script>
  </body>
</html>
