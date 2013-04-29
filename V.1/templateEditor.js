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
	$('style').html($('style').html().replace(regex, "#" + newColor + ";/*" + colorName+ "*/"));
}

function changeFont(fontName, newFont) {
	var regex = new RegExp(": [A-z, -]+;/\\*" + fontName+ "\\*/", "gi");
	$('style').html($('style').html().replace(regex, ": " + newFont + ";/*" + fontName + "*/"));
}

function changeFontSize(fontName, newFont) {
	var regex = new RegExp(": [A-z0-9]+;/\\*" + fontName+ "\\*/", "gi");
	$('style').html($('style').html().replace(regex, ": " + newFont + "px;/*" + fontName + "*/"));
}

function changeImage(imageName, newImage) {
	var regex = new RegExp(": url\\('[A-z0-9/_\\.]*'\\);/\\*" + imageName+ "\\*/", "gi");
	if (regex.test($('style').html())) {
		if (newImage == "none") {
			$('style').html($('style').html().replace(regex, ": none;/*" + imageName+ "*/"));
		} else {
			$('style').html($('style').html().replace(regex, ": url('Images/" + newImage + "');/*" + imageName+ "*/"));
		}
	} else { //none is currently set
		var regex = new RegExp(": none;/\\*" + imageName+ "\\*/", "gi");
		
		if (newImage == "none") {
			$('style').html($('style').html().replace(regex, ": none;/*" + imageName + "*/"));
		} else {
			$('style').html($('style').html().replace(regex, ": url('Images/" + newImage + "');/*" + imageName + "*/"));
		}
	}
}

function changeOpacity(opacityName, newOpacity) {
	//alert($('style').html());
	var regex = new RegExp(": [0-9\\.]*;/\\*" + opacityName+ "\\*/", 'gi');
	var filterRegex = new RegExp(":Alpha(opacity=[0-9]*);/\\*" + opacityName + "Filter\\*/", 'gi');
	
		$('style').html($('style').html().replace(regex, ": " + newOpacity + ";/*" +opacityName + "*/").replace(filterRegex, ":Alpha(opacity=" + (newOpacity * 100) + ");\*" + opacityName + "Filter*/"));
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
		
		$('style').html($('style').html().replace(regex, ": " + h[i] + "em;/*h" + i + "Size"));
	}
}

function changeBorderStyle(id, newVal) {
	var regex = new RegExp(": [A-z0-9]+;/\\*" + id+ "\\*/", "gi");
	
	$('style').html($('style').html().replace(regex, ": " + newVal + ";/*" + id + "*/"));
}

function changeBorderWidth(id, newVal) {
	var regex = new RegExp(": [A-z0-9]+px;/\\*" + id+ "\\*/", "gi");
	
	$('style').html($('style').html().replace(regex, ": " + newVal + "px;/*" + id + "*/"));
}

$(function() {
	$( "#tabs" ).tabs();
});
$(document).ready(function() {
	$(".BorderStyles > input").change(function() {
		changeBorderWidth($(this).attr('id'), $(this).val());
	});
	$(".BorderStyles > input").keypress(function(event) {
		if (event.which < 48 || event.which > 57) {
			event.preventDefault();
		}
	});
	$(".BorderStyles > select").change(function() {
		changeBorderStyle($(this).attr('id'), $(this).val());
	});
	
	$("#headerSize").change(function() {
		changeHeaders($(this).val());
	});
	
	$(":input").keypress(function(event) {
		if (event.which == 13) {
			event.preventDefault();
			$(this).blur();
		}
	});
	
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
	
	$(".BorderStyles > .sliders").each(function(i, el) {
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
		changeImage($(this).attr('id'), $(this).attr('value'));
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
	
	editCount = 0;
	
	//content stuff
	$("#editorContent p").click(function() {
		alert($(this).html());
	
		if (!$(this).attr('id')) {
			$(this).attr('id', editCount++);
		}
		
		//generate input area
		
		//update $(this).html() and set a hidden input value with link to this.id
		
	});
});