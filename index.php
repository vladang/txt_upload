<?php
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Тестовое задание</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="script.js"></script>
  </head>
  <body>

  <style>
  img {  	border:0px;
  }
  .item {    float:left;
    padding:2px;
  }
  .item img {
    height:200px;
  }
  </style>

	<div style="padding:50px; width:90%;">
		<form action="engine.php?mod=upload" method="post" id="upload_form" enctype="multipart/form-data">
  			.txt Файл:
  			<input type="file" name="images" id="images">
			<input type="submit" id="submit" value="Отправить">
		</form>
    	<div id="loader" style="display:none;"><img src="loading.gif"></div>
    </div>

	<div id="container"></div>

  </body>
</html>