<?php
/**
 * @file
 * html.tpl.php
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]><html lang="<?php print $language->language; ?>" class="no-js ie lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html lang="<?php print $language->language; ?>" class="no-js ie lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html lang="<?php print $language->language; ?>" class="no-js ie lt-ie9"><![endif]-->
<!--[if (IE 9)&!(IEMobile)]><html lang="<?php print $language->language; ?>" class="no-js ie ie9"><![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!--><html lang="<?php print $language->language; ?>" class="no-js" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"><!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
  <!-- [if IE]>
  <meta http-equiv="X-UA-Compatible" value="IE=Edge">
  <meta http-equiv="imagetoolbar" content="no" />
  <meta name="MSSmartTagsPreventParsing" content="true" />
  <meta name="mobileoptimized" content="0" />
  <![endif]-->

  <?php print $head; ?>
  <title><?php print $head_title; ?></title>
  <?php print $styles; ?>

  <!--[if lt IE 9]>
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
  <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script type="text/javascript">
  // D'oh...
  if (navigator.userAgent.match(/MSIE 10/)) {
    document.getElementsByTagName('html')[0].className += ' ie ie10 ';
  }
  </script>

  <?php print $scripts; ?>

  <!-- apple-touch-icons -->
  <!-- icons -->
  <!-- shortuct icon -->
  <!-- msapplication tile settings -->
</head>
<body class="<?php print $classes; ?>" <?php print $attributes;?>>

<?php print isset($accessibility_script) ? $accessibility_script : ''; ?>

<div id="skip-link">
  <a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
</div>

<?php print $page_top; ?>

<?php if (isset($editor_menu)): ?>
  <nav class="editor-menu">
    <?php print render($editor_menu); ?>
  </nav>
<?php endif; ?>

<?php print $page; ?>
<?php print $page_bottom; ?>

</body>
</html>
