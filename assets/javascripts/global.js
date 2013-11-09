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
		$.ajax({url:'/queue'}).done(function(data) {
			gs.loadView(data, 'queue');
			gs.hideLoader();
			var add_nickname = setTimeout(function(){ $('.cookie_nickname').val(getCookie('gs_nickname')); }, 500);
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
		$('.item_artist').live('click', function() { gs.searchArtist($(this).attr('rel')); });
		$('.addToQueue_add').live('click', function() { gs.addToQueue(gs.selectedSong, "low"); });
		$('.addToQueue_promote').live('click', function() { gs.addToQueue(gs.selectedSong, "high"); });
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
			$.ajax({url:'/queue'}).done(function(data) {
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
		$.ajax({url:'/search/'+$('#search_input').val()}).done(function(data) {
			gs.loadView(data, 'search');
			gs.hideLoader();
		});
	},

	/**
	 * Submit a new artist search
	 */
	searchArtist: function(artist_id) {
		gs.showLoader();
		$.ajax({url:'/search/artist/'+artist_id}).done(function(data) {
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
		$.ajax({url:'/queue'}).done(function(data) {
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
		gs.selectedSong = new Array(song.attr('rel'), song.children('div').children('.song_name').html(), song.children('div').children('.song_artist').html(), song.children('div').children('.song_artist_id').html(), song.children('div').children('.song_image').html());
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
		console.log(song);
		$.post('/queue/add', {songID:song[0], songTitle:song[1], songArtist:song[2], songArtistId:song[3], songImage:song[4], songPriority:priority}, function(data) {
			$('.moreopts').slideUp();
			gs.showModal(data, 2500);
		});
	},

	/**
	 * Set username
	 */
	setUsername: function() {
		document.cookie='gs_nickname='+$('#setUser_textbox').val();
		$.post('/user/register', {nickname: $('#setUser_textbox').val()}, function() {
			location.reload();
		});
	},

	/**
	 * Set username via enter key
	 */
	setUsernameViaKeypress: function(e) {
		if(e.keyCode==13) {
			gs.setUsername();
		}
	},

	/**
	 * Set session
	 */
	setSession: function(session) {
		session = $.parseJSON(session);
		$('#session_name').val(session.title);
		$('#session_id').val(session.id);
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
		data = $.parseJSON(data);
		$('#modal').html(data.msg).css({'opacity':'0.6', 'height':'auto', 'padding':'10px'});
		var hideModal = setTimeout(function() {
			$('#modal').css({'opacity':'0'});
			var hideModal = setTimeout(function(){ $('#modal').css({'height':'0', 'padding':'0'}); }, 500)
		}, time);
	}
}

gs.init();









function getCookies() {
    var c = document.cookie, v = 0, cookies = {};
    if (document.cookie.match(/^\s*\$Version=(?:"1"|1);\s*(.*)/)) {
        c = RegExp.$1;
        v = 1;
    }
    if (v === 0) {
        c.split(/[,;]/).map(function(cookie) {
            var parts = cookie.split(/=/, 2),
                name = decodeURIComponent(parts[0].trimLeft()),
                value = parts.length > 1 ? decodeURIComponent(parts[1].trimRight()) : null;
            cookies[name] = value;
        });
    } else {
        c.match(/(?:^|\s+)([!#$%&'*+\-.0-9A-Z^`a-z|~]+)=([!#$%&'*+\-.0-9A-Z^`a-z|~]*|"(?:[\x20-\x7E\x80\xFF]|\\[\x00-\x7F])*")(?=\s*[,;]|$)/g).map(function($0, $1) {
            var name = $0,
                value = $1.charAt(0) === '"'
                          ? $1.substr(1, -1).replace(/\\(.)/g, "$1")
                          : $1;
            cookies[name] = value;
        });
    }
    return cookies;
}
function getCookie(name) {
    return getCookies()[name];
}
