<?php
/* creates a compressed zip file */
function createZip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		
		//add the files
		foreach($valid_files as $file) {
			$base = basename($file);
			$zip->addFile($file,$base);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		echo "Bad Files";
		return false;
	}
}

$templateFile = $_POST['file'];

$htmlFile = str_replace("Template", "", $templateFile);
$cssFile = str_replace(".html", ".css", $htmlFile);

$fp = fopen(getcwd() . "\\downloads\\" . $cssFile, 'w');
$content = file_get_contents(getcwd() . "\\templates\\" . $cssFile);
//echo "<pre>";

$imagesToArchive = array();

$keys = array_keys($_POST);
foreach ($keys as $key) {
	if (strpos($key, "FontSize") !== false) {
/* 		if (preg_match("/[A-Za-z0-9\- \,]+;\/\*$key/", $content, $matches)) {
			echo "Flag " . $key . " found.<br />";
			print_r($matches);
		} else {
			echo "Flag " . $key . " not found.<br />";
		} */
		$content = preg_replace("/[A-Za-z0-9\- \,]+;\/\*$key/", $_POST[$key] . "px; /*" . $key, $content);
	} else if (strpos($key, "Font") !== false) {
		/* if (preg_match("/[A-Za-z0-9\- \,]+;\/\*$key/", $content, $matches)) {
			echo "Flag " . $key . " found.<br />";
			print_r($matches);
		} else {
			echo "Flag " . $key . " not found.<br />";
		} */
		$content = preg_replace("/[A-Za-z0-9\- \,]+;\/\*$key/", $_POST[$key] . "; /*" . $key, $content);
	} else if (strpos($key, "Color") !== false) {
		/*if (preg_match("/[A-Fa-f0-9\-\# ]+;\/\*$key/", $content, $matches)) {
			echo "Flag " . $key . " found.<br />";
			print_r($matches);
		} else {
			echo "Flag " . $key . " not found.<br />";
		} */
		$content = preg_replace("/\#[A-Fa-f0-9\- ]+;\/\*$key/", "#" . $_POST[$key] . "; /*" . $key, $content);
	} else if (strpos($key, "Image") !== false && $_POST[$key] != "none") {
		/* if (preg_match("/[A-Za-z0-9\- ]+;\/\*$key/", $content, $matches)) {
			echo "Flag " . $key . " found.<br />";
			print_r($matches);
		} else {
			echo "Flag " . $key . " not found.<br />";
		} */
		$imagesToArchive[] = $_POST[$key];
		
		$content = preg_replace("/[A-Za-z0-9\- ]+;\/\*$key/", "url(\"".$_POST[$key] . "\"); /*" . $key, $content);
	} else if (strpos($key, "Opacity") !== false) {
		/* if (preg_match("/[\.0-9\ ]+;\/\*$key/", $content, $matches)) {
			echo "Flag " . $key . " found.<br />";
			print_r($matches);
		} else {
			echo "Flag " . $key . " not found.<br />";
		}  */
		$content = preg_replace("/[\.0-9\ ]+;\/\*$key/", $_POST[$key] . "; /*" . $key, $content);
		
		//filterOpacity
		
		/* if (preg_match("/[A-Za-z0-9\-\(\)\= ]+;\/\*".$key."Filter/", $content, $matches)) {
			echo "Flag " . $key . "Filter found.<br />";
			print_r($matches);
		} else {
			echo "Flag " . $key . "Filter not found.<br />";
		}  */
		$content = preg_replace("/[A-Za-z0-9\-\(\)\= ]+;\/\*".$key."Filter/", "Alpha(opacity=". ($_POST[$key] * 100) . "); /*" . $key . "Filter", $content);
	} else if (strpos($key, "BorderWidth") !== false) {
		/*if (preg_match("/[A-Fa-f0-9\-\# ]+;\/\*$key/", $content, $matches)) {
			echo "Flag " . $key . " found.<br />";
			print_r($matches);
		} else {
			echo "Flag " . $key . " not found.<br />";
		} */
		$content = preg_replace("/[0-9]+px;\/\*$key/", $_POST[$key] . "px; /*" . $key, $content);
	} else if (strpos($key, "BorderStyle") !== false) {
		/*if (preg_match("/[A-Fa-f0-9\-\# ]+;\/\*$key/", $content, $matches)) {
			echo "Flag " . $key . " found.<br />";
			print_r($matches);
		} else {
			echo "Flag " . $key . " not found.<br />";
		} */
		$content = preg_replace("/[0-9A-Za-z]+;\/\*$key/", $_POST[$key] . "; /*" . $key, $content);
	} 
}
//echo $content;
fwrite($fp, $content);
//echo "</pre>";
$toArchive = array();

$toArchive[] = "" . getcwd() . "\\downloads\\" . $cssFile;
$toArchive[] = "" . getcwd() . "\\templates\\" . $htmlFile;

foreach ($imagesToArchive as $image) {
	$toArchive[] = "" . getcwd() . "\\images\\" . $image;
}


if (createZip($toArchive,  "" . getcwd() . "\\downloads\\template.zip", true)) {
	header("Content-type: application/zip");
	header("Content-Disposition: attachment; filename=template.zip");
	header("Pragma: no-cache");
	header("Expires: 0");
	readfile("" . getcwd() . "\\downloads\\template.zip");
	exit();
}
?>

<html>
<body>
Something went wrong
</body>
</html>