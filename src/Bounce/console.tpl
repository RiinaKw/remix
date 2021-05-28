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
        display: none;
    }
    #remix-console-body.show {
        display: block;
    }
    #remix-console-tabs ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        border-bottom: solid 1px #000000;
        display: flex;
    }
    #remix-console-tabs li {
        width: 20%;
        line-height: 40px;
        text-align: center;
        background-color: #99cc99;
        border-right: solid 1px #000000;
    }
    #remix-console-tabs label {
        display: block;
    }
    #remix-console-tabs .active {
        background-color: #ccffcc;
    }
    #remix-console .pane {
        height: 180px;
        padding: 10px;
        display: none;
    }
    #remix-console .pane.show {
        display: block;
    }
    #remix-console-delay-pane ol {
        height: 100%;
        overflow-y: scroll;
        margin: 0;
    }
</style>
<section id="remix-console">
    <h1 id="remix-console-toggler">console</h1>
    <div id="remix-console-body">
        <nav id="remix-console-tabs">
            <ul>
                <li><label class="active" for="remix-console-delay-pane">Delay</label></li>
                <li><label for="remix-console-2nd-pane">tab2</label></li>
                <li><label for="remix-console-3rd-pane">tab3</label></li>
            </ul>
        </nav>
        <div id="remix-console-content">
            <div class="pane show" id="remix-console-delay-pane">
                <ol>
{{foreach ($delay as $item) }}
                    <li>{{ $item }}</li>
{{endforeach}}
                </ol>
            </div>
            <div class="pane" id="remix-console-2nd-pane">
                2nd
            </div>
            <div class="pane" id="remix-console-3rd-pane">
                3rd
            </div>
        </div>
    </div>
</section>
<script>
    document.getElementById('remix-console-toggler').addEventListener('click', () => {
        document.getElementById('remix-console-body').classList.toggle('show');
    })
    const tabs = document.querySelectorAll('#remix-console-tabs label');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(current => {
                current.classList.remove('active')
            })
            tab.classList.toggle('active');
            const paneId = tab.getAttribute('for')

            const panes = document.querySelectorAll('#remix-console-content .pane')
            panes.forEach(current => {
                current.classList.remove('show')
            })
            document.getElementById(paneId).classList.add('show');
        });
    })
</script>
