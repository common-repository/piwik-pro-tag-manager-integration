<?php if ( ! defined( 'ABSPATH' ) ) exit; ?><?php if ( ! empty( $url ) && ! empty( $container ) && ! empty( $script ) && ! empty( $snippet ) ) : ?><?php if ( version_compare( $snippet, '1.16.0', '>=' ) ) : ?><!-- Piwik PRO Tag Manager <?php if ( ! empty( $time ) ) : ?>@ <?= $time; ?> <?php endif; ?>--><script type="text/javascript">(function(window,document,dataLayerName,id){function stgCreateCookie(a,b,c){var d="";if(c){var e=new Date;e.setTime(e.getTime()+24*c*60*60*1e3),d="; expires="+e.toUTCString()}document.cookie=a+"="+b+d+"; path=/"}var isStgDebug=(window.location.href.match("stg_debug")||document.cookie.match("stg_debug"))&&!window.location.href.match("stg_disable_debug");stgCreateCookie("stg_debug",isStgDebug?1:"",isStgDebug?14:-1);var qP=[];dataLayerName!=="dataLayer"&&qP.push("data_layer_name="+dataLayerName),isStgDebug&&qP.push("stg_debug");var qPString=qP.length>0?("?"+qP.join("&")):"";document.write('<script src="//<?= $url; ?>/'+id+'.sync.js' + qPString + '"></' + 'script>');})(window,document,'dataLayer','<?= $container; ?>');</script><!-- / Piwik PRO Tag Manager --><?= "\n"; ?><?php else : ?><!-- Piwik PRO Tag Manager <?php if ( ! empty( $time ) ) : ?>@ <?= $time; ?> <?php endif; ?>--><script src="<?= $script; ?>"></script><!-- / Piwik PRO Tag Manager --><?= "\n"; ?><?php endif; ?><?php endif; ?>