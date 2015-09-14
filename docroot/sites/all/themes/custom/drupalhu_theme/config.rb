# Require any additional compass plugins here.

sass_dir           = "sass"
http_path          = "/dist"
css_dir            = "css"
# fonts_dir          = "#{http_path}/fonts"
# images_dir         = "#{http_path}/img"
javascripts_dir    = "js"

# You can select your preferred output style here (can be overridden via the command line):
#output_style = :expanded or :nested or :compact or :compressed
  if environment != :production
    output_style = :expanded
    line_comments = true
    disable_warnings = false
    # give us all the info
    disable_warnings = true
    sass_options = {:quiet => true}
  end


  if environment == :production 
    output_style = :compressed
    line_comments = false
    # keep the build output nice and clean
    disable_warnings = true
    sass_options = {:quiet => true}
  end

# To enable relative paths to assets via compass helper functions. Uncomment:
# relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
# line_comments = false


# If you prefer the indented syntax, you might want to regenerate this
# project again passing --syntax sass, or you can uncomment this:
# preferred_syntax = :sass
# and then run:
# sass-convert -R --from scss --to sass lib scss && rm -rf sass && mv scss sass

Sass::Script::Number.precision = 9

# Change this to :production when ready to deploy the CSS to the live server.
environment = :development
#environment = :production

# In development, we can turn on the FireSass-compatible debug_info.
firesass = false
#firesass = true

##
## You probably don't need to edit anything below this.
##

# You can select your preferred output style here (can be overridden via the command line):
# output_style = :expanded or :nested or :compact or :compressed
output_style = (environment == :development) ? :expanded : :compressed

# To enable relative paths to assets via compass helper functions. Since Drupal
# themes can be installed in multiple locations, we don't need to worry about
# the absolute path to the theme from the server root.
#relative_assets = false

# To disable debugging comments that display the original location of your selectors. Uncomment:
line_comments = (environment == :development) ? true : false

# Pass options to sass. For development, we turn on the FireSass-compatible
# debug_info if the firesass config variable above is true.
sass_options = (environment == :development && firesass == true) ? {:debug_info => true} : {}
