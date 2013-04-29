<!DOCTYPE html>
<html lang='en'>
	<head>
		<title>Template Chooser</title>
	</head>
	<body>
		<h2>Click on a template to edit it</h2>
		<?php 
			$dir = getcwd() . "/templates";

			// Open a known directory, and proceed to read its contents
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if (strstr($file, "Template")) {
							echo "<a href='templateEditor.php?file=$file'>$file</a>";
						}
					}
					closedir($dh);
				}
			}
		?>
	</body>
</html>
