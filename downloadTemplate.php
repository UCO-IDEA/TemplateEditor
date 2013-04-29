<?php
header('Content-Type: text/html; charset=utf-8');

function extractContent($start, $end, $content) {
	$begin = strpos($content, $start);
	$stop = strpos($content, $end, $begin);
	return substr($content, $begin + strlen($start), $stop-$begin - strlen($start));
}

function replaceContent($startText, $endText, $newText, &$content) {
	$start = strpos($startText, $content);
	if ($start === false) {
		echo "Start not found.<br/>";
	}
	$end = strpos($endText, $content, $start);
	$substring = substr($content, $start, $end-$start);
	//echo "StartText: ".htmlentities($startText)." <br/>Start: $start <br/>End: $end <br/>Replacing: $substring<br/><br/>";
	$content = substr_replace($content, $newText, $start, $end - $start);
}

/* creates a compressed zip file */
function createZip($files = array(), $bases = array(), $baseFile = "",$destination = '',$overwrite = false) {
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
			if (array_key_exists($file, $bases)) {
				$zip->addFile($file, $bases[$file]);
			} else if (($pos = strpos($file, $baseFile)) !== false) {
				$base = substr($file, $pos + strlen($baseFile));
				$zip->addFile($file, $base);
			} else {
				$zip->addFile($file);
			}
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	} else {
		echo "Bad Files";
		return false;
	}
}
$bases = array();
$toArchive = array();

$templateFile = $_POST['file'];

$values = serialize($_POST);

$base = substr($_POST['file'], 0, strpos($_POST['file'], '\\'));

$jsonFile = "downloads\\$base.template";
$bases[$jsonFile] = "$base.template";

$handler = fopen($jsonFile, 'w');
fwrite($handler, $values);
fclose($handler);

$toArchive[] = $jsonFile;


$htmlFile = str_replace("Template", "", $templateFile);
$contentFileName = str_replace("Template", "Content", $templateFile);
$cssFile = "templates\\$base\\css\\$base.css";//str_replace(".html", ".css", $htmlFile);
/*
 * To Do: If there are folders with the template name, add them to the zip as well
 */
$dir = "";
if (false !== ($dirend = strpos($_POST['file'], '\\'))) {
	$dir = 'templates\\' . substr($_POST['file'], 0, $dirend);
	
	if ($handle = opendir($dir)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				if (is_dir($dir."\\".$entry)) {
					if ($newHandle = opendir($dir."\\".$entry)) {
						while (false !== ($inFolder = readdir($newHandle))) {
							if ($inFolder != "." && $inFolder != ".." && $inFolder != $base.".css") {
								$toArchive[] = $entry.'\\'.$inFolder;
							}
						}
						closedir($newHandle);
					}
				}
			}
		}
		closedir($handle);
	}
} else  {
	echo $_POST['file'] . ": no dir found";
}

//echo "<pre>".print_r($_POST, true)."</pre>";

$cssFileName = "downloads\\$base.css";
mkdir(dirname($cssFileName), 0777, true);
copy($cssFile, $cssFileName) or die ("File Error " . __LINE__);

$fp = fopen($cssFileName, 'w') or die("File error");
$content = file_get_contents($cssFile) or die ("File Error " . __LINE__); // get the contents of the css file
$contentFile = file_get_contents("templates\\" . $contentFileName) or die ("\\templates\\" . $contentFileName . " File Error " . __LINE__); // get the contents of the content template file
//echo "<pre>";

$imagesToArchive = array();
$filesToArchive = array();

$keys = array_keys($_POST);
foreach ($keys as $key) {
	if (preg_match("/Page[0-9]+/", $key, $matches) > 0) {
		$Page = $matches[0];
		
		$filesToArchive[$Page][str_replace($Page, "", $key)] = $_POST[$key]; //divide up each file into a two dimensional array in the form $filesToArchive[filename][idOfElement]
	} else {//find and replace css customizations
		if (strpos($key, "FontSize") !== false) {
			/* if (preg_match("/[A-Za-z0-9\- \,]+;\/\*$key/", $content, $matches)) {
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
			
			$content = preg_replace("/[A-Za-z0-9\- ]+;\/\*$key/", "url(\"../img/".$_POST[$key] . "\"); /*" . $key, $content);
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
}
//echo $content;
fwrite($fp, $content);
fclose($fp);
//echo "</pre>";

$bases[$cssFileName] = "css\\$base.css";

$toArchive[] = $cssFileName;
$toArchive[] = "" . getcwd() . "\\templates\\" . $htmlFile;


//echo "<pre>".print_r($filesToArchive, true)."</pre>";

$keys = array_keys($filesToArchive);
foreach ($keys as $key) {//for each file
	$newContent = $contentFile;
	
	$innerKeys = array_keys($filesToArchive[$key]);
	foreach($innerKeys as $innerKey) {//for each value
		if ($innerKey != "FileName" && $innerKey != "PageTitle") { //FileName and PageTitle are taken care of else where
			$newContent = preg_replace("/id\=\'$innerKey\'>.*<\//", "id='$innerKey'>".$filesToArchive[$key][$innerKey]."</",$newContent);//replace default content with new content
		} else if ($innerKey == "PageTitle") {
			$newContent = preg_replace("/<title>.*<\/title>/", "<title>".$filesToArchive[$key][$innerKey]."</title>",$newContent);//replace default title with new title
		}
	}
	$tempFileName = "downloads\\" . $filesToArchive[$key]["FileName"] . ".html";
	$fp = fopen($tempFileName, 'w');
	
	fwrite($fp, $newContent); //write the entire content to the new filename
	fclose($fp);
	$bases[$tempFileName] = $filesToArchive[$key]["FileName"] . ".html"; 
	$toArchive[] = $tempFileName; //add to archive list
}

/* echo "<br/>New Page:<br/>";
echo  $newContent;
echo "</pre>"; */

foreach ($imagesToArchive as $image) {
	$bases["images\\" . $image] = "img\\" . $image;
	$toArchive[] = "images\\" . $image;//add each image to archive list
}

if (createZip($toArchive, $bases, "templates\\$base\\", "downloads\\template.zip", true)) {
	header("Content-type: application/zip");
	header("Content-Disposition: attachment; filename=template.zip");
	header("Pragma: no-cache");
	header("Expires: 0");
	readfile("downloads\\template.zip");
	exit();
}

?>

<html>
<body>
Something went wrong
</body>
</html>