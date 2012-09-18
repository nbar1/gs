function loadIntoContentLoader(data)
{
	$('#content_loader').html(data);
	$(window).scrollTop(0);
	return true;
}

/*
 * Load queue initially
 */
$.post('f.php?f=initialize', function(data){
	loadIntoContentLoader(data);
});

/*
 * Search view
 */
$('.search_submit').click(function(){
	$.post('f.php?f=search', { query : $('.searchbox').val() }, function(data){
		loadIntoContentLoader(data);
	});
});
$('.searchbox').bind('keypress', function(e) {
	if(e.keyCode==13){
		$('.search_submit').click();
	}
});
$('#button_queue').live('click', function(){
	$.post('f.php?f=queue', function(data){
		loadIntoContentLoader(data);
		$('.searchbox').val("");
	});
});

/*
 * Add to queue options
 */
$('.item_search').live('click', function(){
	selectedSong = new Array($(this).attr('rel'), $(this).children('.song_name').html(), $(this).children('.song_artist').html());
	console.log(selectedSong);
	$('#addToQueue_options').fadeIn();
	$('.selected').removeClass('selected');
	$(this).addClass('selected');
});
$(window).scroll(function(){
	if($('#addToQueue_options').is(":visible"))
	{
		$('#addToQueue_options').fadeOut();
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
	$.post('f.php?f=add', {songID:song[0], songTitle:song[1], songArtist:song[2], songPriority:priority }, function(data){
		console.log(data);
		$('#addToQueue_response').html(data);
		$('#addToQueue_response').fadeIn(function(){
			$('.selected').removeClass('selected');
			$('#addToQueue_options').fadeOut();
			var t = setTimeout("$('#addToQueue_response').fadeOut();", 3000);
		});
	});
}

/*
 * Set nickname view
 */
$('#setUser_submit').live('click', function(){
	$.post('f.php?f=setname', {set:"true", nickname:$('#setUser_textbox').val() }, function(data){
		loadIntoContentLoader(data);
	});
});
$('#setUser_textbox').bind('keypress', function(e) {
	if(e.keyCode==13){
		$('#setUser_submit').click();
	}
});
