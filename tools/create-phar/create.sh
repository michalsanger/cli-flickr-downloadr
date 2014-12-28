#!/bin/bash

# Install Box2 and put it in the path
# https://github.com/box-project/box2

# Set current hash as build name into config
BUILD=`git rev-parse HEAD`
sed -i.bak "s/build: null/build: \"$BUILD\"/g" ../../src/config.neon
rm ../../src/config.neon.bak

# Build phar file
box build

# Revert changes in config
git checkout -- ../../src/config.neon