<?php

/**
 * @file
 * Get a location from an image that was taken with a GPS enabled camera.
 */

if (function_exists('exif_read_data')) {
  $plugin = array(
    'title' => t('Image/exif'),
    'description' => t('Get a location from an image that was taken with a GPS enabled phone or camera'),
    'callback' => 'geocode_exif',
    'field_types' => array('file', 'image'),
    'field_callback' => 'geocode_exif_field',
  );
}

/**
 * Get coordinates.
 *
 * @param string $file_path
 *   Image file path.
 *
 * @return bool|Point
 *   Coordinates.
 */
function geocode_exif($file_path) {
  geocode_load_geophp();

  if ($data = exif_read_data($file_path)) {
    if (!isset($data['GPSLatitudeRef'])) {
      return FALSE;
    }

    $lat = geocode_exif_from($data['GPSLatitudeRef'], $data['GPSLatitude']);
    $lon = geocode_exif_from($data['GPSLongitudeRef'], $data['GPSLongitude']);
    $point = new Point($lon, $lat);

    return $point;
  }
  else {
    return FALSE;
  }
}

/**
 * Get GPS coordinates from an uploaded file.
 *
 * @param array $field
 *   Field info array.
 * @param array $field_item
 *   Field item array.
 *
 * @return bool|null|Point
 *   GPS coordinate.
 */
function geocode_exif_field($field, $field_item) {
  if ($field['type'] == 'file' || $field['type'] == 'image') {
    $file = file_load($field_item['fid']);
    return geocode_exif($file->uri);
  }

  return NULL;
}

function geocode_exif_from($dir, $data) {
  foreach ($data as $k => $item) {
    list($deg, $pct) = explode('/', $item);
    if ($pct) {
      $data[$k] = $deg / $pct;
    }
  }

  $point = (float) $data[0] + ($data[1] / 60) + ($data[2] / 3600);
  if (in_array($dir, array('S', 'W'))) {
    $point = $point * -1;
  }

  return $point;
}
