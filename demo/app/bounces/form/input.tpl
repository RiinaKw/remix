<body>
  <h1>Input form</h1>
{{if ! $errors->isEmpty() }}
  <p style="color: red;">Some errors have occurred</p>
{{endif}}
  <b style="color: red;">{{ $csrf->error() }}</b>
  <form action="confirm" method="post">
    <label>
      name :
      <input type="text" name="name" value="{{ $name }}" />
      <small>{{ $errors->get('name') }}</small>
    </label>
    <br />
    <label>
      email :
      <input type="text" name="email" value="{{ $email }}" />
      <small>{{ $errors->get('email') }}</small>
    </label>
    <br />
    <input type="hidden" name="csrf_token" value="{{ $csrf->token() }}" />
    <button type="submit">confirm</button>
  </form>
</body>
