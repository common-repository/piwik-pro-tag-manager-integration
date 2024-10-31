<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?= $message; ?>: <code>$ crontab -e</code><br />
<pre><code>MAILTO=""
* * * * * wget -qO- <?= $domain; ?>/wp-cron.php &> /dev/null</code></pre>