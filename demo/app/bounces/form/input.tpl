<body>
  <h1>Input form</h1>
  <form action="confirm" method="post">
    <label>
      name :
      <input type="text" name="name" value="{{$name}}" />
    </label>
    <label>
      email :
      <input type="text" name="email" value="{{$email}}" />
    </label>
    <button type="submit">confirm</button>
  </form>
</body>
