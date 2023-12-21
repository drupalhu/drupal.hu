<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo\Task;

use Sweetchuck\Utils\Filter\FileSystemExistsFilter;
use Symfony\Component\Finder\Finder;

class ArtifactCollectFilesTask extends BaseTask {

  protected string $taskName = 'App - Collect files to release';

  protected string $projectDir = '.';

  public function getProjectDir(): string {
    return $this->projectDir;
  }

  public function setProjectDir(string $dir): static {
    $this->projectDir = $dir;

    return $this;
  }

  protected string $drupalRootDir = 'docroot';

  public function getDrupalRootDir(): string {
    return $this->drupalRootDir;
  }

  public function setDrupalRootDir(string $dir): static {
    $this->drupalRootDir = $dir;

    return $this;
  }

  protected string $buildDir = '';

  public function getBuildDir(): string {
    return $this->buildDir;
  }

  public function setBuildDir(string $dir): static {
    $this->buildDir = $dir;

    return $this;
  }

  /**
   * @phpstan-param array<string, mixed> $options
   */
  public function setOptions(array $options): static {
    parent::setOptions($options);

    if (array_key_exists('projectDir', $options)) {
      $this->setProjectDir($options['projectDir']);
    }

    if (array_key_exists('drupalRootDir', $options)) {
      $this->setDrupalRootDir($options['drupalRootDir']);
    }

    if (array_key_exists('buildDir', $options)) {
      $this->setBuildDir($options['buildDir']);
    }

    return $this;
  }

  protected function runHeader(): static {
    $this->printTaskInfo($this->getProjectDir());

    return $this;
  }

  protected function runAction(): static {
    $buildDir = $this->getBuildDir() ?: 'artifact';
    $buildDirSafe = preg_quote($buildDir, '@');

    $projectDir = $this->getProjectDir();
    $docroot = 'docroot';
    $docrootSafe = preg_quote($docroot, '@');

    $outerSitesDir = 'sites';
    $outerSitesDirSafe = preg_quote($outerSitesDir, '@');

    $files = (new Finder())
      ->in($projectDir)
      ->notPath("@^{$buildDirSafe}@")
      ->notPath("@^{$docrootSafe}/sites/simpletest/@")
      ->name('*.yml')
      ->name('*.twig')
      ->files();

    $dirs = [
      "$docrootSafe/modules",
      "$docrootSafe/themes",
      "$docrootSafe/profiles",
      "$docrootSafe/libraries",
      "$docrootSafe/sites/[^/]+/modules",
      "$docrootSafe/sites/[^/]+/themes",
      "$docrootSafe/sites/[^/]+/profiles",
      "$docrootSafe/sites/[^/]+/libraries",
      'drush/Commands',
      "$docrootSafe/sites/[^/]+/drush/Commands",
    ];
    foreach ($dirs as $dir) {
      $files
        ->path("@^$dir/custom/@")
        ->notPath("@$dir/custom/[^/]+/node_modules/@");
    }

    $this
      ->configFinderGit($files)
      ->configFinderPhp($files)
      ->configFinderCss($files, FALSE)
      ->configFinderJavaScript($files)
      ->configFinderImages($files)
      ->configFinderFont($files)
      ->configFinderOs($files)
      ->configFinderIde($files);
    $this->assets['files'][] = $files;

    $this->assets['files'][] = (new Finder())
      ->in($projectDir)
      ->notPath("@^{$buildDirSafe}@")
      ->notPath("@^{$docrootSafe}/sites/simpletest/@")
      ->path("@$docrootSafe/sites/[^/]+/@")
      ->name('settings.php')
      ->name('services.yml')
      ->files();

    $files = (new Finder())
      ->in($projectDir)
      ->notPath("@^{$buildDirSafe}@")
      ->notPath("@^{$docrootSafe}/sites/simpletest/@")
      ->path("@^{$outerSitesDirSafe}/[^/]+/translations/@")
      ->path("@^{$outerSitesDirSafe}/[^/]+/config/@")
      ->ignoreDotFiles(FALSE)
      ->files();
    $this->assets['files'][] = $files;

    $this->assets['files'][] = (new Finder())
      ->in($projectDir)
      ->path("@^drush/@")
      ->notPath("@^{$buildDirSafe}@")
      ->notPath("@^drush/Commands/@")
      ->name('*.yml')
      ->notName('drush.local.example.yml')
      ->notName('drush.local.yml')
      ->files();

    $this->assets['files'][] = (new Finder)
      ->in($projectDir)
      ->notPath("@^{$buildDirSafe}@")
      ->notPath("@^{$docrootSafe}/sites/simpletest/@")
      ->path('@^patches/@')
      ->name('*.patch')
      ->files();
    $this->assets['files'][] = 'composer.json';
    $this->assets['files'][] = 'composer.lock';
    $this->assets['files'][] = "{$docroot}/autoload.php";
    $this->assets['files'][] = "{$docroot}/index.php";

    $this->assets['files'] = array_merge(
      $this->assets['files'],
      array_filter(
        [
          "$docroot/.htaccess",
          "$docroot/favicon.ico",
          "$docroot/robots.txt",
          'sites/all/assets/robots-additions.txt',
          'patches.lock',
          'patches.lock.json',
        ],
        (new FileSystemExistsFilter())->setBaseDir($projectDir),
      ),
    );

    $this->assets['files'][] = (new Finder())
      ->in($projectDir)
      ->notPath("@^{$buildDirSafe}@")
      ->path("@^hooks/@")
      ->ignoreDotFiles(FALSE)
      ->notName('README.md')
      ->files();

    return $this;
  }

  protected function configFinderGit(Finder $finder): static {
    $finder
      ->notPath('.git')
      ->notPath('.gtm')
      ->notName('.gitignore');

    return $this;
  }

  protected function configFinderPhp(Finder $finder): static {
    $finder
      ->name('*.php')
      ->name('*.inc')
      ->name('*.install')
      ->name('*.module')
      ->name('*.theme')
      ->name('*.profile')
      ->name('*.engine')
      ->notPath('vendor')
      ->notName('.phpbrewrc')
      ->notName('composer.lock')
      ->notName('phpcs.xml.dist')
      ->notName('phpcs.xml')
      ->notName('phpunit.xml.dist')
      ->notName('phpunit.xml');

    return $this;
  }

  protected function configFinderCss(Finder $finder, bool $withImportable): static {
    $finder
      ->name('*.css')
      ->notPath('.sass-cache')
      ->notName('config.rb')
      ->notName('.sass-lint.yml')
      ->notName('sass-lint.yml')
      ->notName('.scss-lint.yml')
      ->notName('scss-lint.yml')
      ->notName('*.css.map');

    if ($withImportable) {
      $finder
        ->name('/^_.+\.scss$/')
        ->name('/^_.+\.sass$/');
    }

    return $this;
  }

  protected function configFinderJavaScript(Finder $finder): static {
    $finder
      ->name('*.js')
      ->notPath('node_modules')
      ->notName('.npmignore')
      ->notName('*.js.map')
      ->notName('npm-debug.log')
      ->notName('npm-shrinkwrap.json')
      ->notName('package.json')
      ->notName('yarn.lock')
      ->notName('yarn-error.log')
      ->notName('.nvmrc')
      ->notName('.eslintignore')
      ->notName('.eslintrc.json')
      ->notName('bower.json')
      ->notName('.bowerrc')
      ->notName('Gruntfile.js')
      ->notName('gulpfile.js')
      ->notName('.istanbul.yml');

    return $this;
  }

  protected function configFinderTypeScript(Finder $finder): static {
    $finder
      ->name('*.td.ts')
      ->notPath('typings')
      ->notName('typings.json')
      ->notName('tsconfig.json')
      ->notName('tsd.json')
      ->notName('tslint.json');

    return $this;
  }

  protected function configFinderImages(Finder $finder): static {
    $finder
      ->name('*.png')
      ->name('*.jpeg')
      ->name('*.jpg')
      ->name('*.svg')
      ->name('*.ttf')
      ->name('*.ico');

    return $this;
  }

  protected function configFinderFont(Finder $finder): static {
    $finder
      ->name('*.otf')
      ->name('*.woff')
      ->name('*.woff2')
      ->name('*.eot');

    return $this;
  }

  protected function configFinderRuby(Finder $finder): static {
    $finder
      ->notPath('.bundle')
      ->notName('.ruby-version')
      ->notName('.ruby-gemset')
      ->notName('.rvmrc')
      ->notName('Gemfile')
      ->notName('Gemfile.lock');

    return $this;
  }

  protected function configFinderDocker(Finder $finder): static {
    $finder
      ->notName('Dockerfile')
      ->notName('docker-compose.yml')
      ->notName('.dockerignore');

    return $this;
  }

  protected function configFinderCi(Finder $finder): static {
    $finder
      ->notName('Jenkinsfile')
      ->notPath('.gitlab')
      ->notName('.gitlab-ci.yml')
      ->notPath('.github')
      ->notName('.travis.yml')
      ->notPath('.circle')
      ->notName('circle.yml');

    return $this;
  }

  protected function configFinderOs(Finder $finder): static {
    $finder
      ->notName('.directory')
      ->notName('.directory.lock.*.test')
      ->notName('.DS_Store')
      ->notName('._*');

    return $this;
  }

  protected function configFinderIde(Finder $finder): static {
    $finder
      ->notPath('.idea')
      ->notPath('.phpstorm.meta.php')
      ->notName('.phpstorm.meta.php')
      ->notName('*___jb_old___')
      ->notPath('.kdev4')
      ->notName('*.kdev4')
      ->notName('.kdev*')
      ->notName('cifs*')
      ->notName('*~')
      ->notName('.*.kate-swp')
      ->notName('.kateconfig')
      ->notName('.kateproject')
      ->notPath('.kateproject.d')
      ->notName('*.loalize')
      ->notPath('nbproject')
      ->notPath('.settings')
      ->notName('.buildpath')
      ->notName('.project')
      ->notName('.*.swp')
      ->notName('.phing_targets')
      ->notName('nohup.out')
      ->notName('.~lock.*');

    return $this;
  }

}
