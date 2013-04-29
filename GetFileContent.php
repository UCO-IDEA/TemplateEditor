<?php
function extractContent($start, $end, $content) {
	$begin = strpos($content, $start);
	$stop = strpos($content, $end, $begin);
	return substr($content, $begin, $stop-$begin);
}

if (isset($_POST['file'])) {
	$file = str_replace("Template", "Content", $_POST['file']);
	
	$content = file_get_contents(getcwd() . "/templates/" . $file);
	
	echo extractContent("<body>", "</body>", $content);
}
?>