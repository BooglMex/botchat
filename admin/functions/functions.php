<?php
// TEMPLATES
$dateYYYYMMDD = "/^\d{1,4}[-]\d{1,2}[-]\d{1,2}$/u";
$digits = "/^\d{1,16}(,\d{1,16})*$/u"; // Целые числа через запятую
$shabl_login = "/^([a-zA-Z][0-9a-zA-Z]{4,15})$/u"; // Шаблон логина пользователя
$shabl_password = "/^([0-9a-zA-Z]{8,16})$/u"; // Шаблон пароля пользователя

// ARRAYS
$global_arr_status = array('client', 'VIP');
$arr_number_name_days = array("1"=>"Понедельник", "2"=>"Вторник", "3"=>"Среда", "4"=>"Четверг", "5"=>"Пятница", "6"=>"Суббота", "7"=>"Воскресенье");
$arr_number_name_days_mini = array("1"=>"Пн", "2"=>"Вт", "3"=>"Ср", "4"=>"Чт", "5"=>"Пт", "6"=>"Сб", "7"=>"Вс");

$MB = 1024 * 1024;

// FUNCTIONS
function clear_string($cl_str, $clear_probels=0)
{
	$cl_str = trim($cl_str); /*очистка пробелов в начале и в конце*/
	if($GLOBALS["link"]) $cl_str = mysql_real_escape_string($cl_str); /*экранирует специальные символы в строке, используемой в SQL-запросе*/
	//$cl_str = strip_tags($cl_str); /*очистка HTML и PHP тегов*/
	if($clear_probels) return clear_probels($cl_str);
	else return $cl_str;
}

// Delete multi-probels
function clear_probels($str)
{
	// Удаление «не удаляемых» пробелов.
	//$str = str_replace('&amp;', '&', $str);
	$str = str_replace('&nbsp;', ' ', $str);
	
	// Удаление лишних пробелов
	$str = preg_replace('/\s+/', ' ', $str);
	
	// Удаление лишних пробелов, которые не удаляются стандартными методами (вероятнее всего это NO-BREAK SPACE)
	$str = preg_replace('/\s++/u', ' ', $str);
	
	return $str;
}

// Delete all probels
function clear_all_probels($str){ return preg_replace('/\s/u', '', $str); }

function htmlchars($str) { return htmlspecialchars($str); } // Преобразует символы в коды HTML

function clear_phone($str) { return str_replace(array(" ", "-", "(", ")"), "", $str); } // Удаляем лишние символы в номере

// Функция 'iconv("UTF-8", "cp1251", $str)' нужна для конвертации строки $str из cp1251 в UTF-8.
function my_conv($str) { return iconv("UTF-8", "cp1251", $str); }
function check_checked($str) { if($str == 1) return "checked"; }

/* function connectDB() { return new mysqli("localhost","mywayd2b_mwc","mebel_admin","mywayd2b_mwc"); }
function closeDB($mysqli) { $mysqli->close(); } */

// Генерация пароля
function fungenpass($numb=8)
{
    $number = $numb;
	
    $arr = array('a','b','c','d','e','f',
                 'g','h','i','j','k','l',
                 'm','n','o','p','r','s',
                 't','u','v','x','y','z',
                 '1','2','3','4','5','6',
                 '7','8','9','0');
	
    // Генерируем пароль
    $pass = "";
	
    for($i = 0; $i < $number; $i++)
    {
		// Вычисляем случайный индекс массива
		$index = rand(0, count($arr) - 1);
		$pass .= $arr[$index];
    }
	return $pass;
}

// Группировка цен по разрядам.
function group_numerals($int)
{
	$drob = $int - intval($int);
	if($drob == 0) $num_end = '';
	else $num_end = substr(round($drob, 2), 1);
	
	$int = intval($int);
	
	switch(strlen($int))
	{
		case '4':	$price = substr($int,0,1).' '.substr($int,1,4).$num_end;						break;
	    case '5':	$price = substr($int,0,2).' '.substr($int,2,5).$num_end;						break;
	    case '6':	$price = substr($int,0,3).' '.substr($int,3,6).$num_end;						break;
	    case '7':	$price = substr($int,0,1).' '.substr($int,1,3).' '.substr($int,4,7).$num_end;	break;
	    default:	$price = $int.$num_end;															break;
	}
    return $price; 
}

// Отправка почты
function send_mail($from,$to,$subject,$body,$sender='SITE') // От кого?, кому?, тема сообщения, само сообщение, подпись.
{
	$charset = 'UTF-8'; // Кодировка
	mb_language("ru"); // Язык сообщения
	/*------------- Стандартный шаблон --------------*/
	// Для отправки HTML-письма должен быть установлен заголовок Content-type
	$headers  = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=$charset" . "\r\n";
	
	$headers .= "From: ".$sender." <$from>" . "\r\n";
	$headers .= "Reply-To: $from" . "\r\n";
	$headers .= "Envelope-from: $from" . "\r\n";
	/*---------------------------------------------- */
	
	// Формируем тему сообщения
	$subject = '=?'.$charset.'?B?'.base64_encode($subject).'?=';
	
	if(!strlen($from)) return false;
	elseif(filter_var($from, FILTER_VALIDATE_EMAIL) === false) return false;
	
	if(!strlen($to)) return false;
	elseif(filter_var($to, FILTER_VALIDATE_EMAIL) === false) return false;
	
	// Отправка сообщения
	return mail($to,$subject,$body,$headers,'-f'.$from);
}

// Проверка загруженной на хост картинки на безопасность и соответствие требованиям
function imgSecurity($image, $RazmeR)
{
	list($w_i, $h_i) = getimagesize($image['tmp_name']);
	if(!$w_i || !$h_i) return false;
	
	$name = $image["name"];
	$type = $image["type"];
	$size = $image["size"]; // Размер в байтах
	$blacklist = array(".php", ".phtml", ".php3", ".php4"); // Чёрный список опасных расширений
	
	foreach ($blacklist as $item)
	{
		if(preg_match("/$item\$/i", $name)) return false; // Сканирование полного названия картинки на наличие в нём опасных расширений
	}
	
	if(($type != "image/png") && ($type != "image/jpg") && ($type != "image/jpeg")) return false;
	
	if($size > $RazmeR * 1024) return false; // Ограничение до $RazmeR Кб
	
	return true;
}

//reQuality
function reQuality($uploadfile, $impQuality)
{
	$imgext = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $uploadfile));
	if( ($imgext == 'jpeg' || $imgext == 'jpg') && $impQuality > 0)
	{
		$descriptorImage = imagecreatefromjpeg($uploadfile); // Создаём дескриптор для работы с исходным изображением
		imagejpeg($descriptorImage, $uploadfile, $impQuality); // Пересоздаём jpg-файл чтобы установить его качество на заданное
		imagedestroy($descriptorImage);
	}
}

// Функция для ресайза изображений. $w_o и h_o - ширина и высота выходного изображения
function resize($image, $saveIn, $w_o=false, $h_o=false, $quality=90)
{
	if($w_o < 0 || $h_o < 0) return "Некорректные входные параметры";
	
	list($w_i, $h_i, $type) = getimagesize($image); // Получаем размеры и тип изображения (число)
	$types = array("", "gif", "jpeg", "png"); // Массив с типами изображений
	$ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа
	if($ext)
	{
		$func = 'imagecreatefrom'.$ext; // Получаем название функции, соответствующую типу, для создания изображения
		$img_i = $func($image); // Создаём дескриптор для работы с исходным изображением
	}
	else return "Некорректное изображение"; // Выводим ошибку, если формат изображения недопустимый
	
	/* Если указать только 1 параметр, то второй подстроится пропорционально */
	if($w_o && $w_o < $w_i) $h_o = $w_o * ($h_i / $w_i);
	elseif($h_o && $h_o < $h_i) $w_o = $h_o * ($w_i / $h_i);
	if(!$w_o || !$h_o)
	{
		$w_o = $w_i;
		$h_o = $h_i;
	}
	
	$img_o = imagecreatetruecolor($w_o, $h_o); // Создаём дескриптор для выходного изображения
	
	switch($ext) // Удаляем чёрный фон в png и gif
	{
		case 'gif':
			imagecopyresampled($img_o, $img_i, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i); // Переносим изображение из исходного в выходное, масштабируя его
			$background = imagecolorallocate($img_o , 0, 0, 0);
			imagecolortransparent($img_o, $background);
			break;
		case 'png':
			imagealphablending($img_o, false);
			imagesavealpha($img_o, true);
			imagecopyresampled($img_o, $img_i, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i); // Переносим изображение из исходного в выходное, масштабируя его
			break;
		default:
			imagecopyresampled($img_o, $img_i, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i); // Переносим изображение из исходного в выходное, масштабируя его
			break;
	}
	
	$func = 'image'.$ext; // Получаем функция для сохранения результата
	if($ext == 'jpeg' || $ext == 'jpg') return $func($img_o, $saveIn, $quality);
	else return $func($img_o, $saveIn);
}

// Загрузка картинки
function MyFuncUploadImage($FOTKA, $imgSize, $uploaddir, $query_str1, $query_str2, $imgPrefiks, $width_out=1920, $height_out=1080,  $impQuality=90, $ratio=false)
{
	$error_img = array();
	
	$maxfilesize = $imgSize; // Максимальный размер загружаемого файла
	
	if($FOTKA['error'] > 0)
	{
		switch($FOTKA['error']) //в зависимости от номера ошибки выводим соответствующее сообщение
		{
			case 1: $error_img[] = 'Размер файла превышает допустимое значение UPLOAD_MAX_FILE_SIZE = '.$maxfilesize.' Б.'; break;
			case 2: $error_img[] = 'Размер файла превышает допустимое значение MAX_FILE_SIZE = '.$maxfilesize.' Б.'; break;
			case 3: $error_img[] = 'Не удалось загрузить часть файла.'; break;
			case 4: $error_img[] = 'Файл не был загружен.'; break;
			case 6: $error_img[] = 'Отсутствует временная папка.'; break;
			case 7: $error_img[] = 'Не удалось записать файл на диск.'; break;
			case 8: $error_img[] = 'PHP-расширение остановило загрузку файла.'; break;
		}
	}
	else
	{
		if($FOTKA['type'] == 'image/jpeg' || $FOTKA['type'] == 'image/jpg' || $FOTKA['type'] == 'image/png' || $FOTKA['type'] == 'image/gif') //проверяем расширения
		{
			if($FOTKA['size'] <= $maxfilesize)
			{
				if($ratio)
				{
					list($w_i, $h_i) = getimagesize($FOTKA['tmp_name']);
					$arr_ratio = explode('x', $ratio);
					if( round(($w_i/$h_i), 2) != round(($arr_ratio[0]/$arr_ratio[1]), 2) )
					{
						$error_img[] = "Соотношения загружаемой картинки должны быть - $ratio";
						return implode('<br>',$error_img);
					}
				}
				
				$imgext = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $FOTKA['name']));
				$newfilename = $imgPrefiks.time().rand(100,999).'.'.$imgext; // Новое сгенерированное имя файла
				$uploadfile = $uploaddir.$newfilename; // Путь к файлу (папка.файл)
				if(move_uploaded_file($FOTKA['tmp_name'], $uploadfile)) // Загружаем файл move_uploaded_file(отсюда, сюда)
				{
					$queryAddImg = $query_str1.$newfilename.$query_str2;
					if(mysql_query($queryAddImg))
					{
						$answer = resize($uploadfile, $uploadfile, $width_out, $height_out, $impQuality);
						if(!($answer === true) && $answer != 1) $error_img[] = $answer;
						else $result = mysql_insert_id();
						
						if(!$result) $result = true;
					}
					else
					{
						@unlink($uploadfile);
						$error_img[] = 'Ошибка запроса в базу данных!';
					}
				}
				else $error_img[] = "Ошибка загрузки файла.";
			}
			else $error_img[] = 'Размер файла превышает допустимое значение '.($imgSize/1024).' КБ.';
		}
		else $error_img[] = 'Допустимые расширения: jpeg, jpg, png, gif';
	}
	
	if(count($error_img)) return implode('<br>',$error_img);
	else return $result;
}

function check_image_ratio($FOTKA, $ratio)
{
	if($ratio)
	{
		list($w_i, $h_i) = getimagesize($FOTKA['tmp_name']);
		$arr_ratio = explode('x', $ratio);
		if( round(($w_i/$h_i), 2) == round(($arr_ratio[0]/$arr_ratio[1]), 2) ) return true;
		else return false;
	}
}

function my_ip()
{
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = @$_SERVER['REMOTE_ADDR'];
	
	if(filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;
	elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
	else $ip = $remote;
	
	return $ip;
}

function get_gallery_image($product)
{
	$Q = mysql_query("SELECT gall_image FROM gallery WHERE gall_product=$product LIMIT 1");
	if(mysql_num_rows($Q))
	{
		$R = mysql_fetch_array($Q);
		return $R["gall_image"];
	}
	
	return false;
}

function get_cs_nav($cs_parent, $str_nav='')
{
	$q = mysql_query("SELECT cs_id, cs_name, cs_parent FROM catsalons WHERE cs_id=$cs_parent");
	if(mysql_num_rows($q) == 1)
	{
		$r = mysql_fetch_array($q);
		
		$str_nav = ' <li><a href="salons.php?id='.$r["cs_id"].'">'.$r["cs_name"].'</a></li> '.$str_nav;
		
		$str_nav = get_cs_nav($r["cs_parent"], $str_nav);
	}
	
	return $str_nav;
}

function get_nav($cat_section, $id, $str_nav='')
{
	$q = mysql_query("SELECT cat_id, cat_name, cat_section FROM categories WHERE cat_salon=$id AND cat_id=$cat_section");
	if(mysql_num_rows($q) == 1)
	{
		$r = mysql_fetch_array($q);
		
		$str_nav = ' <li><a href="category.php?id='.$id.'&cat='.$r["cat_id"].'">'.$r["cat_name"].'</a></li> '.$str_nav;
		
		$str_nav = get_nav($r["cat_section"], $id, $str_nav);
	}
	
	return $str_nav;
}

function get_cats($cat, $str_nav='')
{
	$str_nav .= ','.$cat;
	
	$q = mysql_query("SELECT cat_section FROM categories WHERE cat_id='$cat'");
	if(mysql_num_rows($q) == 1)
	{
		$r = mysql_fetch_array($q);
		
		$str_nav = get_cats($r["cat_section"], $str_nav);
	}
	
	return $str_nav;
}

function check_wishlist($product, $u_id=0)
{
	$product = (int) $product;
	$u_id = (int) $u_id;
	
	$ip = my_ip();
	
	if($u_id) $addQ = "wish_user='$u_id'";
	else $addQ = "wish_ip='$ip'";
	
	if($product > 0 && ($u_id > 0 || $ip))
	{
		$q = mysql_query("SELECT wish_product FROM wishlist WHERE $addQ AND wish_product=$product");
		if(mysql_num_rows($q)) return 1;
	}
	
	return 0;
}

function check_date($Ymd)
{
	$arr = explode('-', $Ymd);
	return checkdate($arr[1], $arr[2], $arr[0]); // int month, int day, int year
}

function get_classname($status=0)
{
	if($status > 0)
	{
		$Q = mysql_query("SELECT ucl_name FROM uclasses WHERE ucl_id=$status");
		if(mysql_num_rows($Q) == 1) return mysql_fetch_object($Q) -> ucl_name;
	}
	
	return 'client';
}

function get_countrytown($town=0, $salon=0)
{
	if($town > 0)
	{
		$Q = mysql_query("SELECT country_name, town_name, country_id, town_id FROM towns, countries WHERE town_country=country_id AND town_id=$town");
		if(mysql_num_rows($Q) == 1)
		{
			$R = mysql_fetch_array($Q);
			return array($R["country_name"], $R["town_name"], $R["country_id"], $R["town_id"]);
		}
	}
	elseif($salon > 0)
	{
		$Q = mysql_query("SELECT country_name, town_name, country_id, town_id FROM towns, countries, salons WHERE town_country=country_id AND town_id=sln_town AND sln_id=$salon");
		if(mysql_num_rows($Q) == 1)
		{
			$R = mysql_fetch_array($Q);
			return array($R["country_name"], $R["town_name"], $R["country_id"], $R["town_id"]);
		}
	}
	
	return array('', '', '', '');
}

function get_sale_product($status=0, $cat=0, $product=0)
{
	if($status > 0 && $cat > 0)
	{
		$Q = mysql_query("SELECT ucls_sale FROM uclass_sales WHERE ucls_class=$status AND ucls_cat_product=$cat");
		if(mysql_num_rows($Q) == 1) return (int) mysql_fetch_object($Q) -> ucls_sale;
	}
	elseif($status > 0 && $product > 0)
	{
		$Q = mysql_query("SELECT DISTINCT ucls_sale FROM uclass_sales, products WHERE ucls_cat_product=pr_category AND ucls_class=$status AND pr_id=$product");
		if(mysql_num_rows($Q) == 1) return (int) mysql_fetch_object($Q) -> ucls_sale;
	}
	
	return 0;
}

function get_sale_service($status=0, $cat=0, $service=0)
{
	if($status > 0 && $cat > 0)
	{
		$Q = mysql_query("SELECT ucls_sale FROM uclass_sales WHERE ucls_class=$status AND ucls_cat_service=$cat");
		if(mysql_num_rows($Q) == 1) return (int) mysql_fetch_object($Q) -> ucls_sale;
	}
	elseif($status > 0 && $service > 0)
	{
		$Q = mysql_query("SELECT DISTINCT ucls_sale FROM uclass_sales, salons WHERE ucls_cat_service=s_cat AND ucls_class=$status AND s_id=$service");
		if(mysql_num_rows($Q) == 1) return (int) mysql_fetch_object($Q) -> ucls_sale;
	}
	
	return 0;
}

function convert_Ymd_dmY($Ymd, $delimeter='-')
{
	if(preg_match($GLOBALS["dateYYYYMMDD"], $Ymd))
	{
		$arr = explode('-', $Ymd);
		return $arr[2].$delimeter.$arr[1].$delimeter.$arr[0];
	}
	
	return 0;
}

function get_month_russ($numb)
{
	if($numb == 1) return 'Январь';
	elseif($numb == 2) return 'Февраль';
	elseif($numb == 3) return 'Март';
	elseif($numb == 4) return 'Апрель';
	elseif($numb == 5) return 'Май';
	elseif($numb == 6) return 'Июнь';
	elseif($numb == 7) return 'Июль';
	elseif($numb == 8) return 'Август';
	elseif($numb == 9) return 'Сентябрь';
	elseif($numb == 10) return 'Октябрь';
	elseif($numb == 11) return 'Ноябрь';
	elseif($numb == 12) return 'Декабрь';
	else return '';
}

function get_week_russ($numb)
{
	if($numb == 1) return 'Понедельник';
	elseif($numb == 2) return 'Вторник';
	elseif($numb == 3) return 'Среда';
	elseif($numb == 4) return 'Четверг';
	elseif($numb == 5) return 'Пятница';
	elseif($numb == 6) return 'Суббота';
	elseif($numb == 7) return 'Воскресенье';
	else return '';
}

function get_currencyname($id=0, $salon=0)
{
	if($id > 0)
	{
		$Q = mysql_query("SELECT curr_name FROM currencies WHERE curr_id=$id");
		if(mysql_num_rows($Q) == 1) return mysql_fetch_object($Q) -> curr_name;
	}
	elseif($salon > 0)
	{
		$Q = mysql_query("SELECT curr_name FROM currencies, salons WHERE curr_id=sln_currency AND sln_id=$salon");
		if(mysql_num_rows($Q) == 1) return mysql_fetch_object($Q) -> curr_name;
	}
	
	return 'undefined';
}

function get_salonname($salon=0)
{
	if($salon > 0)
	{
		$Q = mysql_query("SELECT sln_name FROM salons WHERE sln_id=$salon");
		if(mysql_num_rows($Q) == 1) return mysql_fetch_object($Q) -> sln_name;
	}
	
	return 'Undefined';
}

function speccode_razdelitel($str)
{
	if(strlen($str))
	{
		$arr = str_split($str, 4);
		$str = implode(' ', $arr);
	}
	
	return $str;
}
?>