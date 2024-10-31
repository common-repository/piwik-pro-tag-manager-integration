<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?= $message; ?>:<br /><code><?= $file; ?></code>
<pre><code># BEGIN <?= $marker; ?><br /><?= $rules; ?><br /># END <?= $marker; ?></code></pre>