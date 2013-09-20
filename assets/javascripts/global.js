var gs = gs || {};

gs = {
	/**
	 * Set default view
	 */
	view: 'queue',

	/**
	 * Initiate selected song
	 */
	selectedSong: Array(),

	/**
	 * Initialize default view
	 */
	init: function() {
		gs.bind();
		$.post('f.php?f=initialize', function(data) {
			gs.loadView(data, 'queue');
			gs.hideLoader();
		});
	},

	/**
	 * Set up bindings
	 */
	bind: function() {
		$('#search_submit').live('click', function() { gs.searchSubmit(); });
		$('#search_input').bind('keypress', function(e) { gs.searchSubmitViaKeypress(e); });
		$('#button_queue').live('click', function() { gs.returnToQueue(); });
		$('.item_song').live('click', function() { gs.selectSong($(this)); });
		$('#addToQueue_add').live('click', function() { gs.addToQueue(gs.selectedSong, "low"); });
		$('#addToQueue_promote').live('click', function() { gs.addToQueue(gs.selectedSong, "high"); });
		$('#setUser_submit').live('click', function() { gs.setUsername(); });
		$('#setUser_textbox').bind('keypress', function(e) { gs.setUsernameViaKeypress(e); });
	},

	/**
	 * Load given data into a given view
	 */
	loadView: function(data, setView, scrollToTop) {

		$('#content_loader').html(data);
		if(!scrollToTop) {
			$(window).scrollTop(0);
		}
		gs.view = setView;
		return true;
	},

	/**
	 * Reload the queue
	 */
	reloadQueue: function() {
		if(gs.view == 'queue') {
			$.post('f.php?f=queue', function(data) {
				gs.loadView(data, 'queue', 1);
			});
		}
	},

	/**
	 * Submit a new search
	 */
	searchSubmit: function() {
		$('#search_input').blur();
		gs.showLoader();
		$.post('f.php?f=search', { query : $('#search_input').val() }, function(data) {
			gs.loadView(data, 'search');
			gs.hideLoader();
		});
	},

	/**
	 * Submit search via enter key
	 */
	searchSubmitViaKeypress: function(e) {
		if(e.keyCode==13){
			$('#search_input').blur();
			$('#search_submit').click();
		}
	},

	/**
	 * Return to queue view from search view
	 */
	returnToQueue: function() {
		gs.showLoader();
		$.post('f.php?f=queue', function(data) {
			gs.loadView(data, 'queue');
			$('#search_input').val("");
			gs.hideLoader();
		});
	},

	/**
	 * Select a song when tapped
	 */
	selectSong: function(song) {
		var moreopts = song.children('.moreoptions');
		gs.selectedSong = new Array(song.attr('rel'), song.children('div').children('.song_name').html(), song.children('div').children('.song_artist').html());
		$('.moreoptions').css('height', '0');
		if(moreopts.height() > 1) {
			moreopts.css('height', '0');
		} else {
			moreopts.css('height', '45px');
		}
	},

	/**
	 * Add selected song to queue
	 */
	addToQueue: function(song, priority) {
		$.post('f.php?f=add', {songID:song[0], songTitle:song[1], songArtist:song[2], songPriority:priority}, function(data) {
			$('.moreopts').slideUp();
			gs.showModal(data, 2500);
		});
	},

	/**
	 * Set username
	 */
	setUsername: function() {
		$.post('f.php?f=setname', {set:"true", nickname:$('#setUser_textbox').val() }, function(data) {
			gs.loadView(data, 'queue');
		});
	},

	/**
	 * Set username via enter key
	 */
	setUsernameViaKeypress: function(e) {
		if(e.keyCode==13) {
			$('#setUser_submit').click();
		}
	},
	/**
	 * Show loading modal
	 */
	showLoader: function() {
		$("#loader").show();
	},

	/**
	 * Hide loading modal
	 */
	hideLoader: function() {
		$("#loader").hide();
	},
	/**
	 * Show information modal
	 */
	showModal: function(data, time) {
		$('#modal').html(data).css({'opacity':'0.6', 'height':'auto', 'padding':'10px'});
		var hideModal = setTimeout(function() {
			$('#modal').css({'opacity':'0'});
			var hideModal = setTimeout(function(){ $('#modal').css({'height':'0', 'padding':'0'}); }, 500)
		}, time);
	}
}

gs.init();