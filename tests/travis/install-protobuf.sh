#!/bin/sh
set -e

wget https://github.com/google/protobuf/releases/download/v$PROTOBUF_VERSION/protobuf-$PROTOBUF_VERSION.tar.gz

tar xf protobuf-$PROTOBUF_VERSION.tar.gz

cd protobuf-$PROTOBUF_VERSION && ./configure --prefix=$HOME/protobuf && make && make install

export PATH=$PATH:$HOME/protobuf/bin/
