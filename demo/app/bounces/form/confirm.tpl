<body>
  <h1>Input form</h1>
  <dl>
    <dt>name</dt>
    <dd>{{$name}}</dd>
    <dt>email</dt>
    <dd>{{$email}}</dd>
  </dl>
  <form action="input" method="post">
    <button type="submit">back</button>
  </form>
  <form action="submit" method="post">
  <input type="hidden" name="csrf_token" value="{{ $csrf->token() }}" />
    <button type="submit">submit</button>
  </form>
</body>
