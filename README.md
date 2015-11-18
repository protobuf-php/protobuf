Protobuf for PHP
================

Protobuf for PHP is an implementation of Google's Protocol Buffers for the PHP
language, supporting its binary data serialization and including a `protoc`
plugin to generate PHP classes from .proto files.


**NOTICE: THIS CLIENT IS UNDER ACTIVE DEVELOPMENT, USE AT YOUR OWN RISK**


## Installation

Run the following `composer` commands:

```console
$ composer require "protobuf-php/protobuf"
$ composer require "protobuf-php/protobuf-plugin" --dev
```


## Overview

## Example usage


#### addressbook.proto

```
package tutorial;

message Person {
  required string name = 1;
  required int32 id = 2;
  optional string email = 3;

  enum PhoneType {
    MOBILE = 0;
    HOME = 1;
    WORK = 2;
  }

  message PhoneNumber {
    required string number = 1;
    optional PhoneType type = 2 [default = HOME];
  }

  repeated PhoneNumber phone = 4;
}

message AddressBook {
  repeated Person person = 1;
}

```


#### Generate PHP classes

```console
$ ./vendor/bin/protobuf -o /path-to/src /path-to/addressbook.proto
```


#### Serialize message

```php
use Tutorial\Person;
use Tutorial\AddressBook;
use Tutorial\Person\PhoneType;
use Tutorial\Person\PhoneNumber;

$phone  = new PhoneNumber();
$book   = new AddressBook();
$person = new Person();

$person->setId(11);
$person->setName('Fabio B. Silva');
$person->setEmail('fabio.bat.silva@gmail.com');

$phone->setNumber('1231231212');
$phone->setType(PhoneType::MOBILE());

$book->addPerson($person);
$person->addPhone($phone);

$stream  = $book->toStream();
$content = (string) $book->toStream();

file_put_contents('addressbook.bin', $content);

```

#### Unserialize message

```php
use Tutorial\AddressBook;

$handle = fopen('addressbook.bin', 'r');
$book   = AddressBook::fromStream($handle);

echo $book->getPersonList()[0]->getName();
// Fabio B. Silva

```

