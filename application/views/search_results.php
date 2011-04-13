		<section id="results">
			<h1>Sökresultat</h1>
			<?php if($results !== FALSE): ?>

			<ul>
			<?php foreach($results as $result): ?>				
				<li>
					<h2><a href="<?php echo $result['url'] ?>"><?php echo $result['title'] ?></a></h2>
					<p><?php echo $result['text']?></p>
					<cite><?php echo $result['meta']?></cite>
				</li>
			<?php endforeach ?>
			</ul>
			
			<?php else: ?>

			<p class="error">Tyvärr, inga resultat hittades!</p>
			
			<?php endif ?>		
		</section>
