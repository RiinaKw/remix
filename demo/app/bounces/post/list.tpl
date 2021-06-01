<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <title>Blog Posts list</title>
  </head>
  <body>
      <h1>Blog Posts : {{ $action }}</h1>
      <h2>{{ $request }}</h2>
      <pre>{{ var_dump($params) }}</pre>

      <ul>
        <li>
          <strong>list</strong> : {{ $url_list }}<br />
          <form id="list" action="{{ $url_list }}" method="GET">
            <label>GET</label>
            <input type="submit" />
          </form>
        </li>
        <li>
          <strong>new form</strong> : {{ $url_new }}<br />
          <form id="new" action="{{ $url_new }}" method="GET">
            <label>GET</label>
            <input type="submit" />
          </form>
        </li>
        <li>
          <strong>insert</strong> : {{ $url_list }}<br />
          <form id="insert" action="{{ $url_list }}" method="POST">
            <label>POST</label>
            <input type="hidden" name="_method" value="PUT" />
            <input type="text" name="title" value="example" />
            <input type="submit" />
          </form>
        </li>
        <li>
          <strong>show</strong> : {{ $url_show }}<br />
          <form id="show" action="{{ $url_show }}" method="GET">
            <label>GET</label>
            <input type="submit" />
          </form>
        </li>
        <li>
          <strong>edit form</strong> : {{ $url_edit }}<br />
          <form id="edit" action="{{ $url_edit }}" method="GET">
            <input type="submit" />
          </form>
        </li>
        <li>
          <strong>update</strong> : {{ $url_show }}<br />
          <form id="update" action="{{ $url_show }}" method="POST">
            <label>PUT</label>
            <input type="hidden" name="_method" value="PUT" />
            <input type="text" name="title" value="example" />
            <input type="submit" />
          </form>
        </li>
        <li>
          <strong>delete</strong> : {{ $url_delete }}<br />
          <form id="delete" action="{{ $url_delete }}" method="GET">
            <label>GET</label>
            <input type="submit" />
          </form>
        </li>
        <li>
          <strong>destroy</strong> : {{ $url_show }}<br />
          <form id="destroy" action="{{ $url_show }}" method="POST">
            <label>DELETE</label>
            <input type="hidden" name="_method" value="DELETE" />
            <input type="submit" />
          </form>
        </li>
        <li>
          <strong>validate</strong> : {{ $url_validate }}<br />
          <form id="validate" action="{{ $url_validate }}" method="POST">
            <label>POST</label>
            <input type="text" name="title" value="example" />
            <input type="submit" />
          </form>
        </li>
      </ul>
  </body>
</html>
