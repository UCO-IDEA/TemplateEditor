<?php
	header('Content-Type: text/html; charset=utf-8');

	function extractContent($start, $end, $content) {
		$begin = strpos($content, $start);
		$stop = strpos($content, $end, $begin);
		return substr($content, $begin + strlen($start), $stop-$begin - strlen($start));
	}
	
	$fontOptions = "
		<option class='fontOption' value='Arial, Helvetica, sans-serif'>Arial</option>
		<option class='fontOption' value='Georgia, serif'>Georgia</option>
		<option class='fontOption' value='\"Helvetica Neue\", Helvetica, Arial, sans-serif'>Helvetica</option>
		<option class='fontOption' value='Tahoma, Geneva, sans-serif'>Tahoma</option>
		<option class='fontOption' value='Verdana, Verdana, Geneva, sans-serif'>Verdana</option>
	";
	
	$borderStyleOptions = "
		<option class='borderStyleOptions' value='none' selected='selected'>none</option>
		<option class='borderStyleOptions' value='dotted'>dotted</option>
		<option class='borderStyleOptions' value='dashed'>dashed</option>
		<option class='borderStyleOptions' value='solid'>solid</option>
	";

	$fontSizeOptions = "
		<option class='fontSizeOption' value='08'>08</option>
		<option class='fontSizeOption' value='10'>10</option>
		<option class='fontSizeOption' value='12'>12</option>
		<option class='fontSizeOption' value='14'>14</option>
		<option class='fontSizeOption' value='16'>16</option>
		<option class='fontSizeOption' value='18'>18</option>
		<option class='fontSizeOption' value='20'>20</option>
		<option class='fontSizeOption' value='22'>22</option>
		<option class='fontSizeOption' value='24'>24</option>
	";
	
	$imageOptions = "
		<option class='imageOption' value='none'>None</option>
		<option class='imageOption' value='clean_textile.png'>Clean Textile</option>
		<option class='imageOption' value='dark_wood.png'>Dark Wood</option>
		<option class='imageOption' value='egg_shell.png'>Egg Shell</option>
		<option class='imageOption' value='mochaGrunge.png'>Mocha Grunge</option>
		<option class='imageOption' value='white_leather.png'>White Leather</option>
		<option class='imageOption' value='Subtile_Diamonds.png'>Subtile Diamonds</option>
		<option class='imageOption' value='diamond_upholstery.png'>Big Diamonds</option>
		<option class='imageOption' value='white_wall.png'>White Wall</option>
		<option class='imageOption' value='wall4.png'>Concrete Wall</option>
		<option class='imageOption' value='redox_02.png'>Redox</option>
		<option class='imageOption' value='tiny_grid.png'>grid</option>
		<option class='imageOption' value='gplaypattern.png'>GPlay</option>
		<option class='imageOption' value='white_tiles.png'>White Tiles</option>
		<option class='imageOption' value='furley_bg.png'>Light Scratches</option>
	";
	
	if (isset($_GET['template'])) {
		$_FILES['file']['error'] = -1;
		$_FILES['file']['name'] = $_GET['template'];
		$_FILES['file']['tmp_name'] = getcwd() . "/templates/" . $_GET['template'];
	}
	
	$file = "OneColumnTemplate.html";
	if (isset($_GET['file']) && file_exists(getcwd() . "/templates/" . $_GET['file'])) {
		$file = $_GET['file'];
	} else if (isset($_FILES)) {
		if ($_FILES['file']['error'] > 0) {
			echo "file error " . __LINE__;
		} else {
			$info = pathinfo($_FILES['file']['name']);
			if (strcmp($info['extension'], 'template') == 0) {
				$handle = fopen($_FILES['file']['tmp_name'], 'r') or die("File Error " . __LINE__);
				$content = fread($handle, filesize($_FILES['file']['tmp_name']));
				fclose($handle);
				
				$values = unserialize($content);
				
				$file = $values['file'];
			} else {
				echo "Invalid file";
			}
		}
	}
	
	$content = file_get_contents(getcwd() . "/templates/" . $file);
	
	$headContent = extractContent("<head>", "</head>", $content);
	
	$cssContent = extractContent("<style id='contentCSS' type=\"text/css\">", "</style>", $headContent);
	
	$bodyContent = extractContent("<body>", "</body>", $content);
	
	function getDescription($startPos) {
		GLOBAL $cssContent;
		$start = strpos($cssContent,"/*", $startPos + 1);
		$end = strpos($cssContent, "*/", $start);
		return substr($cssContent, $start+2, $end-$start-2);
	}
?>
<html charset='utf-8'>
	<head>
		<title>Template Editor</title>
		
		<script type='text/javascript'>
			<?php 
				if (isset($values)) {
					echo "var values =  ".json_encode($values) .";";
					echo "var hasValues = true;";
				} else {
					echo "var hasValues = false;";
				}
			?>
		</script>
		
		
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
		<script type="text/javascript" src="js/jquery.colorpicker.js"></script>
		<script type="text/javascript" src='js/templateEditorIframe.js'></script>
		
		<link href="css/jquery.colorpicker.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" media="screen" type="text/css" href="css/jquery-ui-1.8.22.custom.css" />
		<link rel="stylesheet" media="screen" type="text/css" href="css/view.css" />
		<link rel="stylesheet" media="screen" type="text/css" href="css/iFrameLayout.css" />
	</head>
	<body>
		<div id='popup'>
			<div id='popupContainer'>
				<a href='#' id='popupClose'>&times;</a>
				<textarea id='content'></textarea>
				<input type='hidden' id='toUpdate' value="" />
				<button id='popupSubmit'>Submit Changes</button>
				<span id='navigationOptions'>
					Add a link - Link To:
					<select id='linkSelect'>
						
					</select>
					Link Text: <input type='text' id='linkText' />
					<button id='addLink'>Add Link</button>
				</span>
			</div>
		</div>
		<div id='newFilePopup'>
			<a href='#' id='filePopupClose'>&times;</a>
			<h2>Create a new File</h2>
			File Name:<input type='text' id='newFileName' /><br />
			Page Title:<input type='text' id='newPageTitle' /><br />
			<button type='button' id='newFileSubmit'>Submit</button>
		</div>
		<form method='post' action='downloadTemplate.php'>
			<div class='controls' id='tabs'>
				<ul>
					<li><a href='#FontsTab'>Fonts</a></li>
					<li><a href='#ColorsTab'>Colors</a></li>
					<li><a href='#LooksTab'>Styling</a></li>
				</ul>
				<div id='FontsTab'>
					<span class='fontSelectors'>
						<table class='editorTable'>
							<tr><th colspan='50'>Fonts</th></tr>
							<tr>
							<?php
								$fontCount = 1;
								$fonts = array();
								while (strpos($cssContent, "/*Font$fontCount") !== false) {
									$pos = strpos($cssContent, "/*Font$fontCount");
									$for = getDescription($pos);
									
									echo "<td>$for</td>";
									
									$fontCount++;
								}
								echo "</tr><tr>";
								
								for ($i = 1; $i < $fontCount; $i++) {
									echo "<td><select class='fontChange' id='Font$i' name='Font$i'>$fontOptions</select></td>";
								}
								echo "</tr></table>";
							?>
					</span>
					<span class='fontSizeSelectors'>
					<table class='editorTable'>
					<tr><th colspan='50'>Font Sizes</th></tr>
					<tr>
						<?php
							$fontCount = 1;
							$fonts = array();
							while (strpos($cssContent, "/*FontSize$fontCount") !== false) {
								$pos = strpos($cssContent, "/*FontSize$fontCount");
								$start = strrpos($cssContent, ":", -(strlen($cssContent) - $pos)) + 1;
								$currentVal = substr($cssContent, $start, $pos - $start - 3);
								$for = getDescription($pos);
								
								echo "<td>$for</td>";
								$fonts[] = $currentVal;
								$fontCount++;
							}
							echo "</tr><tr>";
							
							for ($fontCount = 1; $fontCount <= count($fonts); $fontCount++) {
								echo "<td><input type='text' class='fontSizeChange' id='FontSize$fontCount' name='FontSize$fontCount' value='".$fonts[$fontCount-1]."' readonly='readonly'/><span class='FontSizeChange sliders' id='$fontCount'></span></td>";
							}
							echo "</tr></table>";
						?>
					</span>
					<span class='headerOptions'>
						<table class='editorTable'>
							<tr><th colspan='50'>Header Options</th></tr>
							<tr>
								<td>
									<select id='headerSize' name='headerSize'>
										<option value='Largest'>Largest</option>
										<option value='Larger'>Larger</option>
										<option value='Large'>Large</option>
										<option value='Default' selected='selected'>Default</option>
										<option value='Small'>Small</option>
									</select>
								</td>
							</tr>
						</table>
					</span>
				</div>
				<div id='ColorsTab'>
					<span class='OpacitySelectors'>
					<table class='editorTable'>
						<tr><th colspan='50'>Opacity</th></tr>
						<tr>
						<?php
							$opacityCount = 1;
							$opacities = array();
							
							while (strpos($cssContent, "/*Opacity$opacityCount") !== false) {
								$pos = strpos($cssContent, "/*Opacity$opacityCount");
								$start = strrpos($cssContent, ": ", -(strlen($cssContent) - $pos)) + 2;
								$end = strpos($cssContent, "/*", $start);
								$currentVal = substr($cssContent, $start, $end - $start-1);
								$for = getDescription($pos);
								
								echo "<td>$for</td>";
								$opacities[] = $currentVal;
								
								$opacityCount++;
							}
							
							echo "</tr><tr>";
							
							for($i = 0; $i < count($opacities); $i++) {
								echo "<td><input type='text' value='" .$opacities[$i] ."' id='InputOpacity" . ($i+1) ."' name='Opacity" . ($i+1) ."' readonly='readonly'/><span class='opacityChange sliders' id='Opacity" . ($i+1) ."'></span></td>";
							}
							
							echo "</tr></table>";
						?>
					</span>
					<span class='colorChoosers'>
						<table class='editorTable'>
						<tr><th colspan='50'>Colors</th></tr>
						<tr>
						<?php 
							$colorCount = 1;
							
							$colors = array();
							while (strpos($cssContent, "/*Color$colorCount") !== false) {
								$pos = strpos($cssContent, "/*Color$colorCount");
								
								$for = getDescription($pos);
								
								echo "<td>$for</td>";
								$colors[] = substr($cssContent, $pos-7, 6);
								
								$colorCount++;
							}
							echo "</tr><tr>";
							for ($i = 0; $i < count($colors); $i++) {
								echo "<td><input class='color' type='text' id='Color" . ($i+1) . "' name='Color" . ($i+1) ."' value='" . $colors[$i] . "' style='background-color:" . $colors[$i] . "' /></td>";
							}
							echo "</tr></table>";
						?>
					</span>
				</div>
				<div id='LooksTab'>
					<span class='ImageSelectors'>
						<table class='editorTable'>
						<tr><th colspan='50'>Images</th></tr>
						<tr>
						<?php
							$imageCount = 1;
							while (strpos($cssContent, "/*Image$imageCount") !== false) {
								$pos = strpos($cssContent, "/*Image$imageCount");
								$start = strrpos($cssContent, "Images/", -(strlen($cssContent) - $pos)) + strlen("Images/");
								$end = strpos($cssContent, "');", $start);
								$currentVal = substr($cssContent, $start, $end - $start);
								$for = getDescription($pos);
								
								
								echo "<td>$for</td>";
								
								$imageCount++;
							}
							echo "</tr><tr>";
							
							for ($i = 1; $i < $imageCount; $i++) {
								echo "<td><select class='imageChange' id='Image$i' name='Image$i'>$imageOptions</select></td>";
							}
							
							echo "</tr></table>";
						?>
					</span>
					
					
					<span class='BorderStyles'>
						<table class='editorTable'>
						<tr><th colspan='50'>Border/Underline Styles</th></tr>
						<tr>
						<?php
							$borderCount = 1;
							echo "<tr><th>Changes</th><th>Thickness</th><th>Border Style</th></tr>";
							while (strpos($cssContent, "/*BorderWidth$borderCount") !== false) {
								$pos = strpos($cssContent, "/*BorderWidth$borderCount");
								$start = strrpos($cssContent, ": ", -(strlen($cssContent) - $pos)) + 2;
								$end = strpos($cssContent, "/*", $start) - 2;
								$currentVal = substr($cssContent, $start, $end - $start-1);
								$for = getDescription($pos);
								
								echo "<tr><td>$for</td><td><input type='text' class='borderWidthChange' id='InputBorderWidth$borderCount' name='BorderWidth$borderCount' value='$currentVal' readonly='readonly'/><div style='width: 200px;' class='BorderWidthChange BorderSliders' id='BorderWidth$borderCount'></div></td><td>";
								
								if (strpos($cssContent, "/*BorderStyle$borderCount") !== false) {
									echo "<select class='borderStyleChange' id='BorderStyle$borderCount' name='BorderStyle$borderCount'>$borderStyleOptions</select>";
								}
								
								echo "</td></tr>";
								
								$borderCount++;
							}
							echo "</table>";
						?>
					</span>
				</div>
				<div>
					<input type='hidden' name='file' id='file' value='<?php echo $file;?>' />
					<button id='btnEditContent' type='button'>Edit Content</button>
					<button id='showStylePage' type='button' style='display: none'>Show Style Demo</button>
					<button id='submit' name='submit'>Download</button>
					<div id='contentPages'>
						
					</div>
				</div>
		</div>
		<iframe id='editorContent' style="padding: 5px; border: 1px solid black;" src='http://busn.uco.edu/cpde/TemplateEditor/templates/<?php echo $file; ?>' ></iframe>
		</form>
		
		<div id='loading'>Loading...</div>
	</body>
</html>