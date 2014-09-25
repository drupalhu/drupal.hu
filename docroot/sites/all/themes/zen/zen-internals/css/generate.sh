#!/bin/sh

# This script is used by the MAINTAINER to make copies of the stylesheets for
# the base Zen theme from the stylesheets in the STARTERKIT.

rm *.css;
for FILENAME in ../../STARTERKIT/css/*.css; do
  cp $FILENAME .;
done
rm layouts/*.css;
for FILENAME in ../../STARTERKIT/css/layouts/*.css; do
  cp $FILENAME layouts;
done

# Don't need the core reference.
rm drupal7-reference.css;

rm ../images/*;
for FILENAME in ../../STARTERKIT/images/*; do
  cp $FILENAME ../images/;
done
