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
		<option class='fontOption' value='\"Helvetica Neue\", Helvetica, Arial, sans-serif;'>Helvetica</option>
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
		<option class='imageOption' value='Subtile_Diamonds.png'>Subtile Diamonds</option>
		<option class='imageOption' value='diamond_upholstery.png'>Big Diamonds</option>
		<option class='imageOption' value='white_wall.png'>White Wall</option>
		<option class='imageOption' value='wall4.png'>Concrete Wall</option>
	";
	
	$imageOptions = "
		<option class='imageOption' value='none'>None</option>
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
					<div class='fontSelectors'>
					Fonts:
						<?php
							$fontCount = 1;
							
							while (strpos($cssContent, "/*Font$fontCount") !== false) {
								$pos = strpos($cssContent, "/*Font$fontCount");
								$for = getDescription($pos);
								
								echo "$for<select class='fontChange' id='Font$fontCount' name='Font$fontCount'>$fontOptions</select>";
								
								$fontCount++;
							}
						?>
					</div>
					<div class='fontSizeSelectors'>
					Font Sizes:
						<?php
							$fontCount = 1;
							
							while (strpos($cssContent, "/*FontSize$fontCount") !== false) {
								$pos = strpos($cssContent, "/*FontSize$fontCount");
								$start = strrpos($cssContent, ":", -(strlen($cssContent) - $pos)) + 1;
								$currentVal = substr($cssContent, $start, $pos - $start - 3);
								$for = getDescription($pos);
								
								echo "$for<input type='text' class='fontSizeChange' id='FontSize$fontCount' name='FontSize$fontCount' value='$currentVal' readonly='readonly'/><span class='FontSizeChange sliders' id='$fontCount'></span>";
								
								$fontCount++;
							}
						?>
					</div>
					<div class='headerOptions'>
						Header Options:
						<select id='headerSize' name='headerSize'>
							<option value='Largest'>Largest</option>
							<option value='Larger'>Larger</option>
							<option value='Large'>Large</option>
							<option value='Default' selected='selected'>Default</option>
							<option value='Small'>Small</option>
						</select>
					</div>
				</div>
				<div id='ColorsTab'>
					<div class='OpacitySelectors'>
					Opacity:
						<?php
							$opacityCount = 1;
							
							while (strpos($cssContent, "/*Opacity$opacityCount") !== false) {
								$pos = strpos($cssContent, "/*Opacity$opacityCount");
								$start = strrpos($cssContent, ": ", -(strlen($cssContent) - $pos)) + 2;
								$end = strpos($cssContent, "/*", $start);
								$currentVal = substr($cssContent, $start, $end - $start-1);
								$for = getDescription($pos);
								
								echo "<label for='InputOpacity$opacityCount'>$for<input type='text' value='" .$currentVal ."' id='InputOpacity$opacityCount' name='Opacity$opacityCount' readonly='readonly'/></label><span class='opacityChange sliders' id='Opacity$opacityCount'></span>";
								
								$opacityCount++;
							}
						?>
					</div>
					<div class='colorChoosers'>
					Colors:
						<?php 
							$colorCount = 1;
							
							$colors = array();
							echo "<table>";
							echo "<tr>";
							while (strpos($cssContent, "/*Color$colorCount") !== false) {
								$pos = strpos($cssContent, "/*Color$colorCount");
								
								$for = getDescription($pos);
								
								echo "<td><label for='Color$colorCount'>$for</label></td>";
								$colors[] = substr($cssContent, $pos-7, 6);
								
								$colorCount++;
							}
							echo "</tr><tr>";
							for ($i = 0; $i < count($colors); $i++) {
								echo "<td><input class='color' type='text' id='Color" . ($i+1) . "' name='Color" . ($i+1) ."' value='" . $colors[$i] . "' style='background-color:" . $colors[$i] . "' /></td>";
							}
							echo "</tr></table>";
						?>
					</div>
				</div>
				<div id='LooksTab'>
					<div class='ImageSelectors'>
					Images:
						<?php
							$imageCount = 1;
							
							while (strpos($cssContent, "/*Image$imageCount") !== false) {
								$pos = strpos($cssContent, "/*Image$imageCount");
								$start = strrpos($cssContent, "Images/", -(strlen($cssContent) - $pos)) + strlen("Images/");
								$end = strpos($cssContent, "');", $start);
								$currentVal = substr($cssContent, $start, $end - $start);
								$for = getDescription($pos);
								
								echo "<label for='Image$imageCount'>$for</label><select class='imageChange' id='Image$imageCount' name='Image$imageCount'>
								$imageOptions</select>";
								
								$imageCount++;
							}
						?>
					</div>
					
					
					<div class='BorderStyles'>
					Border/Underline Styles:
						<?php
							$borderCount = 1;
							echo "<table class='editorTable'>";
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
					</div>
				</div>
				<div>
					<input type='hidden' name='file' id='file' value='<?php echo $file;?>' />
					<button id='btnEditContent' type='button'>Edit Content</button>
					<button id='submit' name='submit'>Download</button>
					<div id='contentPages'>
						
					</div>
				</div>
		</div>
		<iframe id='editorContent' style="padding: 5px; border: 1px solid black;" src='http://busn.uco.edu/cpde/TemplateEditor/templates/<?php echo $file; ?>' ></iframe>
		</form>
		
		<div class='loading hide'>Loading...</div>
	</body>
</html>