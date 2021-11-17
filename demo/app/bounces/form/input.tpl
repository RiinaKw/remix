<body>
  <h1>Input form</h1>
{{if ! $errors->isEmpty() }}
  <p style="color: red;">Some errors have occurred</p>
  <b style="color: red;">{{ $errors->get('csrf') }}</b>
{{endif}}
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
    <input type="text" name="csrf_token" value="{{ $csrf_token }}" />
    <button type="submit">confirm</button>
  </form>
</body>
