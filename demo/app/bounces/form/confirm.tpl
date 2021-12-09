<body>
  <h1>Input form</h1>
  <dl>
    <dt>name</dt>
    <dd>{{ $form->get('name') }}</dd>
    <dt>email</dt>
    <dd>{{ $form->get('email') }}</dd>
    <dt>profile</dt>
    <dd>{{ $form->get('profile') }}</dd>
  </dl>
  <form action="input" method="post">
    <button type="submit">back</button>
  </form>
  <form action="submit" method="post">
    {{! $csrf->html() !}}

    <button type="submit">submit</button>
  </form>
</body>
