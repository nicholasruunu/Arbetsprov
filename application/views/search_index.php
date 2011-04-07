<!doctype html>
<html>
<head>
	<meta charset="UTF-8">

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
				<?php foreach($menu_array as $site => $value): ?>
					<li><a<?php if($value['active']) echo ' class="active"' ?>
					title="<?php echo $value['title'] ?>" href="<?php echo site_url("search/{$site}") ?>"><?php echo $site ?></a></li>
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
	<!-- Faller inte tillbaka på en lokal version eftersom uppkoppling måste finnas !-->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/jquery.urldecoder.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/script.js"></script>
</body>
</html>