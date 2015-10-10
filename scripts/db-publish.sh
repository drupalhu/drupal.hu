#!/bin/sh -e
##
## Pre-requirements:
##   - Download a database dump manually and place it to
##     "backup/prod-druphungary-druphungary-YYYY-MM-DD.sql.gz"
##   - Setup drush aliases in "~/.drush/druphungary.aliases.drushrc.php"
##     Just follow the instructions on the animgif.
##     https://docs.acquia.com/cloud/drush-aliases
##   - Be sure you are using the same Git commit as the environment that the
##     backup came from at that time when the backup was created.
##
## WARNING - Your local database will be overwritten!
##
## Usage: `cd docroot && ../scripts/db-publish.sh`
##


self_dir=$(dirname "$0")

ac_site='druphungary'
ac_env='dev'

# Grab the latest dump from the local "backup" directory.
source_gz_name=$(find ../backup -type f -regextype posix-extended -regex '^.*?/prod-druphungary-druphungary-.*\.sql\.gz$' | sort --reverse | head -1)
if [ -z "$source_gz_name" ]; then
  echo 'There is no any backup.' 1>&2

  exit 1
fi

echo "Selected database dump to cleanup and upload is '$source_gz_name'"

# Parse the "YYYY-MM-DD" part from the file name.
source_gz_name_base=$(basename "$source_gz_name")
if [[ ! "$source_gz_name_base" =~ ^prod-druphungary-druphungary-([0-9-]{10}).sql.gz$ ]]; then
  echo "File name format is invalid: ${source_gz_name_base}" 1>&2

  exit 2
fi

source_date="${BASH_REMATCH[1]}"
if [ ! "$(date -d ${source_date})" ]; then
  exit 3
fi

source_sql_name="${source_gz_name%.*}"
if [ ! -s "${source_sql_name}" ]; then
  echo "Extracting ${source_gz_name}"
  gzip --decompress --keep "${source_gz_name}"

  if [ ! -s "${source_sql_name}" ]; then
    echo 'Source SQL not found.'

    exit 4
  fi
fi

drush sql-drop --database='default'

echo "Import database dump $source_sql_name"
drush --database='default' sql-cli -A < "${source_sql_name}"

drush cc all
drush pm-disable acquia_spi --yes

echo 'Run DB cleanup script: ../hooks/scripts/db-scrub.stage.mysql'
drush --database='default' sql-cli -A < "../hooks/scripts/db-scrub.stage.mysql"

dump_sql_name="../backup/${ac_site}-dev.sql"
dump_sql_base=$(basename "$dump_sql_name")
dump_yml_name="${dump_sql_name}.yml"
dump_gz_name="${dump_sql_name}.gz"

echo "dump_create_date: '${source_date}'" > "${dump_yml_name}"
echo "git_sha: '$(git rev-parse HEAD)'" >> "${dump_yml_name}"

if [ -s "${dump_sql_name}" ]; then
  rm "${dump_sql_name}"
fi

if [ -s "${dump_gz_name}" ]; then
  rm "${dump_gz_name}"
fi

drush --database='default' sql-dump --ordered-dump --result-file="$dump_sql_name" --gzip

ac_alias="@${ac_site}.${ac_env}"
remote_user=$(drush site-alias "${ac_alias}" --field-labels='0' --format='csv' --fields='remote-user')
remote_host=$(drush site-alias "${ac_alias}" --field-labels='0' --format='csv' --fields='remote-host')
remote_root=$(drush site-alias "${ac_alias}" --field-labels='0' --format='csv' --fields='root')

echo "Start copy files."
for extension in 'gz' 'yml'
do
  scp "${dump_sql_name}.${extension}" "${remote_user}@${remote_host}:${remote_root}/files/${dump_sql_base}.${extension}"
done
