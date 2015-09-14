# Maintaining the Drupal Bootstrap Project

## Prerequisites
This project relies heavily on NodeJS/Grunt to automate some very time
consuming tasks and to ensure effective management. If you do not have these
CLI tools, please install them now:

* https://nodejs.org
* http://gruntjs.com

## Installation

This project's installation may initially take a while to complete. Please read
through this entire README before continuing so you are aware of what to expect.
Suffice it to say: you will not have to manually update this project again.

After you have installed the prerequisite CLI tools, run `npm install` in this
directory. This will install the necessary NodeJS modules inside the
`node_modules` folder.

After NodeJS has finished installing its own modules, it will automatically
invoke `grunt install` for you. This is a grunt task that is specifically
designed to keep the project in sync amongst maintainers.

## Grunt
There are several tasks available to run, please execute `grunt --help` to view
the full list of tasks currently available. This README only covers the most
important or commonly used tasks.

### `grunt install`
This task is automatically invoked as a `postinstall` event of `npm install`.
There should be no need to manually invoke this task. However, if you feel the
need to manual invoke this task, you may do so without fear of producing any
unintended side-effects. This is simply an alias for sub-tasks defined below.

### `grunt githooks`
This is a sub-task of `grunt install`. It will automatically install the
necessary git hooks for this project. These git hooks allow the project to keep
track of changes to files in order to automate and ensure certain files are
committed (e.g. compiled CSS files). Do not worry, if you already have existing
git hook files in place, this will work around them.

Any time there is a change to `package.json`, `Gruntfile.js`, `.githooks.js.hbs`
or any of the files in the `grunt` subdirectory, the `npm install` task will
automatically be executed by the git hook itself. This allows the workflow to
be altered by one maintainer and those changes propagated to the others the
next time they pull down the repository.

### `grunt sync`
This is a sub-task used by `grunt install`. It will automatically
download and install the various 3.x.x versions of the Bootstrap and Bootswatch
libraries for local development purposes in the `./lib` folder. This process
utilizes Bower and can initially take a while for it to fully complete.

Once you have the various versions of libraries have been installed, this task
becomes much faster. This task utilizes the jsDelivr API to determine which
versions to install. To avoid abusing API calls, this list is cached for one
week as the `.libraries` file in the root of this project. In the event that a
new list needs to be generated and the week cache expiration has not lifted,
you can either simply remove the file manually or run `grunt sync --force` to
force an API call and generate a new list.

### `grunt clean-vendor-dirs`
This is a sub-task used by `grunt install`. Drupal currently does not exclude
vendor directories when scanning directories of modules/themes to look for
.info files. Some NodeJS modules actually are installed with files that have
this same extension, yet cannot be parsed by Drupal. Due to the nature of how
Drupal currently parses these files, it can cause a PCRE recursion in PHP. This
ultimately leads to a segmentation fault and thus rendering the site inoperable.
For more details, see: https://www.drupal.org/node/2329453

### `grunt compile`
This task ensures that all the necessary variations of versions and themes of
Bootstrap and Bootswatch are compile from `starterkits/less/less/overrides.less`.
Typically, this task generates hundreds of files and can take upwards of
\~10 seconds to fully complete.

Optionally, if the `--dev` parameter is specified, this task will only compile
the starterkit's `overrides.less` file for the latest version of Bootstrap: 
* `./css/<%= latestVersion/overrides.css`
* `./css/<%= latestVersion/overrides.min.css`

### `grunt watch`
This task is responsible for watching various source files and executing the
appropriate tasks these source files are normally consumed by. With the caveat
of long compilation times mentioned in the previous section, it is highly
recommended running this task as such: `grunt watch --dev`. Keep in mind that
this limits the rapid development of the `overrides.less` file to the default
Bootstrap theme. If you have switched themes, you must manually compile all
the version and theme override files.
