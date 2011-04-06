<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<!-- Låt vara om .htaccess används, annars ta bort kommentar
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> -->
	
	<title>Arbetsprov: Sök på populära sökmotorer</title>
	<meta name="author" content="Nicholas Ruunu - nicholas.ruunu@gmail.com">
	<meta name="description" content="Sök på Google, Bing, Yahoo eller alla på en gång!">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="icon" href="<?php echo base_url() ?>favicon.ico">
	<link rel="apple-touch-icon" href="<?php echo base_url() ?>assets/img/apple-touch-icon.png">
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/style.css">
	
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<div id="wrapper">
		<header>
			<h1 class="<?php echo $site ?>">Nicholas Ruunu, Webbdesigner Extraordinairé</h1>
			<nav>
				<ul>
				<?php foreach($menu_array as $menu => $active): ?>
					<li><a<?php if($active) echo ' class="active"' ?> href="<?php echo site_url("search/{$menu}") ?>"><?php echo $menu ?></a></li>
				<?php endforeach ?>				
				</ul>
			</nav>
		</header>

		<div id="main">
			<?php echo form_open("search/{$site}", array('id' => 'search')) ?>
				<div><?php echo form_input('search_string', $search_string,
					'placeholder="Skriv in din söksträng!" pattern="\S.*" autofocus required') ?></div>
				<div><button name="submit">Sök</button></div>
			<?php echo form_close() ?>

		<?php if( ! empty($search_results)): ?>		
			<?php $this->load->view('search_results', $search_results) ?>
		<?php endif ?>
		
			<footer>
				<small><strong>Verktyg:</strong>
				<a href="//codeigniter.com">CodeIgniter 2.0.1</a>,
				<a href="//jquery.com">jQuery 1.5.1</a>,
				<a href="//html5boilerplate.com">HTML5 Boilerplate</a><br>
				<em>Utvecklad för moderna webbläsare</em></small>
			</footer>
		</div>
	</div>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/jquery.urldecoder.min.js"></script>
	<script>
		// Enkel jQuery, inline för lättare översikt
		$(function() {
		
			// Initerar variabler
			var $links = $('nav > ul > li > a');
			var $search = $('#search')
				.after('<div id="ajax-results"></div'); // Lägger till en ajax-div vi kan manipulera
			var $search_input = $search.find('input');
			var $results = $('#ajax-results');
			var $logo = $('header').children('h1');

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
				$search.siblings('section').slideUp('fast', function() {
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
	</script>
</body>
</html>