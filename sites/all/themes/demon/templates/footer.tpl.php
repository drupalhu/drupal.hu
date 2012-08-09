  </div></div> <!-- /#main, /#main-wrapper -->
  <div id="footer-wrapper">
    <div class="section container-24">

    <?php if ($page['footer_firstcolumn'] || $page['footer_secondcolumn'] || $page['footer_thirdcolumn']): ?>
      <div id="footer-columns" class="clearfix">
        <div id="footer-first-col" class="grid-8 suffix-1">
          <?php print render($page['footer_firstcolumn']); ?>
        </div>
        <div id="footer-middle-col" class="grid-7 suffix-1">
          <?php print render($page['footer_secondcolumn']); ?>
        </div>
        <div id="footer-last-col" class="grid-7">
          <?php print render($page['footer_thirdcolumn']); ?>
        </div>
      </div> <!-- /#footer-columns -->
    <?php endif; ?>

    </div><?php  //.section ?>
    <?php if ($page['footer']) { ?>
      <div id="footer" class="clearfix">
        <?php print render($page['footer']); ?>
      </div><?php //#footer ?>
    <?php } ?>
  </div><?php //#footer-wrapper ?>

</div></div><!-- /#page, /#page-wrapper -->
