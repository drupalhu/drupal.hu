/* global module */
/* global require */
/* global global */

global.drupalRoot = function() {
  'use strict';

  var path = require('path');
  var grunt = require('grunt');

  if (grunt.file.exists('../../includes/bootstrap.inc')) {
    return path.resolve('../..');
  }
  return null;
};

module.exports = function(grunt) {
  "use strict";

  var theme_name = 'drupalhu_theme';

  var global_vars = {
    theme_name: theme_name,
    theme_css: 'assets/css',
    theme_scss: 'assets/sass',
    theme_path: '.',
    theme_lib_path: 'assets/lib',
    theme_scss_path: 'assets/sass',
    theme_css_path: 'assets/css',
    theme_js_path: '/assets/js'
  };

  grunt.file.delete('build/reports');
  grunt.file.mkdir('build/reports');

  var env = grunt.option('env') || 'local';

  var envOptions = {
    local: {
      phpcs: {
        report: 'full',
        reportFile: ''
      },
      coder: {
        extraOptions: [],
        output: ''
      },
      phpDebug: {
        logFile: '',
        logFormat: 'console'
      },
      jsDebug: {
        logFile: '',
        logFormat: 'console'
      },
      scsslint: {
        bundleExec: false,
        config: '.scss-lint.yml',
        reporterOutput: null,
        colorizeOutput: true,
        force: true
      },
      sass: {
        sourceComments: true,
        outputStyle: 'nested',
        stdout: true,
        includePaths: ['<%= global_vars.theme_scss_path %>']
      }
    },
    ci: {
      phpcs: {
        report: 'checkstyle',
        reportFile: 'build/reports/phpcs.checkstyle.xml'
      },
      coder: {
        extraOptions: [
          '--checkstyle'
        ],
        output: '> build/reports/coder.checkstyle.xml'
      },
      phpDebug: {
        logFile: 'build/reports/php.debug.txt',
        logFormat: 'text'
      },
      jsDebug: {
        logFile: 'build/reports/js.debug.txt',
        logFormat: 'text'
      },
      scsslint: {
        bundleExec: false,
        config: '.scss-lint.yml',
        reporterOutput: 'build/reports/scsslint.checkstyle.xml',
        colorizeOutput: false,
        force: true
      },
      sass: {
        outputStyle: 'compressed',
        sourceMap: true,
        stdout: true,
        includePaths: ['<%= global_vars.theme_scss_path %>']
      }
    }
  };
  var options = envOptions[env];

  var files = {
    php: {
      default: [
        "**/*.inc",
        "**/*.install",
        "**/*.module",
        "**/*.php",
        "**/*.profile",
        "!**/*.features.*",
        "!**/*.field_group.inc",
        "!**/*.pages_default.inc",
        "!**/*.strongarm.inc",
        "!**/*.views_default.inc",
        "!node_modules/**"
      ],
      phpcs: {
        extensions: "install,inc,module,php,profile",
        ignore: "node_modules,*.features.*,*.field_group.inc,*.pages_default.inc,*.strongarm.inc,*.views_default.inc,*.variable.inc"
      }
    },
    js: {
      default: [
        "**/*.js",
        "!node_modules/**",
        "!**/lib/**"
      ]
    }
  };

  /**
   * @param {{numMatches: Number}} matches
   */
  var debugFound = function(matches) {
    if (matches.numMatches > 0) {
      var msg = matches.numMatches + " debug message(s) found!!!";
      grunt.log.warn(msg);
      if (env === 'ci') {
        grunt.log.warn('JENKINS: MARK BUILD AS UNSTABLE');
      }
    }
  };

  grunt.initConfig({
    global_vars: global_vars,
    pkg: grunt.file.readJSON("package.json"),

    phplint: {
      files: files.php.default
    },

    phpcs: {
      application: {
        dir: files.php.default
      },
      options: {
        standard: "Drupal",
        ignore: files.php.phpcs.ignore,
        extensions: files.php.phpcs.extensions,
        ignoreExitCode: true,
        report: options.phpcs.report,
        reportFile: options.phpcs.reportFile
      }
    },

    search: {
      phpDebug: {
        files: {
          src: files.php.default
        },
        options: {
          searchString: /var_dump\(|dsm\(|dpm\(|kpr\(/g,
          logFormat: options.phpDebug.logFormat,
          logFile: options.phpDebug.logFile,
          onComplete: function(matches) { debugFound(matches); }
        }
      },
      jsDebug: {
        files: {
          src: files.js.default
        },
        options: {
          searchString: /console\.log\(|console\.table\(|console\.trace\(/g,
          logFormat: options.jsDebug.logFormat,
          logFile: options.jsDebug.logFile,
          onComplete: function(matches) { debugFound(matches); }

        }
      }
    },

    shell: {
      drushCoder: {
        options: {
          stdout: true,
          stderr: true
        },
        command: function() {
          var drushCommandBase = "drush --root='/tmp' -v coder";
          var coderOptions = [
            "--no-empty",
            "--ignorename",
            "--ignore",
            "--minor",
            "--security",
            "--sql"
          ];
          coderOptions = coderOptions.concat(options.coder.extraOptions).join(' ');

          var fileList = grunt.file.expand(files.php.default).join(' ');

          return drushCommandBase + " " + coderOptions + " " + fileList + " " + options.coder.output;
        }
      }
    },

    sass: {
      dist: {
        options: options.sass,
        files: {
          // '<%= global_vars.theme_css_path %>/proxima.css': '<%= global_vars.theme_scss_path %>/proxima.scss',
          // '<%= global_vars.theme_css_path %>/editor.css': '<%= global_vars.theme_scss_path %>/editor.scss',
          // '<%= global_vars.theme_css_path %>/admin_menu.css': '<%= global_vars.theme_scss_path %>/admin_menu.scss',
          '<%= global_vars.theme_css_path %>/bootstrap.css': '<%= global_vars.theme_scss_path %>/bootstrap.scss',
          '<%= global_vars.theme_css_path %>/style.css': '<%= global_vars.theme_scss_path %>/style.scss',
          '<%= global_vars.theme_css_path %>/style-ie.css': '<%= global_vars.theme_scss_path %>/style-ie.scss'
        }
      }
    },

    scsslint: {
      allFiles: [
        '<%= global_vars.theme_scss_path %>/**/*.scss',
      ],
      options: options.scsslint
    },

    watch: {
      grunt: { files: ['Gruntfile.js'] },

      sass: {
        files: '<%= global_vars.theme_scss_path %>/**/*.scss',
        tasks: ['sass'],
        options: {
          livereload: true
        }
      }
    }
  });

  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks("grunt-phplint");
  grunt.loadNpmTasks("grunt-phpcs");
  grunt.loadNpmTasks("grunt-search");
  grunt.loadNpmTasks("grunt-shell");
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-scss-lint');

  grunt.registerTask('build', ['sass']);

  grunt.registerTask("default", "check");

  grunt.registerTask("check", "Full style check.", [
    "phplint",
    "search:phpDebug",
    "search:jsDebug",
    //"scsslint",
    "phpcs",
    "shell:drushCoder"
  ]);
};
