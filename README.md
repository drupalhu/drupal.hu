
# Drupal.hu

[![CircleCI](https://circleci.com/gh/drupalhu/drupal.hu/tree/2.x.svg?style=svg)](https://circleci.com/gh/drupalhu/drupal.hu/?branch=2.x)


A [Drupal.hu] weboldal kódbázisa.


## Bekapcsolódás a fejlesztésbe - DDev

Bekapcsolódás a fejlesztésbe [DDev local] használatával.


### Bekapcsolódás a fejlesztésbe - DDev - előfeltételek

* [Git] `git --version` >= 2.25.0
* [Docker] `docker --version` >= 20.10.3
* [Docker Compose] `docker-compose --version` >= 1.26.2
* [DDev local] `ddev version` >= v1.17.0
* [yq] `yq --version` >= 4.0.0
* [jq] `jq --version` >= 1.5.0


### Bekapcsolódás a fejlesztésbe - DDev - lépések

Egy terminál ablakban az alábbi parancsok futtatása:
1. `git clone --origin 'upstream' --branch '2.x' https://github.com/drupalhu/drupal.hu.git`
2. `cd drupal.hu`
3. `ddev auth ssh`
4. Linuxon NFS nélkül: `./.ddev/commands/host/generate-ddev-config-local.bash` \
   Linuxon egyébként is az első `ddev start` futtatásakor beállítja,
   hogy NFS nélkül menjen, de az automata detektálás már túl későn
   történik, így csak a második `ddev start` működne rendesen.
5. Ha a host gépen a standard port-ok valamelyike (80 443 stb) DDev-től függetlenül foglalt: \
   Futtatandó parancs: \
   `./.ddev/commands/host/config-port-offset.bash 5000;` \
   Ellenőrizendő fájlok:
   1. .ddev/.env
   2. .ddev/config.local.yaml
6. `ddev start`
7. A terminál kimenet végén ott van az URL ahol elérhető a weboldal.


### Bekapcsolódás a fejlesztésbe - DDev - testreszabás - alapok

A legtöbb esetben az alapértelmezett értékek megfelelőek, ezért nincs
szükség testreszabásra. \
Azonban ha a host gépen DDev-től függetlenül futnak olyan szolgáltatások,
amik olyan port számokat használnak, amiket a DDev is szeretne – például
80(http), 443(https), 8025(mailhog) stb – akkor az érintett konténereket
nem tudja elindítani. \
Ilyenkor a `./.ddev/config.local.yaml` fájlban, illetve a `./.ddev/.env`
fájlban kell a megfelelő értékeket beállítani.

Például `./.ddev/config.local.yaml`:
```yaml
mailhog_port: 5025
mailhog_https_port: 5026
```

[DDev .ddev/config.yaml options]


### Bekapcsolódás a fejlesztésbe - DDev - testreszabás - extra

Extra szolgáltatások – például Solr, Redis vagy Memcache – nem képezik
az alap DDev részét, így nem a `./.ddev/config.*yaml` fájlokban kell
konfigurálni őket. \
Ezek az extra szolgáltatások külön „Docker Compose” fájlokban vannak
definiálva, azért a „Docker” és a „Docker Compose” által biztosított
lehetőségekkel lehet megoldani a konfigurációt.
[Environment variables in Docker Compose]

Ismeretlen okból kifolyólag a `./.ddev/.env` fájl, és a
`./.ddev/docker-compose.*.yaml#/services/*/env_file` konfiguráció nem
működött. További hiba keresést igényel. \
Kerülő megoldásként az alábbi parancs futtatása javasolt minden terminál
ablakban egyszer.
```bash
export $(sed --expression '/^#/d' --expression '/^$/d' ./.ddev/.env | xargs);
```

A testreszabható környezeti változók listázására az alábbi parancs használható:
```bash
grep \
  --only-matching \
  --line-number \
  --perl-regexp '\$\{APP_.+?\}' \
  ./.ddev/docker-compose.*.yaml \
  ./.ddev/*/Dockerfile
```

Példa a `./.ddev/.env` fájl tartalmára:
```bash
APP_SOLR_EXPOSE_HTTP_PORT=5983
APP_SOLR_EXPOSE_HTTPS_PORT=5984
```


---


## Bekapcsolódás a fejlesztésbe - Local

A szükséges szoftverek (PHP, NVM, HTTP, MySQL, Solr) telepítését és
konfigurációját kézzel kell megoldani.
Ez a dokumentáció jelenleg nem add útmutatást a szükséges szoftverek telepítéséhez.


### Bekapcsolódás a fejlesztésbe - Local - előfeltételek

* [Git] `git --version` >= 2.25.0
* [PHP] `php --version` >= `./composer.json#/require/php`
* [Composer] `composer --version` >= 2.x
* [NVM] `nvm --version` >= 0.37
  * `node --version` >= `./.nvmrc`
* MySQL 8.x kompatibilis adatbázis szerver ([MySQL], [MariaDB], [Percona])
* HTTP szerver ([Apache HTTP], [Nginx])
* [Apache Solr] 8.x
* [yq] `yq --version` >= 4.0.0
* [jq] `jq --version` >= 1.5.0


### Bekapcsolódás a fejlesztésbe - Local - lépések

@todo Marvin parancsok jelenleg nem elérhetőek, ezért az 5. 9. és 10. lépés nem fog menni.

1. `git clone https://github.com/drupalhu/drupal.hu.git`
2. `cd drupal.hu`
3. `composer install`
4. `alias d='bin/drush --config=drush @app.local'`
5. `d marvin:onboarding`
6. `"${EDITOR:-vi}" docroot/sites/default/settings.local.php`
   * `$databases` ellenőrzése.
   * `$config['search_api.server.general']['backend_config']['connector_config']` ellenőrzése.
7. `"${EDITOR:-vi}" drush/drush.local.yml`
   * `commands.options.uri` ellenőrzése.
8. `"${EDITOR:-vi}" behat/behat.local.yml`
   * `default.extensions.Drupal\MinkExtension.base_url` ellenőrzése.
9. `d marvin:build`
10. `composer run site:install:prod:default`


[Apache HTTP]: https://httpd.apache.org
[Apache Solr]: https://solr.apache.org
[Composer]: https://getcomposer.org
[DDev .ddev/config.yaml options]: https://ddev.readthedocs.io/en/stable/users/extend/config_yaml
[DDev local]: https://www.ddev.com/ddev-local
[Docker]: https://www.docker.com
[Docker Compose]: https://docs.docker.com/compose
[Environment variables in Docker Compose]: https://docs.docker.com/compose/environment-variables
[Drupal.hu]: https://drupal.hu
[Git]: https://git-scm.com
[jq]: https://stedolan.github.io/jq
[MariaDB]: https://mariadb.org/
[MySQL]: https://www.mysql.com
[Nginx]: http://nginx.org
[NVM]: https://github.com/nvm-sh/nvm
[Percona]: https://www.percona.com/software/mysql-database/percona-server
[PHP]: https://www.php.net
[yq]: https://github.com/mikefarah/yq
