#!/bin/bash

if [ -z "$1" ] 
  then
    echo "Version not supplied"
    exit 1
fi

sed -i.bak "s/version: \".*\"/version: \"$1\"/g" ../../src/config.neon
rm ../../src/config.neon.bak

sed -i.bak "s/download\/.*\/flickr_downloadr\.phar/download\/$1\/flickr_downloadr\.phar/g" ../../README.md
rm ../../README.md.bak

git commit -a -m "Bump version $1"
git tag $1
