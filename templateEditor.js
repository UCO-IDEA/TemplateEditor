var currentPage = 0;

function resetMCE() {
	$("#popup > #popupContainer > #content").tinymce().remove();
	$("#popup > #popupContainer > #content").tinymce({
		script_url : 'js/tinymce/jscripts/tiny_mce/tiny_mce.js',
		apply_source_formatting : true,
		body_id : "main",
		theme : "advanced",
		width : "100%",
		height : "500",
		forced_root_block: false,
		content_style: $('#contentCSS').html(),
		
		plugins : "table",
		theme_advanced_buttons1 : ",bold,italic,underline,|,justifyleft,justifycenter,justifyright,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,|,code,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : false
   });
}

function disableEnterKey(e)
{
    var key;      
    if(window.event)
        key = window.event.keyCode; //IE
    else
        key = e.which; //firefox
	
	
    return (key != 13);
}

function changeColor(colorName, newColor) {
	var regex = new RegExp("#[0-9A-z]+;/\\*" + colorName+ "\\*/", "gi");
	
	$('#contentCSS').html($('#contentCSS').html().replace(regex, "#" + newColor + ";/*" + colorName+ "*/"));
	
	resetMCE();
}

function changeFont(fontName, newFont) {
	var regex = new RegExp(": [A-z, -]+;/\\*" + fontName+ "\\*/", "gi");
	$('#contentCSS').html($('#contentCSS').html().replace(regex, ": " + newFont + ";/*" + fontName + "*/"));
	resetMCE();
}

function changeFontSize(fontName, newFont) {
	var regex = new RegExp(": [A-z0-9]+;/\\*" + fontName+ "\\*/", "gi");
	$('#contentCSS').html($('#contentCSS').html().replace(regex, ": " + newFont + "px;/*" + fontName + "*/"));
	resetMCE();
}

function changeImage(imageName, newImage) {
	var regex = new RegExp(": url\\('[A-z0-9/_\\.]*'\\);/\\*" + imageName+ "\\*/", "gi");
	if (regex.test($('#contentCSS').html())) {
		if (newImage == "none") {
			$('#contentCSS').html($('#contentCSS').html().replace(regex, ": none;/*" + imageName+ "*/"));
		} else {
			$('#contentCSS').html($('#contentCSS').html().replace(regex, ": url('Images/" + newImage + "');/*" + imageName+ "*/"));
		}
	} else { //none is currently set
		var regex = new RegExp(": none;/\\*" + imageName+ "\\*/", "gi");
		
		if (newImage == "none") {
			$('#contentCSS').html($('#contentCSS').html().replace(regex, ": none;/*" + imageName + "*/"));
		} else {
			$('#contentCSS').html($('#contentCSS').html().replace(regex, ": url('Images/" + newImage + "');/*" + imageName + "*/"));
		}
	}
	resetMCE();
}

function changeOpacity(opacityName, newOpacity) {
	//alert($('#contentCSS').html());
	var regex = new RegExp(": [0-9\\.]*;/\\*" + opacityName+ "\\*/", 'gi');
	var filterRegex = new RegExp(":Alpha(opacity=[0-9]*);/\\*" + opacityName + "Filter\\*/", 'gi');
	
	$('#contentCSS').html($('#contentCSS').html().replace(regex, ": " + newOpacity + ";/*" +opacityName + "*/").replace(filterRegex, ":Alpha(opacity=" + (newOpacity * 100) + ");\*" + opacityName + "Filter*/"));
	resetMCE();
}

function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

function changeHeaders(val) {
	var h = new Array(7);
	
	switch (val) {
		case "Largest": h[1] = 3.5; break;
		case "Larger": h[1] = 3;break;
		case "Large": h[1]=2.5; break;
		case "Default": h[1] = 2; break;
		case "Small": h[1] = 1.5; break;
	}
	h[2] = roundNumber(h[1] * .75, 2);
	h[3] = roundNumber(h[2] * .78, 2);
	h[4] = roundNumber(h[3] * .85 ,2);
	h[5] = roundNumber(h[4] * .83 ,2);
	h[6] = roundNumber(h[5] * .90 ,2);
	
	//alert(h[6] + " " + h[5] + " " + h[4] + " " + h[3] + " " + h[2] + " " + h[1]);
	
	for (var i = 1; i <= 6; i++) {
		var regex = new RegExp(": [0-9\\.em ]+;/\\*h" + i + "Size", "gi");
		
		$('#contentCSS').html($('#contentCSS').html().replace(regex, ": " + h[i] + "em;/*h" + i + "Size"));
	}
	resetMCE();
}

function changeBorderStyle(id, newVal) {
	var regex = new RegExp(": [A-z0-9]+;/\\*" + id+ "\\*/", "gi");
	
	$('#contentCSS').html($('#contentCSS').html().replace(regex, ": " + newVal + ";/*" + id + "*/"));
	resetMCE();
}

function changeBorderWidth(id, newVal) {
	var regex = new RegExp(": [A-z0-9]+px;/\\*" + id+ "\\*/", "gi");
	
	$('#contentCSS').html($('#contentCSS').html().replace(regex, ": " + newVal + "px;/*" + id + "*/"));
	resetMCE();
}

$(window).bind('beforeunload', function(){
	return '>>>>>Before You Go<<<<<<<< \n Reloading or exiting this page could cause you to lose any undownloaded content! \n Please be careful when exiting.';
});

function getDefaults() {
	//get defaults
	$("#editorContent p[id],h1[id],h2[id],h3[id],h4[id],h5[id],h6[id],blockquote[id]").each( function(i) {
		$("#Page" + currentPage + "Items").append("<input type='hidden' id='Page" + currentPage + $(this).attr('id') + "' name='Page" + currentPage + $(this).attr('id') + "' value='" + $(this).html() + "' />");
	});
}

 function getNewContent() {
	$.post(
		"GetFileContent.php",
		{
			"file": $("#file").val()
		}, 
		function(data) {
			$("#editorContent").html(data);
			getDefaults();
		},
		"html"
	);
 }

$(document).ready(function() {
	$( "#tabs" ).tabs();
	
	var code = "";
	var delay = 0;
	$(document.documentElement).keyup(function(event) {
		code += event.which;
		delay = 0;
		if (code.indexOf("38384040373937396665") != -1) {
			code = "";
			changeImage("Image1", "contra.jpg");
		}
	});
	
	function konamiReset() {
		if (delay == 1) {
			code = "";
		} else  {
			delay = 1;
		}
	}
	
	window.setInterval(konamiReset, 500);
	
	$(".BorderStyles > input").change(function() {
		changeBorderWidth($(this).attr('id'), $(this).val());
	});
	$(".BorderStyles > input").keypress(function(event) {
		if (event.which < 48 || event.which > 57) {
			event.preventDefault();
		}
	});
	$(".borderStyleChange").change(function() {
		changeBorderStyle($(this).attr('id'), $(this).val());
	});
	
	$("#headerSize").change(function() {
		changeHeaders($(this).val());
	});
	
/* 	$(":input").keypress(function(event) {
		if (event.which == 13) {
			event.preventDefault();
			$(this).blur();
		}
	}); */
	
	//style stuff
	$(".OpacitySelectors > .sliders").each(function(i, el) {
		el = $(el);
		var val = $("#Opacity"+$(el).attr('id')).val()*100;
		
		el.slider({
			min: 0,
			max: 100,
			value: val,
			slide: function(event, ui) {
				changeOpacity("Opacity" + $(this).attr('id'), ui.value/100);
				$("#Opacity"+$(this).attr('id')).val(ui.value/100);
			 }
		});
	});
	
	$(".fontSizeSelectors > .sliders").each(function(i, el) {
		el = $(el);
		var val = $("#FontSize"+$(el).attr('id')).val();
		
		el.slider({
			min: 10,
			max: 22,
			step:2,
			value: val,
			slide: function(event, ui) {
				changeFontSize("FontSize" + $(this).attr('id'), ui.value);
				$("#FontSize"+$(this).attr('id')).val(ui.value);
			 }
		});
	});
	
	$(".BorderSliders").each(function(i, el) {
		el = $(el);
		var val = $("#BorderSize"+$(el).attr('id')).val();
		
		el.slider({
			min: 1,
			max: 10,
			value: val,
			slide: function(event, ui) {
				changeFontSize("BorderWidth" + $(this).attr('id'), ui.value);
				$("#BorderWidth"+$(this).attr('id')).val(ui.value);
			 }
		});
	});
	
	$(".ImageSelectors > select").change(function() {
		changeImage($(this).attr('id'), $(this).prop('value'));
	});
	
	$(".fontSelectors > select").change(function() {
		changeFont($(this).attr('id'), $(this).attr('value'));
	});
	
	$('.color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			changeColor(el.id, hex);
			$(el).css("background-color", "#"+hex);
			$(el).ColorPickerHide();
	},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});
	
	//content stuff
	var maxPage = 0;
	
	$("#popupClose").click(function() {
		$("#popup").hide(100);
	});
	
	$("#popupSubmit").click(function() {
		$("#" + $("#popup > #popupContainer > #toUpdate").val()).html($("#popup > #popupContainer > #content").val());
		$("#Page" + currentPage + $("#popup > #popupContainer > #toUpdate").val()).val( $("#popup > #popupContainer > #content").val());
		$("#popup").hide(100);
	});
	
	$("#btnEditContent").click(function() {
		$("#newFileName").val("Page" + (currentPage+1));
		$("#newPageTitle").val("Page " + (currentPage+1));
		$("#newFilePopup").show(100);
	});
	
	$("#editorContent").on('mouseenter', "p,h1,h2,h3,h4,h5,h6,blockquote", function() {
		if ($(this).attr('id')) {
			$(this).css('cursor', 'url(images/edit.gif), crosshair');
		}
	}).on('mouseleave', "p,h1,h2,h3,h4,h5,h6,blockquote", function() {
		if ($(this).attr('id')) {
			$(this).css('cursor', 'auto');
		}
	 });
	
	$("#editorContent").on('dblclick', "p,h1,h2,h3,h4,h5,h6,blockquote", function() {
		if ($(this).attr('id')) {
			//generate input area
			$("#popup > #popupContainer > #content").val($(this).html());
			$("#popup > #popupContainer > #toUpdate").val($(this).attr('id'));
			$("#popup").show(100);
			
			//update/set a hidden input value with link to this.id
			if (!$("#Page" + currentPage + $(this).attr('id')).length) {
				$("#Page" + currentPage + "Items").append("<input type='hidden' value='" + $(this).html() + "' id='Page" + currentPage + $(this).attr('id') + "' name='Page" + currentPage + $(this).attr('id') + "' />");
			}
		}
	});
	
	//new File dialog
	$("#newFileSubmit").click(function() {
		currentPage++;
		maxPage++;
		
		$("form").append("<div id='Page"+ currentPage +"Items'></div>");
		
		$("#Page" + currentPage + "Items").append("<input type='hidden' id='Page" + currentPage + "Name' name='Page" + currentPage + "FileName' value='" + $("#newFileName").val() + "' />");
		$("#Page" + currentPage + "Items").append("<input type='hidden' id='Page" + currentPage + "Title' name='Page" + currentPage + "PageTitle' value='" + $("#newPageTitle").val() + "' />");
		
		getNewContent();//ajax call
		
		$("#btnEditContent").html("Add New Page");
		$("#contentPages").append("<button type='button' class='changeTo' id='Page" + currentPage + "' >" + $("#newFileName").val() + "</button>");
		
		$("#newFilePopup").hide(100);
	});
	
	$("#contentPages").on('click', 'button', function() {
		var page = $(this).attr('id');
		
		currentPage = parseInt($(this).attr('id').replace("Page", ""));
		
		$('#' + $(this).attr('id') + "Items > *").each(function(index) {
			$("#" + $(this).attr('id').replace(page, "")).html($(this).val());
		});
	});
	
	$("#filePopupClose").click(function() {
		$("#newFilePopup").hide(100);
	});
	
	$("#popup > #popupContainer > #content").tinymce({
		script_url : 'js/tinymce/jscripts/tiny_mce/tiny_mce.js',
		apply_source_formatting : true,
		body_id : "main",
		theme : "advanced",
		width : "100%",
		height : "500",
		forced_root_block: false,
		content_style: $('#contentCSS').html(),
		
		plugins : "table",
		theme_advanced_buttons1 : ",bold,italic,underline,|,justifyleft,justifycenter,justifyright,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,|,code,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : false
   });
});