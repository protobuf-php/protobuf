#!/bin/sh
set -e

if [ -d "$HOME/protobuf/lib" ]; then
  echo 'Using cached instalation.';
  exit 0;
fi

wget https://github.com/google/protobuf/releases/download/v$PROTOBUF_VERSION/protobuf-$PROTOBUF_VERSION.tar.gz

tar xf protobuf-$PROTOBUF_VERSION.tar.gz

cd protobuf-$PROTOBUF_VERSION && ./configure --prefix=$HOME/protobuf && make && make install

