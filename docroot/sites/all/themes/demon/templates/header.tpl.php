<?php
/**
 * @file
 * Header elements.
 *
 * Everything between top of the page and the primary tabs.
 */
?>
<div id="header" class="<?php print $secondary_menu ? 'with-secondary-menu ' : 'without-secondary-menu '; ?>container-24">
  <div class="section clearfix">
    <?php if ($secondary_menu): /* positioned to top-right corner of header, that is why we print this here */ ?>
      <div id="secondary-menu" class="navigation">
        <?php print theme(
          'links__system_secondary_menu', array(
            'links' => $secondary_menu,
            'attributes' => array(
              'id' => 'secondary-menu-links',
              'class' => array('links', 'inline', 'clearfix'),
            ),
            'heading' => array(
              'text' => t('Secondary menu'),
              'level' => 'h2',
              'class' => array('element-invisible'),
            ),
          )
        ); ?>
      </div> <!-- /#secondary-menu -->
    <?php endif; ?>

    <?php if ($logo): ?>
      <a href="<?php print $front_page; ?>"
         title="<?php print t('Home'); ?>" rel="home" id="logo"
         class="grid-6">
        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>"/>
      </a>
    <?php endif; ?>

    <?php if ($site_name || $site_slogan): ?>
      <div id="name-and-slogan"<?php if ($hide_site_name && $hide_site_slogan): print ' class="element-invisible"'; endif; ?>>
        <?php if ($site_name): ?>
          <?php if ($title): ?>
            <div id="site-name"<?php if ($hide_site_name): print ' class="element-invisible"'; endif; ?>>
              <strong>
                <a href="<?php print $front_page; ?>"
                   title="<?php print t('Home'); ?>"
                   rel="home"><span><?php print $site_name; ?></span></a>
              </strong>
            </div>
          <?php else: /* Use h1 when the content title is empty */ ?>
            <h1 id="site-name"<?php if ($hide_site_name): print ' class="element-invisible"'; endif; ?>>
              <a href="<?php print $front_page; ?>"
                 title="<?php print t('Home'); ?>"
                 rel="home"><span><?php print $site_name; ?></span></a>
            </h1>
          <?php endif; ?>
        <?php endif; ?>

        <?php if ($site_slogan): ?>
          <div id="site-slogan"<?php if ($hide_site_slogan): print ' class="element-invisible"'; endif; ?>>
            <?php print $site_slogan; ?>
          </div>
        <?php endif; ?>
      </div> <!-- /#name-and-slogan -->
    <?php endif; ?>

    <div id="header-content">
      <?php print render($page['header']); ?>
    </div>
    <?php if ($main_menu): ?>
      <div id="main-menu" class="navigation grid-17 prefix-1 ">
        <?php print theme(
          'links__system_main_menu', array(
            'links' => $main_menu,
            'attributes' => array(
              'id' => 'main-menu-links',
              'class' => array('links', 'clearfix'),
            ),
            'heading' => array(
              'text' => t('Main menu'),
              'level' => 'h2',
              'class' => array('element-invisible'),
            ),
          )
        ); ?>
      </div> <!-- /#main-menu -->
    <?php endif; ?>
  </div>
</div>
<!-- /.section, /#header -->

<div id="triptych-wrapper">
  <?php if ($page['triptych_first'] || $page['triptych_middle'] || $page['triptych_last']): ?>
    <div id="triptych" class="clearfix container-24">
      <div class="grid-8"><?php print render($page['triptych_first']); ?></div>
      <div class="grid-8"><?php print render($page['triptych_middle']); ?></div>
      <div class="grid-8"><?php print render($page['triptych_last']); ?></div>
    </div><!-- /#triptych -->
  <?php endif; ?>
</div>
<!-- /#triptych-wrapper -->

<?php if ($messages): ?>
  <div id="messages" class="container-24">
    <div class="section clearfix">
      <?php print $messages; ?>
    </div>
  </div> <!-- /.section, /#messages -->
<?php endif; ?>

<?php
  /* if ($page['featured']): ?>
    <div id="featured"><div class="section clearfix">
      <?php print render($page['featured']); ?>
    </div></div> <!-- /.section, /#featured -->
  <?php endif; */
?>

<?php if ($page['front_highlighted']): ?>
  <div id="highlighted-wrapper">
    <div id="highlighted" class="container-24 clearfix">
      <?php print render($page['front_highlighted']); ?>
    </div>
  </div>
<?php endif; ?>
