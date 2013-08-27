var view = 'queue';

function loadIntoContentLoader(data, setView, scrollToTop) {
	$('#content_loader').html(data);
	if(!scrollToTop) {
		$(window).scrollTop(0);
	}
	view = setView;
	return true;
}

function reloadQueue() {
	if(view == 'queue') {
		$.post('f.php?f=queue', function(data) {
			loadIntoContentLoader(data, 'queue', 1);
		});
	}
}

function showLoader() {
	$("#loader").show();
}
function hideLoader() {
	$("#loader").hide();
}

/*
 * Load queue initially
 */
$.post('f.php?f=initialize', function(data) {
	loadIntoContentLoader(data, 'queue');
	hideLoader();
});

/*
 * Search view
 */
$('#search_submit').click(function() {
	$('#search_input').blur();
	showLoader();
	$.post('f.php?f=search', { query : $('#search_input').val() }, function(data) {
		loadIntoContentLoader(data, 'search');
		hideLoader();
	});
});
$('#search_input').bind('keypress', function(e) {
	if(e.keyCode==13){
		$('#search_input').blur();
		$('#search_submit').click();
	}
});
$('#button_queue').live('click', function() {
	showLoader();
	$.post('f.php?f=queue', function(data) {
		loadIntoContentLoader(data, 'queue');
		$('#search_input').val("");
		hideLoader();
	});
});

/*
 * Add to queue options
 */
$('.item_search.closed').live('click', function() {
	$('.moreoptions').hide(0);
	$('.songinfo').show(0);
	$('.item_search').addClass('closed');
	$(this).removeClass('closed');
	selectedSong = new Array($(this).attr('rel'), $(this).children('div').children('.song_name').html(), $(this).children('div').children('.song_artist').html());
	console.log(selectedSong);
	$(this).children('.songinfo').hide(0);
	$(this).children('.moreoptions').show(0);
});

$('#addToQueue_add').live('click', function() {
	addToQueue(selectedSong, "low");
});

$('#addToQueue_promote').live('click', function() {
	addToQueue(selectedSong, "high");
});

function addToQueue(song, priority) {
	console.log(song);
	$.post('f.php?f=add', {songID:song[0], songTitle:song[1], songArtist:song[2], songPriority:priority }, function(data) {
		//$('#addToQueue_response').html('<div class="col-lg-12 response_text btn btn-success">'+data+'</div>');
		console.log(data);
		$('.moreoptions').hide(0, function(){
			$('.songinfo').show0();
		})
	});
}

/*
 * Set nickname view
 */
$('#setUser_submit').live('click', function() {
	$.post('f.php?f=setname', {set:"true", nickname:$('#setUser_textbox').val() }, function(data) {
		loadIntoContentLoader(data, 'queue');
	});
});
$('#setUser_textbox').bind('keypress', function(e) {
	if(e.keyCode==13) {
		$('#setUser_submit').click();
	}
});
