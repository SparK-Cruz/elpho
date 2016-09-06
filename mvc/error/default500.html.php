<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>500 Internal server error</title>
  </head>
  <body>
    <h1>HTTP/1.1 500: Internal server error</h1>
    <h3><?=$viewbag->type?></h3>
    <p><pre><?=$viewbag->message?></pre></p>
  </body>
</html>