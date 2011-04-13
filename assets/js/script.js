$(function() {
	
	// Initerar variabler
	var $links = $('header > nav').find('a');
	var $search = $('#search')
		.after('<div id="ajax-results"></div'); // Lägger till en ajax-div vi kan manipulera
	var $search_input = $search.find('input');
	var $results = $('#ajax-results');
	var $logo = $('header').children('h1');
	
	// Author information
	var $info = $('#author-info').hide();
	var $info_link = $('#info-link');
	
	
	// Clickhandler på "Om mig"
	$info_link.click(function(evt) {
		evt.preventDefault();
		
		$info.slideToggle('slow', function() {
			if($info_link.toggleClass('active').hasClass('active')) {
				$info_link.text('Stäng');
			} else {
				$info_link.text('Om mig');
			}
		});
	});

	// Clickhandler på tabbarna
	$links.click(function(evt) {
		evt.preventDefault();
		
		if($(this).hasClass('active')) {
			$results.slideToggle('slow');
		} else {
			$links.removeClass();
			$(this).addClass('active');
			
			// Fadear in ikonerna
			$logo
				.hide()
				.removeClass()
				.addClass($(this).text())
				.fadeIn('slow');
			
			$results.slideUp('slow', function() {
				$(this).empty();
				
				// Om söksträngen inte börjar på space så trigga söken
				// direkt när vi byter tabb
				if($search_input.val().match(/\S.*/)) {
					$search.trigger('submit');
				}
			});
		}
	});
	
	// Submithandler på sökformuläret
	$search.submit(function(evt) {
		evt.preventDefault();
		
		// Om man sökt från urlen (http://213.114.132.37/search/all/nicholas)
		// kommer "section" fram utanför ajax-results, det tar vi bort
		$search.siblings('#results').slideUp('fast', function() {
			$(this).remove();
		});
				
		if($search_input.val().match(/\S.*/)) {
			// Hämtar urlen från det aktiva menyvalet
			var $url = $links.filter('.active').attr('href');
			// encodeURIComponent fungerade inte bra med sökmotorerna
			var $search_string = $.url.encode($search_input.val());

			$.ajax({
				url: $url+'/'+$search_string,
				dataType: 'html',
				type: 'GET',
				beforeSend: function() {
					$results.slideUp('slow');
					$search_input.addClass('loading');
				},
				success: function(html) {
					$search_input.removeClass();
					$results.html(html).slideDown('slow');
				}
			});
		}
	});
});