#!/usr/bin/env php
<?php

## Description: Generates ~/.my.cnf.
## Usage: web:generate:my-cnf
## Example: "ddev web:generate:my-cnf"


function ini_encode(array $a): string
{
  return array_reduce(array_keys($a), function($str, $sectionName) use ($a) {
    $sub = $a[$sectionName];
    return $str . "[$sectionName]" . PHP_EOL .
      array_reduce(array_keys($sub), function($str, $key) use($sub) {
        return $str . $key . '=' . $sub[$key] . PHP_EOL;
      }) . PHP_EOL;
  });
}

$file_name = getenv('HOME') . '/.my.cnf';
$file_data = file_exists($file_name) ?
  parse_ini_file($file_name, TRUE)
  : [];

$file_data += [
  'client' => [],
  'mysql' => [],
  'mysqldump' => [],
];

$connection = [
  'user' => getenv('MYSQL_USER'),
  'password' => getenv('MYSQL_PASSWORD'),
  'host' => getenv('MYSQL_HOST'),
  'port' => getenv('MYSQL_PORT'),
  'database' => getenv('MYSQL_DATABASE'),
];

$file_data['client'] += [
  'prompt' => "\u@\h:\p/\dMySQL $ ",
];
$file_data['client'] = array_replace_recursive(
  $file_data['client'],
  $connection,
);

$file_data['mysql'] = array_replace_recursive(
  $file_data['mysql'],
  $connection,
);

$file_data['mysqldump'] = array_replace_recursive(
  $file_data['mysql'],
  $connection,
);

file_put_contents(
  $file_name,
  ini_encode($file_data),
);
