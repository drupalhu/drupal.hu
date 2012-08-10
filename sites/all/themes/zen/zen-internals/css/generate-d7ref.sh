#!/bin/sh

CORE=~/Sites/core/7.x/;
ZENREF=~/Sites/contrib/zen/7.x-5.x/STARTERKIT/css/drupal7-reference.css;

CSS=( \
	modules/aggregator/aggregator.css \
	modules/aggregator/aggregator-rtl.css \
	modules/block/block.css \
	modules/book/book.css \
	modules/book/book-rtl.css \
	modules/color/color.css \
	modules/color/color-rtl.css \
	modules/comment/comment.css \
	modules/comment/comment-rtl.css \
	modules/contextual/contextual.css \
	modules/contextual/contextual-rtl.css \
	modules/dashboard/dashboard.css \
	modules/dashboard/dashboard-rtl.css \
	modules/dblog/dblog.css \
	modules/dblog/dblog-rtl.css \
	modules/field/theme/field.css \
	modules/field/theme/field-rtl.css \
	modules/field_ui/field_ui.css \
	modules/field_ui/field_ui-rtl.css \
	modules/file/file.css \
	modules/filter/filter.css \
	modules/forum/forum.css \
	modules/forum/forum-rtl.css \
	modules/help/help.css \
	modules/help/help-rtl.css \
	modules/image/image.admin.css \
	modules/image/image.css \
	modules/image/image-rtl.css \
	modules/locale/locale.css \
	modules/locale/locale-rtl.css \
	modules/menu/menu.css \
	modules/node/node.css \
	modules/openid/openid.css \
	modules/openid/openid-rtl.css \
	modules/overlay/overlay-child.css \
	modules/overlay/overlay-child-rtl.css \
	modules/overlay/overlay-parent.css \
	modules/poll/poll.css \
	modules/poll/poll-rtl.css \
	modules/profile/profile.css \
	modules/search/search.css \
	modules/search/search-rtl.css \
	modules/shortcut/shortcut.admin.css \
	modules/shortcut/shortcut.css \
	modules/shortcut/shortcut-rtl.css \
	modules/simpletest/simpletest.css \
	modules/system/system.admin.css \
	modules/system/system.admin-rtl.css \
	modules/system/system.base.css \
	modules/system/system.base-rtl.css \
	modules/system/system.maintenance.css \
	modules/system/system.menus.css \
	modules/system/system.menus-rtl.css \
	modules/system/system.messages.css \
	modules/system/system.messages-rtl.css \
	modules/system/system.theme.css \
	modules/system/system.theme-rtl.css \
	modules/taxonomy/taxonomy.css \
	modules/toolbar/toolbar.css \
	modules/toolbar/toolbar-rtl.css \
	modules/tracker/tracker.css \
	modules/update/update.css \
	modules/update/update-rtl.css \
	modules/user/user.css \
	modules/user/user-rtl.css
	);

# Clean drupal7 ref file.
echo > $ZENREF;

for FILE in ${CSS[*]}; do
  echo				>> $ZENREF;
  echo				>> $ZENREF;
  echo "/*"			>> $ZENREF;
  echo " * "$FILE	>> $ZENREF;
  echo " */"		>> $ZENREF;
  cat $CORE$FILE	>> $ZENREF;
done
