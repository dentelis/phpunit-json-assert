<?php
declare(strict_types=1);

namespace tests\JsonAssertions;

use Dentelis\PhpUnitJsonAssert\JsonAssertions;
use Dentelis\StructureValidator\Type\ArrayType;
use Dentelis\StructureValidator\Type\IntegerType;
use Dentelis\StructureValidator\Type\ObjectType;
use Dentelis\StructureValidator\Type\StringType;
use Dentelis\StructureValidator\TypeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[
    CoversClass(JsonAssertions::class),
]
final class JsonTest extends TestCase
{
    use JsonAssertions;

    public static function successProvider(): array
    {
        return [
            [
                'foo',
                (new StringType())
            ],
            [
                1,
                (new IntegerType())
            ],
            [
                '[]',
                (new ArrayType())->assertEmpty()
            ],
            [
                '{"name":"user","email":"user@example.com"}',
                (new ObjectType())
                    ->addProperty('name', (new StringType())->assertNotEmpty())
                    ->addProperty('email', (new StringType())->assertEmail())
            ],
            [
                '{"name":"user","email":"user@example.com", "new":true}',
                (new ObjectType())
                    ->setExtensible()
                    ->addProperty('name', (new StringType())->assertNotEmpty())
                    ->addProperty('email', (new StringType())->assertEmail())
            ],

            [
                json_decode('{"name":"user","email":"user@example.com"}'),
                (new ObjectType())
                    ->addProperty('name', (new StringType())->assertNotEmpty())
                    ->addProperty('email', (new StringType())->assertEmail())
            ],
            [
                '[{"name":"user","email":"user@example.com"},{"name":"user","email":"user@example.com"}]',
                (new ArrayType())
                    ->assertNotEmpty()
                    ->assertType((new ObjectType())
                        ->addProperty('name', (new StringType())->assertNotEmpty())
                        ->addProperty('email', (new StringType())->assertEmail())
                    )
            ],
            [
                json_decode('[{"name":"user","email":"user@example.com"},{"name":"user","email":"user@example.com"}]'),
                (new ArrayType())
                    ->assertNotEmpty()
                    ->assertType((new ObjectType())
                        ->addProperty('name', (new StringType())->assertNotEmpty())
                        ->addProperty('email', (new StringType())->assertEmail())
                    )
            ]
        ];
    }

    public static function failProvider(): array
    {
        return [
            [
                'foo',
                (new IntegerType())
            ],
            [
                1,
                (new StringType())
            ],
            [
                '[]',
                (new ArrayType())->assertNotEmpty()
            ],
            [
                '{"name":"user","email":"lorem ipsum"}',
                (new ObjectType())
                    ->addProperty('name', (new StringType())->assertNotEmpty())
                    ->addProperty('email', (new StringType())->assertEmail())
            ],
            [
                '{"name":"user","email":"user@example.com","new":true}',
                (new ObjectType())
                    //->setExtensible()
                    ->addProperty('name', (new StringType())->assertNotEmpty())
                    ->addProperty('email', (new StringType())->assertEmail())
            ],
            [
                '{"name":"user"}',
                (new ObjectType())
                    ->addProperty('name', (new StringType())->assertNotEmpty())
                    ->addProperty('email', (new StringType())->assertEmail())
            ],

            [
                json_decode('{"name":"user","email":"lorem ipsum"}'),
                (new ObjectType())
                    ->addProperty('name', (new StringType())->assertNotEmpty())
                    ->addProperty('email', (new StringType())->assertEmail())
            ],
            [
                json_decode('{"name":"user","email":"user@example.com","new":true}'),
                (new ObjectType())
                    //->setExtensible()
                    ->addProperty('name', (new StringType())->assertNotEmpty())
                    ->addProperty('email', (new StringType())->assertEmail())
            ],
            [
                json_decode('{"name":"user"}'),
                (new ObjectType())
                    ->addProperty('name', (new StringType())->assertNotEmpty())
                    ->addProperty('email', (new StringType())->assertEmail())
            ],

            [
                '[{"name":"user","email":"foo"},{"name":"user","email":"user@example.com"}]',
                (new ArrayType())
                    ->assertNotEmpty()
                    ->assertType((new ObjectType())
                        ->addProperty('name', (new StringType())->assertNotEmpty())
                        ->addProperty('email', (new StringType())->assertEmail())
                    )
            ],
            [
                json_decode('[{"name":"user","email":"user@example.com"},{"name":"user","email":"foo"}]'),
                (new ArrayType())
                    ->assertNotEmpty()
                    ->assertType((new ObjectType())
                        ->addProperty('name', (new StringType())->assertNotEmpty())
                        ->addProperty('email', (new StringType())->assertEmail())
                    )
            ]
        ];

    }

    #[DataProvider('successProvider')]
    public function testSuccess(mixed $value, TypeInterface $type): void
    {
        $this->assertJsonStructure($value, $type);
    }

    #[DataProvider('failProvider')]
    public function testFail(mixed $value, TypeInterface $type): void
    {
        $this->assertJsonDoesNotMatchStructure($value, $type);
    }


}
