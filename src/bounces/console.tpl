<style>
  #remix-console {
    background-color: #ccccff;
    position: fixed;
    bottom: 0;
    right: 0;
  }
  #remix-console .RemixConsole__toggler {
    background-color: #ccccff;
    margin: 0;
    padding: 10px 20px;
    line-height: 20px;
    position: absolute;
    top: -40px;
    right: 0;
    cursor: pointer;
  }
  #remix-console .RemixConsole__body {
    width: 500px;
    height: 220px;
    position: relative;
    display: none;
  }
  #remix-console.is-open .RemixConsole__body {
    display: flex;
  }
  #remix-console .Pane {
    width: 20%;
    text-align: center;
    background-color: #99cc99;
    border-right: solid 1px #000000;
  }
  #remix-console .Pane[open] {
    background-color: #ccffcc;
  }

  #remix-console .Pane__title {
    line-height: 40px;
    cursor: pointer;
    display: block;
  }
  #remix-console .Pane__title::-webkit-details-marker {
    display: none;
  }

  #remix-console .Pane__content {
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
  #remix-console .Pane__container {
    margin: 0;
    padding: 0;
  }

  #remix-console .Delay {
    height: 100%;
    font-size: 80%;
  }
  #remix-console .Delay__item {
    margin-left: 4em;
    padding: 0.2em;
  }
  #remix-console .Delay__item.--BODY {
    background-color: lightgreen;
  }
  #remix-console .Delay__item.--TRACE {
    background-color: lightblue;
  }
  #remix-console .Delay__item.--MEMORY {
    background-color: yellow;
  }
  #remix-console .Delay__item.--TIME {
    background-color: fuchsia;
  }
  #remix-console .Delay__item.--QUERY {
    background-color: cyan;
  }
</style>
<section id="remix-console" class="RemixConsole">
  <h1 class="RemixConsole__toggler">console</h1>
  <div class="RemixConsole__body">

    <details class="Pane" open>
      <summary class="Pane__title">Delay</summary>
      <section class="Pane__content">
        <ol class="Pane__container Delay">
{{foreach ($delay as $item) }}
          <li class="Delay__item --{{ $item['type'] }}">
            [{{ $item['type'] }}] {{ $item['log'] }}
          </li>
{{endforeach}}
        </ol>
      </section>
    </details>

    <details class="Pane">
      <summary class="Pane__title">Preset</summary>
      <section class="Pane__content">
        <div class="Pane__container">{{ $preset }}</div>
      </section>
    </details>

    <details class="Pane">
      <summary class="Pane__title">3rd</summary>
      <section class="Pane__content">
        <div class="Pane__container">
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
  document.querySelector('#remix-console .RemixConsole__toggler').addEventListener('click', () => {
    elConsole.classList.toggle('is-open');
  })

  const elsDetails = document.querySelectorAll('#remix-console .Pane')
  elsDetails.forEach(elDetails => {
    elDetails.addEventListener('click', e => {
      // Hide other panes
      elsDetails.forEach(current => {
        current.open = false
      })
    })
  })

  document.querySelectorAll('#remix-console .Pane__content').forEach(elPane => {
    elPane.addEventListener('click', e => {
      // Stop propagation to <details>
      e.stopPropagation()
    })
  })
</script>
