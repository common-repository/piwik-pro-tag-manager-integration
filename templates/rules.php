<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
<?php if ( ! empty( $domain ) ) : ?>RewriteCond %{HTTP_HOST} <?= $domain; ?>$ [NC]<?= "\n"; ?><?php endif; ?>
<?php if ( ! empty( $rewrite ) && ! empty( $dir ) ) : ?>RewriteRule ^<?= $rewrite; ?>(.*) <?= $dir; ?>$1 [L]<?= "\n"; ?><?php endif; ?>
</IfModule>