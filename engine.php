<?php

error_reporting(E_ALL);

ini_set('log_errors', 'On');
ini_set('error_log', 'error.log');

header('Content-Type: text/html; charset=utf-8');

class Engine {	private $img_format = array('jpg', 'gif', 'png');
	private $watermark = 'watermark.png';

	function __construct() {
		$this->sql = new mysqli('localhost', 'root', '', 'testovoe');
	}

    //Возвращает тип файла
	private function typeFile($image) {		$name = basename($image);
		$name = explode('.', $name);
		return end($name);
	}

    //Проверка является ли файл изображением и был ли ранее загружен
	private function checkImage($image) {
		if (!empty($image)) {			//Проверка формата файла картинки
			if (in_array($this->typeFile($image), $this->img_format)) {				//Формат соответсвует разрешенному, проверим была ли ранее загружена такая ссылка
				$query = $this->sql->query("SELECT `id` FROM `images` WHERE `url`='".$this->sql->escape_string($image)."'")->fetch_assoc();
        		if (empty($query['id'])) return true; else return false;
			} else {				return false;
			}
		} else {			return false;
		}
	}

    //Ресайз и наложение водяного знака
	private function image_resize($source_path, $destination_path, $newwidth, $newheight = false, $quality = false) {

    	ini_set("gd.jpeg_ignore_warning", 1);

    	list($oldwidth, $oldheight, $type) = getimagesize($source_path);

    	switch ($type) {
        	case IMAGETYPE_JPEG: $typestr = 'jpeg'; break;
        	case IMAGETYPE_GIF: $typestr = 'gif' ;break;
        	case IMAGETYPE_PNG: $typestr = 'png'; break;
    	}
    	$function = "imagecreatefrom$typestr";
    	$src_resource = $function($source_path);

    	if (!$newheight) { $newheight = round($newwidth * $oldheight/$oldwidth); }
    	elseif (!$newwidth) { $newwidth = round($newheight * $oldwidth/$oldheight); }
    	$destination_resource = imagecreatetruecolor($newwidth,$newheight);

        //Водяной знак
    	$watermark = imagecreatefrompng($this->watermark);
    	list($mark_width, $mark_height) = getimagesize($this->watermark);
    	imagecopyresampled($src_resource, $watermark, 0, 0, 0, 0, $mark_width, $mark_height, $mark_width, $mark_height);

    	imagecopyresampled($destination_resource, $src_resource, 0, 0, 0, 0, $newwidth, $newheight, $oldwidth, $oldheight);

    	if ($type = 2) {
        	imageinterlace($destination_resource, 1); // чересстрочное формирование изображение
        	imagejpeg($destination_resource, $destination_path, $quality);
    	} else {
	        $function = "image$typestr";
        	$function($destination_resource, $destination_path);
    	}
    	imagedestroy($destination_resource);
    	imagedestroy($src_resource);
	}

    //Обработка формы загрузки
	public function upload() {

		if ($_FILES['images']['tmp_name']) {
			$images = file_get_contents($_FILES['images']['tmp_name']);
			$images = explode("\r\n", $images);

			foreach ($images as $key => $val) {
				if ($this->checkImage($val)) {                	//Если файл прошел проверку, запишем ссылку в БД и получим ИД инкримента для присвоения имени
                	$this->sql->query("INSERT INTO `images` (`url`) VALUES ('".$this->sql->escape_string($val)."')");
                    $fileName = $this->sql->insert_id . '.' . $this->typeFile($val);
                    //Скачиваем картинку, сохраняем и ресайзим
                    $content = file_get_contents($val);
                    $file = 'upload/' . $fileName;
                    file_put_contents($file, $content);
					$this->image_resize($file, $file, '', '200', '100');
				}
			}
		}
	}

	public function images() {

		$html = '';

		$result = $this->sql->query("SELECT * FROM `images`");
		while ($data = $result->fetch_assoc()) {
        	$html .= '<div class="item"><img src="upload/' . $data['id']. '.' . $this->typeFile($data['url']) . '"></div>';
		}
        return $html;
	}

}


$Engine = new Engine();

switch ($_GET['mod']) {

	case 'upload':
		echo $Engine->upload();
	break;

	case 'images':
		echo $Engine->images();
	break;

	default:
    	echo'Ошибка 404, страница не найдена!';
	break;

}