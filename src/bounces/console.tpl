<style>
  #remix-console {
    background-color: #ccccff;
    position: fixed;
    bottom: 0;
    right: 0;
  }
  #remix-console h1 {
    background-color: #ccccff;
    margin: 0;
    padding: 10px 20px;
    line-height: 20px;
    position: absolute;
    top: -40px;
    right: 0;
  }
  #remix-console-body {
    width: 500px;
    height: 220px;
    position: relative;
    display: none;
  }
  #remix-console-body.show {
    display: flex;
  }

  #remix-console-body details {
    width: 20%;
    text-align: center;
    background-color: #99cc99;
    border-right: solid 1px #000000;
  }
  #remix-console-body details[open] {
    background-color: #ccffcc;
  }

  #remix-console-body summary {
    line-height: 40px;
    cursor: pointer;
    display: block;
  }
  #remix-console-body summary::-webkit-details-marker {
    display: none;
  }

  #remix-console-body .pane {
    position: absolute;
    left: 0;
    top: 40px;

    width: 100%;
    height: 180px;
    overflow-y: scroll;

    text-align: left;
    border-top: solid 1px #000000;
    background-color: #ccccff;
  }
  #remix-console-body .pane-wrapper {
    margin: 0;
    padding: 10px;
  }

  #remix-console-delay ol {
    height: 100%;
    margin: 0;
    padding: 0;
    font-size: 80%;
  }
  #remix-console-delay li {
    margin-left: 2em;
    padding: 0.2em;
  }
  #remix-console-delay li.BODY {
    background-color: lightgreen;
  }
  #remix-console-delay li.TRACE {
    background-color: lightblue;
  }
  #remix-console-delay li.MEMORY {
    background-color: yellow;
  }
  #remix-console-delay li.TIME {
    background-color: fuchsia;
  }
  #remix-console-delay li.QUERY {
    background-color: cyan;
  }
</style>
<section id="remix-console">
  <h1 id="remix-console-toggler">console</h1>
  <div id="remix-console-body">

    <details id="remix-console-delay" open>
      <summary>Delay</summary>
      <section class="pane">
        <ol class="pane-wrapper">
{{foreach ($delay as $item) }}
          <li class="{{ $item['type'] }}">
            [{{ $item['type'] }}] {{ $item['log'] }}
          </li>
{{endforeach}}
        </ol>
      </section>
    </details>

    <details>
      <summary>Preset</summary>
      <section class="pane">
        <pre class="pane-wrapper">{{ var_dump($preset) }}</pre>
      </section>
    </details>

    <details>
      <summary>3rd</summary>
      <section class="pane">
        <div class="pane-wrapper">
          3rd content
        </div>
      </section>
    </details>

  </div>
</section>
<script>
  // Make sure to append it inside <body>
  const elConsole = document.getElementById('remix-console')
  const elBody = document.querySelector('body')
  elBody.appendChild(elConsole)

  // Toggle tab
  document.getElementById('remix-console-toggler').addEventListener('click', () => {
    document.getElementById('remix-console-body').classList.toggle('show');
  })

  const elsDetails = document.querySelectorAll('#remix-console-body details')
  elsDetails.forEach(elDetails => {
    elDetails.addEventListener('click', e => {
      // Hide other panes
      elsDetails.forEach(current => {
        current.open = false
      })
    })
  })

  document.querySelectorAll('#remix-console-body .pane').forEach(elPane => {
    elPane.addEventListener('click', e => {
      // Stop propagation to <details>
      e.stopPropagation()
    })
  })
</script>
