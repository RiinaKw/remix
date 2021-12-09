<body>
  <h1>Input form</h1>
{{if ! $errors->isEmpty() }}
  <p style="color: red;">Some errors have occurred</p>
{{endif}}
  <b style="color: red;">{{ $csrf->error() }}</b>
  <form action="confirm" method="post">
    <label>
      name :
      <input type="text" name="name" value="{{ $form->get('name') }}" />
      <small>{{ $errors->get('name') }}</small>
    </label>
    <br />
    <label>
      email :
      <input type="text" name="email" value="{{ $form->get('email') }}" />
      <small>{{ $errors->get('email') }}</small>
    </label>
    <br />
    <label>
      profile :
      <textarea name="profile">{{ $form->get('profile') }}</textarea>
      <small>{{ $errors->get('profile') }}</small>
    </label>
    <br />
    {{! $csrf->html() !}}

    <button type="submit">confirm</button>
  </form>
</body>
