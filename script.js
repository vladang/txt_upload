$(function(){
	$('#upload_form').on('submit', function(e){
		e.preventDefault();

        if (!$('#images').val()) {
        	alert('Ошибка, файл не выбран!');
        	return false;
        }

		$('#container').hide();
		$('#loader').show();
		$('submit').attr('disabled', 'disabled');

		var $that = $(this),
		formData = new FormData($that.get(0));
		$.ajax({
			url: $that.attr('action'),
			type: $that.attr('method'),
			contentType: false,
			processData: false,
			data: formData,
			success: function(data){
				loadImages();
                $('submit').removeAttr('disabled');
			}
		});
	});
	loadImages();

	$(window).resize(function(){
		resize();
	});
});

function loadImages() {
    $('#container').hide();
	$('#loader').show();

	$('#container').load('engine.php?mod=images', function() {
    	$('#container').show();
		$('#loader').hide();

    	var imageLoaded = function() {
        	resize();
    	}
    	$('#container img').each(function() {
        	var tmpImg = new Image();
        	tmpImg.onload = imageLoaded;
        	tmpImg.src = $(this).attr('src');
    	});
	});
}

function resize() {

    $('.item').css('width', '');
    $('.item').css('overflow', '');

    var width = parseFloat($('#container').css('width'));
    var str_width = 0;
    var stroka = 1;

    $('.item img').each(function(){

	str_width = parseFloat($(this).css('width'))+str_width;

        $(this).parent().attr('str', stroka);

        if (str_width > width) {
            correct_stroka(stroka, width, str_width);
            str_width = 0;
            stroka++;
        }
    });
}

function correct_stroka(stroka, width, str_width) {

    var count = $('[str=' + stroka + ']').length;
    var minus = parseInt((str_width-width)/count+5);
    var raznica = width-(str_width-parseInt((str_width-width)/count)*count);
    var plus = 0;

    $('[str = ' + stroka + ' ]').each(function(i){

        if (i == 1) plus = raznica; else plus = 0;

        var item_width = parseInt($(this).css('width'))-minus+plus;

    	$(this).css('width',  item_width + 'px');
    	$(this).css('overflow', 'hidden');
    });
}

$(window).resize(function(){
	resize();
});
