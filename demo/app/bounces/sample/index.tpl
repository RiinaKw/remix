<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
  </head>
  <body>
      <h1>{{ $title }}</h1>

      <ul>
          <li><a href="{{ $url_xml }}">
              Example of XML
          </a></li>
          <li><a href="{{ $url_json }}">
              Example of JSON
          </a></li>
          <li><a href="{{ $url_status }}">
              Example of status code
          </a></li>
          <li><a href="{{ $url_status_with_code }}">
              Example of status code with 418
          </a></li>
          <li><a href="{{ $url_exception }}">
              Example of Exception
          </a></li>
          <li><a href="{{ $url_exception_with_code }}">
              Example of Exception with status code 402
          </a></li>
      </ul>
  </body>
</html>
