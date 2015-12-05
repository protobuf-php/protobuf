<?php

namespace ProtobufTest;

use Protobuf\Field;

class FieldTest extends TestCase
{
    public function phpTypeProvider()
    {
        return [
            [-1,                    null],

            [Field::TYPE_BOOL,      'bool'],

            [Field::TYPE_DOUBLE,    'float'],
            [Field::TYPE_FLOAT,     'float'],

            [Field::TYPE_STRING,    'string'],
            [Field::TYPE_BYTES,     '\Protobuf\Stream'],

            [Field::TYPE_INT64,     'int'],
            [Field::TYPE_UINT64,    'int'],
            [Field::TYPE_INT32,     'int'],
            [Field::TYPE_FIXED64,   'int'],
            [Field::TYPE_FIXED32,   'int'],
            [Field::TYPE_UINT32,    'int'],
            [Field::TYPE_SFIXED32,  'int'],
            [Field::TYPE_SFIXED64,  'int'],
            [Field::TYPE_SINT32,    'int'],
            [Field::TYPE_SINT64,    'int'],

        ];
    }

    /**
     * @dataProvider phpTypeProvider
     */
    public function testGetPhpType($type, $expected)
    {
        $this->assertEquals($expected, Field::getPhpType($type));
    }

    public function labelNameProvider()
    {
        return [
            [-1,                    null],
            [Field::LABEL_OPTIONAL, 'optional'],
            [Field::LABEL_REQUIRED, 'required'],
            [Field::LABEL_REPEATED, 'repeated']
        ];
    }

    /**
     * @dataProvider labelNameProvider
     */
    public function testGetLabelName($type, $expected)
    {
        $this->assertEquals($expected, Field::getLabelName($type));
    }

    public function typeNameProvider()
    {
        return [
            [-1,                    null],
            [Field::TYPE_DOUBLE,    'double'],
            [Field::TYPE_FLOAT,     'float'],
            [Field::TYPE_INT64,     'int64'],
            [Field::TYPE_UINT64,    'uint64'],
            [Field::TYPE_INT32,     'int32'],
            [Field::TYPE_FIXED64,   'fixed64'],
            [Field::TYPE_FIXED32,   'fixed32'],
            [Field::TYPE_BOOL,      'bool'],
            [Field::TYPE_STRING,    'string'],
            [Field::TYPE_MESSAGE,   'message'],
            [Field::TYPE_BYTES,     'bytes'],
            [Field::TYPE_UINT32,    'uint32'],
            [Field::TYPE_ENUM,      'enum'],
            [Field::TYPE_SFIXED32,  'sfixed32'],
            [Field::TYPE_SFIXED64,  'sfixed64'],
            [Field::TYPE_SINT32,    'sint32'],
            [Field::TYPE_SINT64,    'sint64'],
        ];
    }

    /**
     * @dataProvider typeNameProvider
     */
    public function testGetTypeName($type, $expected)
    {
        $this->assertEquals($expected, Field::getTypeName($type));
    }
}
