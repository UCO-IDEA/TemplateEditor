<!DOCTYPE html>
<html lang='en'>
	<head>
		<title>Template Chooser</title>
		<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		
		<script src="js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class='container'>
			<div class='row'>
				<div class='span12'>
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
					<a href='templateEditorIframe.php?file=OneColumn\OneColumnTemplate.html'>OneColumnTemplate.html</a><br />
					<a href='templateEditorIframe.php?file=BootstrapResponsive\BootstrapResponsiveTemplate.html'>BootstrapResponsiveTemplate.html</a>
					<h2>Or upload json to edit a template</h2>
					<form action='templateEditorIframe.php' method='post' enctype="multipart/form-data">
						<label for="file">json file:</label>
						<input type="file" name="file" id="file"><br>
						<input type="submit" name="submit" value="Submit">
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
