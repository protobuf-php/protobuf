#!/bin/sh
set -e

if [ -z "$PROTOBUF_VERSION" ]; then
  echo 'PROTOBUF_VERSION env var is not defined.';
  exit 1;
fi

if [ -d "$HOME/protobuf/$PROTOBUF_VERSION/lib" ]; then
  echo 'Using cached instalation.';
  exit 0;
fi

case "$PROTOBUF_VERSION" in
2*)
    PROTOBUF_RELEASE_FILE=protobuf-$PROTOBUF_VERSION
    ;;
3*)
    PROTOBUF_RELEASE_FILE=protobuf-cpp-$PROTOBUF_VERSION
    ;;
*)
    echo "Unknown protobuf version: $PROTOBUF_VERSION"
    exit 1;
    ;;
esac

wget https://github.com/google/protobuf/releases/download/v$PROTOBUF_VERSION/$PROTOBUF_RELEASE_FILE.tar.gz

tar xf $PROTOBUF_RELEASE_FILE.tar.gz

cd protobuf-$PROTOBUF_VERSION && ./configure --prefix=$HOME/protobuf/$PROTOBUF_VERSION && make && make install

