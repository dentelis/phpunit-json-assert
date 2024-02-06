<?php

namespace Dentelis\PhpUnitJsonAssert;

use Dentelis\StructureValidator\Exception\ValidationException;
use Dentelis\StructureValidator\TypeInterface;
use PHPUnit\Framework\Assert;
use Throwable;


/**
 * The trait to be used in tests to run Json structure validations
 * @package Dentelis\PhpUnitJsonAssert
 */
trait JsonAssertions
{
    /**
     * Asserts that provided json exactly matches required structure
     * String will be json_decoded
     * @param mixed $jsonDocument
     * @param TypeInterface $type
     * @param string $message
     * @return void
     */
    public static function assertJsonStructure(
        mixed         $jsonDocument,
        TypeInterface $type,
        string        $message = 'Failed asserting that json match provided structure'
    ): void
    {
        Assert::assertTrue($type->validate(
            is_string($jsonDocument) ? json_decode($jsonDocument) : $jsonDocument
        ), $message);
    }

    /**
     * Asserts that provided json do not match required structure
     * String will be json_decoded
     * @param mixed $jsonDocument
     * @param TypeInterface $type
     * @param string $message
     * @return void
     */
    public static function assertJsonDoesNotMatchStructure(
        mixed         $jsonDocument,
        TypeInterface $type,
        string        $message = 'Failed asserting that json does not match provided structure'
    ): void
    {
        try {
            $result = $type->validate(
                is_string($jsonDocument) ? json_decode($jsonDocument) : $jsonDocument
            );
            Assert::assertFalse($result, $message);
        } catch (Throwable $e) {
            Assert::assertInstanceOf(ValidationException::class, $e, $message);
        }
    }


}