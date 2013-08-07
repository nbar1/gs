var view = 'queue';
function loadIntoContentLoader(data, setView, scrollToTop)
{
	$('#content_loader').html(data);
	if(!scrollToTop)
	{
		$(window).scrollTop(0);
	}
	view = setView;
	return true;
}

function reloadQueue()
{
	if(view == 'queue')
	{
		$.post('f.php?f=queue', function(data){
			loadIntoContentLoader(data, 'queue', 1);
		});
	} else {
		console.log(view);
	}
}

/*
 * Load queue initially
 */
$.post('f.php?f=initialize', function(data){
	loadIntoContentLoader(data, 'queue');
});

/*
 * Search view
 */
$('#search_submit').click(function(){
	$('#search_input').blur();
	$.post('f.php?f=search', { query : $('#search_input').val() }, function(data){
		loadIntoContentLoader(data, 'search');
	});
});
$('#search_input').bind('keypress', function(e) {
	if(e.keyCode==13){
		$('#search_input').blur();
		$('#search_submit').click();
	}
});
$('#button_queue').live('click', function(){
	$.post('f.php?f=queue', function(data){
		loadIntoContentLoader(data, 'queue');
		$('#search_input').val("");
	});
});

/*
 * Add to queue options
 */
$('.item_search').live('click', function(){
	selectedSong = new Array($(this).attr('rel'), $(this).children('div').children('.song_name').html(), $(this).children('div').children('.song_artist').html());
	console.log(selectedSong);
	$('#addToQueue_response').hide();
	$('#backToQueue_options').fadeOut(function(){
		$('#addToQueue_options').fadeIn();
	});
	$('.selected').removeClass('selected');
	$(this).addClass('selected');
});
$(window).scroll(function(){
	if($('#addToQueue_options').is(":visible"))
	{
		$('#addToQueue_options').fadeOut(function(){
			$('#backToQueue_options').fadeIn();
		});
		$('.selected').removeClass('selected');
	}
});
$('#addToQueue_add').live('click', function(){
	addToQueue(selectedSong, "low");
});
$('#addToQueue_promote').live('click', function(){
	addToQueue(selectedSong, "high");
});
function addToQueue(song, priority)
{
	console.log(song);
	$.post('f.php?f=add', {songID:song[0], songTitle:song[1], songArtist:song[2], songPriority:priority }, function(data){
		$('#addToQueue_response').html('<div class="col-lg-12 response_text btn btn-success">'+data+'</div>');
		console.log(data);
		$('#addToQueue_options').fadeOut(function(){
			$('.selected').removeClass('selected');
			$('#addToQueue_response').fadeIn();
			var t = setTimeout("$('#addToQueue_response').fadeOut(function(){$('#backToQueue_options').fadeIn();$('#addToQueue_options').hide();});", 3000);
		});
	});
}

/*
 * Set nickname view
 */
$('#setUser_submit').live('click', function(){
	$.post('f.php?f=setname', {set:"true", nickname:$('#setUser_textbox').val() }, function(data){
		loadIntoContentLoader(data, 'queue');
	});
});
$('#setUser_textbox').bind('keypress', function(e) {
	if(e.keyCode==13){
		$('#setUser_submit').click();
	}
});
