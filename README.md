Protobuf for PHP
================

[![Build Status](https://travis-ci.org/protobuf-php/protobuf.svg?branch=master)](https://travis-ci.org/protobuf-php/protobuf)
[![Coverage Status](https://coveralls.io/repos/protobuf-php/protobuf/badge.svg?branch=master&service=github)](https://coveralls.io/github/protobuf-php/protobuf?branch=master)
[![Total Downloads](https://poser.pugx.org/protobuf-php/protobuf/downloads)](https://packagist.org/packages/protobuf-php/protobuf)
[![License](https://poser.pugx.org/protobuf-php/protobuf/license)](https://packagist.org/packages/protobuf-php/protobuf)

Protobuf for PHP is an implementation of Google's Protocol Buffers for the PHP
language, supporting its binary data serialization and including a `protoc`
plugin to generate PHP classes from .proto files.


## Installation

Run the following `composer` commands:

```console
$ composer require "protobuf-php/protobuf"
```


## Overview

This tutorial provides a basic introduction to working with protocol buffers.
By walking through creating a simple example application, it shows you how to

* Define message formats in a ```.proto``` file.
* Use the protocol buffer compiler.
* Use the PHP protocol buffer API to write and read messages.


#### Why Use Protocol Buffers?

The example we're going to use is a very simple "address book" application that can read and write people's contact details to and from a file. Each person in the address book has a name, an ID, an email address, and a contact phone number.

How do you serialize and retrieve structured data like this?
There are a few ways to solve this problem:

* Use PHP Serialization. This is the default approach since it's built into the language, but it is not very space efficient, and also doesn't work very well if you need to share data with applications written in other languages (Nodejs,Java,Python, etc..).

* You can invent an ad-hoc way to encode the data items into a single string – such as encoding 4 ints as "12:3:-23:67". This is a simple and flexible approach, although it does require writing one-off encoding and parsing code, and the parsing imposes a small run-time cost. This works best for encoding very simple data.

* Serialize the data to XML. This approach can be very attractive since XML is (sort of) human readable and there are binding libraries for lots of languages. This can be a good choice if you want to share data with other applications/projects. However, XML is notoriously space intensive, and encoding/decoding it can impose a huge performance penalty on applications. Also, navigating an XML DOM tree is considerably more complicated than navigating simple fields in a class normally would be.


Protocol buffers are the flexible, efficient, automated solution to solve exactly this problem. With protocol buffers, you write a ```.proto``` description of the data structure you wish to store. From that, the protocol buffer compiler creates a class that implements automatic encoding and parsing of the protocol buffer data with an efficient binary format. The generated class provides getters and setters for the fields that make up a protocol buffer and takes care of the details of reading and writing the protocol buffer as a unit. Importantly, the protocol buffer format supports the idea of extending the format over time in such a way that the code can still read data encoded with the old format.


#### Defining Your Protocol Format

To create your address book application, you'll need to start with a ```.proto``` file. The definitions in a ```.proto``` file are simple: you add a message for each data structure you want to serialize, then specify a name and a type for each field in the message. Here is the ```.proto``` file that defines your messages, ```addressbook.proto```.

```proto
package tutorial;
import "php.proto";
option (php.package) = "Tutorial.AddressBookProtos";

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

As you can see, the syntax is similar to C++ or Java. Let's go through each part of the file and see what it does.
The ```.proto``` file starts with a package declaration, which helps to prevent naming conflicts between different projects.
In PHP, the package name is used as the PHP namespace unless you have explicitly specified a ```(php.package)```, as we have here.
Even if you do provide a ```(php.package)```, you should still define a normal package as well to avoid name collisions in the Protocol Buffers name space as well as in non PHP languages.


After the package declaration, you can see two options that are PHP-specific: ```import "php.proto";``` and ```(php.package)```.
* ```import "php.proto"``` add supports a few PHP specific options for proto files.
* ```(php.package)``` specifies in what php namespace name your generated classes should live.
If you don't specify this explicitly, it simply matches the package name given by the package declaration, but these names usually aren't appropriate PHP namespace names.


Next, you have your message definitions. A message is just an aggregate containing a set of typed fields. Many standard simple data types are available as field types, including ```bool```, ```int32```, ```float```, ```double```, and ```string```. You can also add further structure to your messages by using other message types as field types – in the above example the ```Person``` message contains ```PhoneNumber``` messages, while the ```AddressBook``` message contains ```Person``` messages. You can even define message types nested inside other messages – as you can see, the ```PhoneNumber``` type is defined inside ```Person```. You can also define ```enum``` types if you want one of your fields to have one of a predefined list of values – here you want to specify that a phone number can be one of ```MOBILE```, ```HOME```, or ```WORK```.


The ```" = 1"```, ```" = 2"``` markers on each element identify the unique ```tag``` that field uses in the binary encoding. Tag numbers 1-15 require one less byte to encode than higher numbers, so as an optimization you can decide to use those tags for the commonly used or repeated elements, leaving tags 16 and higher for less-commonly used optional elements. Each element in a repeated field requires re-encoding the tag number, so repeated fields are particularly good candidates for this optimization.


Each field must be annotated with one of the following modifiers:

* **required**: a value for the field must be provided, otherwise the message will be considered "uninitialized". Trying to build an uninitialized message will throw a RuntimeException. Parsing an uninitialized message will throw an IOException. Other than this, a required field behaves exactly like an optional field.
* **optional**: the field may or may not be set. If an optional field value isn't set, a default value is used. For simple types, you can specify your own default value, as we've done for the phone number type in the example. Otherwise, a system default is used: zero for numeric types, the empty string for strings, false for bools. For embedded messages, the default value is always the "default instance" or "prototype" of the message, which has none of its fields set. Calling the accessor to get the value of an optional (or required) field which has not been explicitly set always returns that field's default value.
* **repeated**: the field may be repeated any number of times (including zero). The order of the repeated values will be preserved in the protocol buffer. Think of repeated fields as dynamically sized arrays.

You'll find a complete guide to writing .proto files – including all the possible field types – in the [Protocol Buffer Language Guide](https://developers.google.com/protocol-buffers/docs/proto). Don't go looking for facilities similar to class inheritance, though – protocol buffers don't do that.


#### Compiling Your Protocol Buffers

Now that you have a ```.proto```, the next thing you need to do is generate the classes you'll need to read and write ```AddressBook``` (and hence ```Person``` and ```PhoneNumber```) messages. To do this, you need to run the protocol buffer plugin on your .proto:

If you haven't installed the compiler (```protoc```) or you dont have the php plugin, see https://github.com/protobuf-php/protobuf-plugin.

Now run the compiler plugin, specifying the proto files source directory (the file directory is used if you don't provide a value), the destination directory (where you want the generated code to go), and the path to your ```.proto``` In this case:

```console
php ./vendor/bin/protobuf --include-descriptors -i . -o ./src/ ./addressbook.proto
```

This generates the following PHP classes in your specified destination directory

```console
src/
└── Tutorial
    └── AddressBookProtos
        ├── AddressBook.php
        ├── Person
        │   ├── PhoneNumber.php
        │   └── PhoneType.php
        └── Person.php
```

#### The Protocol Buffer API

Let's look at some of the generated code and see what classes and methods the compiler has created for you. If you look in ```src/Tutorial/AddressBookProtos/Person.php``` you can see that it defines a class called ```Person```.

Messages have auto-generated accessor methods for each field of the message.
Here are some of the accessors for the Person class (implementations omitted for brevity):

```php
<?php
###################### required string name = 1; ###################################
/** @return bool */
public function hasName();
/** @return string */
public function getName();
/** @param string $value */
public function setName($value);
####################################################################################


###################### required int32 id = 2; ######################################
/** @return bool */
public function hasId();
/** @return int */
public function getId();
/** @param int $value */
public function setId($value);
####################################################################################


###################### optional string email = 3; ##################################
/** @return bool */
public function hasEmail();
/** @return string */
public function getEmail();
/** @param string $value */
public function setEmail($value);
####################################################################################


###################### repeated .tutorial.Person.PhoneNumber phone = 4; ############
/** @return bool */
public function hasPhoneList();
/** @return \Protobuf\Collection<\ProtobufTest\Protos\Person\PhoneNumber> */
public function getPhoneList();
/** @param \Protobuf\Collection<\ProtobufTest\Protos\Person\PhoneNumber> $value */
public function setPhoneList(\Protobuf\Collection $value);
####################################################################################
?>
```

As you can see, there are simple getters and setters for each field.
There are also has getters for each singular field which return true if that field has been set.
Repeated fields have a extra method, an add method which appends a new element to the list.

Notice how these accessor methods use camel-case naming, even though the ```.proto``` file uses lowercase-with-underscores.
This transformation is done automatically by the protocol buffer compiler so that the generated classes match standard PHP style conventions.
You should always use lowercase-with-underscores for field names in your ```.proto``` files; this ensures good naming practice in all the generated languages. See the style guide for more on good ```.proto``` style.


Protocol Buffers types map to the following PHP types:

| Protocol Buffers | PHP                |
| ---------------- | ------------------ |
| double           | float              |
| float            | float              |
| int32            | int                |
| int64            | int                |
| uint32           | int                |
| uint64           | int                |
| sint32           | int                |
| sint64           | int                |
| fixed32          | int                |
| fixed64          | int                |
| sfixed32         | int                |
| sfixed64         | int                |
| bool             | bool               |
| string           | string             |
| bytes            | \\Protobuf\\Stream |


#### Enums and Nested Classes

The generated code includes a ```PhoneType``` [enum](https://github.com/protobuf-php/protobuf/blob/master/src/Enum.php):

```php
<?php
namespace Tutorial\AddressBookProtos\Person;

class PhoneType extends \Protobuf\Enum
{
    /**
     * @return \Tutorial\AddressBookProtos\Person\PhoneType
     */
    public static function MOBILE() { /** ... */ }

    /**
     * @return \Tutorial\AddressBookProtos\Person\PhoneType
     */
    public static function HOME() { /** ... */ }

    /**
     * @return \Tutorial\AddressBookProtos\Person\PhoneType
     */
    public static function WORK() { /** ... */ }
?>
```

All nested types are generated using the parent class ```Person``` as part of its namespace.

```php
<?php
use Tutorial\AddressBookProtos\Person;

$person = new Person();
$phone  = new Person\PhoneNumber();
$type   = Person\PhoneType::MOBILE();

$person->setId(1);
$person->setName('Fabio B. Silva');
$person->setEmail('fabio.bat.silva@gmail.com');

$phone->setType($type);
$phone->setNumber('1231231212');
?>
```

#### Known issues

- Protobuf stores floating point values using the [IEEE 754](http://en.wikipedia.org/wiki/IEEE_754) standard
  with 64bit words for the `double` and 32bit for the `float` types. PHP supports IEEE 754 natively although
  the precission is platform dependant, however it typically supports 64bit doubles. It means that
  if your PHP was compiled with 64bit sized doubles (or greater) you shouldn't have any problem encoding
  and decoded float and double typed values.

- Integer values are also [platform dependant in PHP](http://www.php.net/manual/en/language.types.integer.php).
  The library has been developed and tested against PHP binaries compiled with 64bit integers. The encoding and
  decoding algorithm should in theory work no matter if PHP uses 32bit or 64bit integers internally, just take
  into account that with 32bit integers the numbers cannot exceed in any case the `PHP_INT_MAX` value (2147483647).

  While Protobuf supports unsigned integers PHP does not. In fact, numbers above the compiled PHP maximum
  integer (`PHP_INT_MAX`, 0x7FFFFFFFFFFFFFFF for 64bits) will be automatically casted to doubles, which
  typically will offer 53bits of decimal precission, allowing to safely work with numbers upto
  0x20000000000000 (2^53), even if they are represented in PHP as floats instead of integers. Higher numbers
  will loose precission or might even return an _infinity_ value, note that the library does not include
  any checking for these numbers and using them might provoke unexpected behaviour.

  Negative values when encoded as `int32`, `int64` or `fixed64` types require the big integer extensions
  [GMP](http://www.php.net/gmp) or [BC Math](http://www.php.net/bc) to be available in your PHP environment.
  The reason is that when encoding these negative numbers without using _zigzag_ the binary representation uses the most significant bit for the sign, thus the numbers become
  above the maximum supported values in PHP. The library will check for these conditions and will automatically
  try to use GMP or BC to process the value.


#### Parsing and Serialization

Each protocol buffer class has methods for writing and reading messages of your chosen type using the protocol buffer binary format. These include :


```php
<?php

/**
 * Message constructor
 *
 * @param \Protobuf\Stream|resource|string $stream
 * @param \Protobuf\Configuration          $configuration
 */
public function __construct($stream = null, Configuration $configuration = null);

/**
 * Creates message from the given stream.
 *
 * @param \Protobuf\Stream|resource|string $stream
 * @param \Protobuf\Configuration          $configuration
 *
 * @return \Protobuf\Message
 */
public static function fromStream($stream, Configuration $configuration = null);


/**
 * Serializes the message and returns a stream containing its bytes.
 *
 * @param \Protobuf\Configuration $configuration
 *
 * @return \Protobuf\Stream
 */
public function toStream(Configuration $configuration = null);

/**
 * Returns a human-readable representation of the message, particularly useful for debugging.
 *
 * @return string
 */
public function __toString();
?>
```


#### Writing A Message

Now let's try using your protocol buffer classes. The first thing you want your address book application to be able to do is write personal details to your address book file. To do this, you need to create and populate instances of your protocol buffer classes and then write them to an output stream.

Here is a program which reads an ```AddressBook``` from a file, adds one new ```Person``` to it based on user input, and writes the new ```AddressBook``` back out to the file again. The parts which directly call or reference code generated by the protocol compiler are highlighted.


```php
#!/usr/bin/env php
<?php

use Tutorial\AddressBookProtos\Person;
use Tutorial\AddressBookProtos\AddressBook;

// Read the existing address book or create a new one.
$addressBook = is_file($argv[1])
    ? new AddressBook(file_get_contents($argv[1]))
    : new AddressBook();

$person = new Person();
$id     = intval(readline("Enter person ID: "));
$name   = trim(readline("Enter person name: "));
$email  = trim(readline("Enter email address (blank for none): "));

$person->setId($id);
$person->setName($name);

if ( ! empty($email)) {
    $person->setEmail($email);
}

while (true) {
    $number = trim(readline("Enter a phone number (or leave blank to finish):"));

    if (empty($number)) {
        break;
    }

    $phone  = new Person\PhoneNumber();
    $type   = trim(readline("Is this a mobile, home, or work phone? "));

    switch (strtolower($type)) {
        case 'mobile':
            $phone->setType(Person\PhoneType::MOBILE());
            break;
        case 'work':
            $phone->setType(Person\PhoneType::WORK());
            break;
        case 'home':
            $phone->setType(Person\PhoneType::HOME());
            break;
        default:
            echo "Unknown phone type. Using default." . PHP_EOL;
    }

    $phone->setNumber($number);
    $person->addPhone($phone);
}

// Add a person.
$addressBook->addPerson($person);

// Print current address book
echo $addressBook;

// Write the new address book back to disk.
file_put_contents($argv[1], $addressBook->toStream());
?>
```

This tutorial documentation its based on the [Protocol Buffer Basics Tutorial](https://developers.google.com/protocol-buffers/docs/javatutorial).
