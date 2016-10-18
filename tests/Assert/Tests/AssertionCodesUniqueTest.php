<?php
namespace Assert\Tests;

use acgrid\Assert\Assertion;
use acgrid\Assert\AssertionFailedException;

class AssertionCodesUniqueTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertionCodesAreUnique()
    {
        $assertReflection = new \ReflectionClass(AssertionFailedException::class);
        $constants        = $assertReflection->getConstants();

        Assertion::eq(count($constants), count(array_unique($constants)));
    }
}
