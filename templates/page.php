<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">
	<h1><?= $title; ?></h1>
	<p><?= $content; ?></p>
	<h2 class="nav-tab-wrapper">
		<?php foreach( $sections as $tab => $label ) : ?>
			<a href="<?= $url; ?>&tab=<?= $tab; ?>" class="nav-tab<?= $tab === $section ? ' nav-tab-active' : ''; ?>"><?= $label; ?></a>
		<?php endforeach; ?>
	</h2>
	<form method="post" action="options.php">
		<?php
			settings_fields( $section );
			do_settings_sections( $section );
			submit_button();
		?>
	</form>
</div>