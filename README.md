# phpunit-json-assert

PHPUnit data structure validation for JSON documents

## Installation

Use the package manager [composer](https://getcomposer.org/) to install Validator.

```bash
composer require dentelis/phpunit-json-assert
```

## Usage

Add JsonAssertions trait to your test file

```php
<?php
declare(strict_types=1);

use Dentelis\PhpUnitJsonAssert\JsonAssertions;
use Dentelis\StructureValidator\Type\ObjectType;
use Dentelis\StructureValidator\Type\StringType;
use PHPUnit\Framework\TestCase;

final class CustomTest extends TestCase
{
    use JsonAssertions;
    
    public function test(): void
    {
        $this->assertJsonStructure(
            '{"name":"user","email":"user@example.com"}',
             (new ObjectType())
                 ->addProperty('name', (new StringType())->assertNotEmpty())
                 ->addProperty('email', (new StringType())->assertEmail())
        );
    }
}
```