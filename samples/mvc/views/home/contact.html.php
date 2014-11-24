<?php
  $title = "Contact";
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("views/common/head.html.php"); ?>
  </head>
  <body>
    <?php include("views/common/menu.html.php"); ?>
    <form action="" method="POST">
      <label>Name:</label><input type="text" name="name" /><br />
      <label>Message:</label><textarea name="message"></textarea><br />
      <input type="submit" />
      <p><?=$viewbag->resultMessage?></p>
    </form>
  </body>
</html>