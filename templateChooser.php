<!DOCTYPE html>
<html lang='en'>
	<head>
		<title>Template Chooser</title>
		<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="css/chooser.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		
		<script src="js/bootstrap.min.js"></script>
		<style id='chooseCSS'>
			body {
				background-color: #fffff;/*Color1*/
			}
			.row {
				padding-bottom: 10px;
			}
			
		</style>
		<script type='text/javascript'>
			function invertColor(colorToInvert)  {
				var t = [
					"0" + (128 - parseInt((""+colorToInvert).substring(0,2), 16) % 255) + ".",
					"0" + (128 - parseInt((""+colorToInvert).substring(2,4), 16) % 255) + ".",
					"0" + (128 - parseInt((""+colorToInvert).substring(4,6), 16) % 255) + "."
				];
				
				return t[0].substring(t[0].indexOf(".") - 2, t[0].indexOf(".")) + t[1].substring(t[1].indexOf(".") - 2, t[1].indexOf(".")) + t[2].substring(t[2].indexOf(".") - 2, t[2].indexOf("."));
			}
		
			function modifyColor(colorToMod, mod) {
				var color = [
					parseInt((""+colorToMod).substring(0,2), 16),
					parseInt((""+colorToMod).substring(2,4), 16),
					parseInt((""+colorToMod).substring(4,6), 16)
				];
				
				var t = [
					color[0] * mod[0],
					color[1] * mod[1],
					color[2] * mod[2]
				];
				
				for (var i = 0; i < 3; i++) {
					if (t[i] > 255) {
						t[i] = 255;
					} else if (t[i] < 0) {
						t[i] = 0;
					}
					
					t[i] = "0" + t[i].toString(16) + ".";
				}
				
				//console.log(t);
				
				return t[0].substring(t[0].indexOf(".") - 2, t[0].indexOf(".")) + t[1].substring(t[1].indexOf(".") - 2, t[1].indexOf(".")) + t[2].substring(t[2].indexOf(".") - 2, t[2].indexOf("."));
			}
		
			function swapValues(regex, newVal, flag) {
				$('#chooseCSS').html($('#chooseCSS').html().replace(regex, "#" + newVal + ";/*" + flag+ "*/"));
			}
			
			$(document).ready(function() {
				$(".premadeChooser").change(function() {
					
					var colors = new Array();
					
					console.log($("#" + this.id + " > option:selected").data('colors'));
					
					$("#" + $("#" + this.id + " > option:selected").data('colors')).children(".colorPalette").each(function(index) {
						swapValues(new RegExp("#[0-9A-z]+;/\\*" + $(this).data("name")+ "\\*/", "gi"), $(this).data("color"), $(this).data("name"));
						colors[index] = $(this).data("color");
					});
					
					var headerColor = 2;
					var baseColor = 1;
					var textColor = 3;
					
					console.log(colors);
					
					var chooseColor = modifyColor(colors[baseColor], [1.025,1.025,1.025]);
					var styleColor = modifyColor(colors[baseColor], [.975,.975,.975]);
					var contentColor = modifyColor(colors[baseColor], [1.05,1.05,1.05]);
					var downloadColor = modifyColor(colors[baseColor], [.95,.95,.95]);
					
					$("#header").css("background-color", "#" + colors[baseColor]).css("color", "#" + colors[textColor]);
					$("#header h1, #header a").css("color", "#" + colors[textColor]);
					$("#header h3").css("color", "#" + colors[7]);
					
					$("#templates").css("background-color", "#" + colors[baseColor]).css("color", "#" + colors[textColor]);
					$("#templates h1,h2,h3,h4,h5,h6, #templates a").css("color", "#" + colors[headerColor]);
					
					$("#Choose").css("background-color", "#" + chooseColor).css("color", "#" +  colors[textColor]);
					$("#Choose h1,h2,h3,h4,h5,h6, #Choose a").css("color", "#" + colors[headerColor]);
					
					$("#Style").css("background-color", "#" + styleColor).css("color", "#" + colors[textColor]);
					$("#Style h1,h2,h3,h4,h5,h6, #Style a").css("color", "#" + colors[headerColor]);
					
					$("#Content").css("background-color", "#" + contentColor).css("color", "#" + colors[textColor]);
					$("#Content h1,h2,h3,h4,h5,h6, #Content a").css("color", "#" + colors[headerColor]);
					
					$("#Download").css("background-color", "#" + downloadColor).css("color", "#" + colors[textColor]);
					$("#Download h1,h2,h3,h4,h5,h6, #Download a").css("color", "#" + colors[headerColor]);
					
				});
				
				$(".premadeChooser").change();
			});
		</script>
	</head>
	
	<body>
		<!--<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="#">Template Editor</a>
					<ul class="nav">
						<li class="">
							<a href="#">About</a>
						</li>
						<li class="">
							<a href="#Choose">Choose</a>
						</li>
						<li class="">
							<a href="#Style">Style</a>
						</li>
						<li class="">
							<a href="#Content">Content</a>
						</li>
						<li class="">
							<a href="#Download">Download</a>
						</li>
					</ul>
				</div>
			</div>
		</div>-->
		<div class='container' id='mainContainer'>
			<div class='row'>
				<div class='span12' id='header'>
					<h1>DStyler</h1><h3>a simple D2L editor</h3>
					<p>
						The template editor is an open source web app that gives quick way to customize templates to use in D2L. 
					</p>
					
					<!--<p>	
						The process of creating your own template can be done in 4 easy steps; Choose, Style, Content, and Download. You can even modify your template after downloading by uploading the '.template' file.
					</p>
					<p>		
						The web app was created by the University of Central Oklahoma's Center for Elearning and Continuing Education(CeCE) as a tool for our instructional designers and teachers alike. You can contact us at cece.ITech@gmail.com.
					</p>
					<p>	
						The source can be found <a href='https://github.com/ceceITech/TemplateEditor'>here</a>.
					</p>-->
				</div>
				<div class='span12' id='templates'>
					<h2>Click on a template to customize it</h2>
					<?php 
						$dir = getcwd() . "/templates";
						
						// Open a known directory, and proceed to read its contents
						if (is_dir($dir)) {
							if ($dh = opendir($dir)) {
								while (($file = readdir($dh)) !== false) {
									if ($file != '.' && $file != '..' && $file != 'OneColumn') {
										echo "<form method='post' action='templateEditorIframe.php'>";
										echo "$file";
										$colors = "";
										
										$premadeDir = $dir . "/$file/Premade";
										if (is_dir($premadeDir) && $premadeHandle = opendir($premadeDir)) {
											echo "<select class='premadeChooser' id='$file' name='template'>";
											while (($premade = readdir($premadeHandle)) !== false) {
												if ($premade != '.' && $premade != '..') {
													$name = str_replace(".template", "", $premade);
													$display = str_replace("_", " ", $name);
													$display = str_replace("1", "", $display);
													echo "<option value='$file/Premade/$premade' data-colors='$name'>$display</option>";
													//echo "<div class='span6'><div class='row'><div class='span2'><a href='templateEditorIframe.php?template=$file/Premade/$premade'>". $display ."</a></div>";
													
													$handle = fopen($premadeDir ."/". $premade, 'r') or die("File Error " . __LINE__);
													$content = fread($handle, filesize($premadeDir ."/". $premade));
													fclose($handle);
													
													$colors .= "<span class='colorsList' id='$name'>";
													$values = unserialize($content);
													foreach(array_keys($values) as $key) {
														if (strpos($key, "Color") !== false) {
															$colors .= "<span class='colorPalette' data-name='$key' data-color='{$values[$key]}'></span>";
														}
													}
													$colors .= "</span>";
													
													/*
													echo "<div class='span1 preview' data-colorsID='$name'>Preview</div>";
													
													echo "<div class='span2 colorsList' id='$name' >";
													$values = unserialize($content);
													foreach(array_keys($values) as $key) {
														if (strpos($key, "Color") !== false) {
															echo "<span class='colorPalette' data-name='$key' data-color='{$values[$key]}' style='background-color: #{$values[$key]}'></span>";
														}
													}*/
													
													//echo "</div></div></div>";
												}
											}
											echo "</select>";
											echo "<input type='submit' value='submit' />";
											echo "</form>";
											echo $colors;
										}
									}
								}
								closedir($dh);
							}
						}
					?>
					<h3>Or upload template to edit</h3>
					<form action='templateEditorIframe.php' method='post' enctype="multipart/form-data">
						<label for="file">template file: <input type="file" name="file" id="file"></label>
						<input type="submit" name="submit" value="Submit">
					</form>
				</div>
				<div class='span12' id='Choose'>
					<a name="Choose"></a>
					<h2>Choose</h2>
					<span>The first step is to choose the template you want to edit. You can do this by clicking on one of the links above. Don't worry if you cant find exactly what you are looking for, you can change everything later.</span>
				</div>
				<div class='span12' id='Style'>
					<a name="Style"></a>
					<h2>Stylize</h2>
					<span>Next, you will be able to customize the look of the template. You can change everything from colors, images, fonts, and sizes. You can continue to change these settings throughout the process.</span>
				</div>
				<div class='span12' id='Content'>
					<a name="Content"></a>
					<h2>Content</h2>
					<span>The third step is inserting your own content. Click the "Edit Content" button to get started, then double click on the part of the page you want to change. You can paste from Word and insert links to your own pages from this popup. This part is optional, you can download the "empty" template that will still have all the style changes you specified.</span>
				</div>
				<div class='span12' id='Download'>
					<a name="Download"></a>
					<h2>Download</h2>
					<span>Finally, click download to get a zip file of your template. This zip file can be directly uploaded into your D2L course or edited in your favorite editor first. You can come back later and upload the .template file to change anything.</span>
				</div>
			</div>
		</div>
	</body>
</html>
