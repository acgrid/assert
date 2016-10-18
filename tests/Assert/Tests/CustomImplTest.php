<?php


namespace Assert\Tests;

use acgrid\Assert\Assertion;
use acgrid\Assert\AssertionChain;
use acgrid\Assert\InvalidArgumentException;
use acgrid\Assert\LazyAssertion;
use acgrid\Assert\LazyAssertionException;
use PHPUnit\Framework\TestCase;

class MyAssertion extends Assertion {
    protected static $exceptionClass = MyException::class;
}

class MyChain extends AssertionChain {
    protected static $assertClass = MyAssertion::class;
}

class MyException extends InvalidArgumentException {}

class MyLazyAssertion extends LazyAssertion  {
    protected static $exceptionClass = MyLazyException::class;
}

class MyLazyException extends LazyAssertionException {}

class CustomImplTest extends TestCase
{

    protected function setUp()
    {
        \acgrid\Assert\chainClass(MyChain::class);
        \acgrid\Assert\lazyClass(MyLazyAssertion::class);
    }

    protected function tearDown()
    {
        \acgrid\Assert\chainClass(AssertionChain::class);
        \acgrid\Assert\lazyClass(LazyAssertion::class);
    }

    public function testMyAssertion()
    {
        try{
            MyAssertion::string(1, 'Not a string');
            $this->fail('Should catch an exception.');
        }catch(MyException $e){

        }
    }

    public function testMyChain()
    {
        $this->assertInstanceOf(MyChain::class, \acgrid\Assert\that('x'));
        try{
            \acgrid\Assert\that(41)->integerish()->lessThan(32);
            $this->fail('Should catch an exception.');
        }catch(MyException $e){

        }
    }


    public function testLazy()
    {
        $this->assertInstanceOf(MyLazyAssertion::class, $lazy = \acgrid\Assert\lazy());
        try{
            $lazy->that('x', 'a')->alnum()->betweenLength(1, 3)->that(31, 'b')->string()->verifyNow();
        }catch(MyLazyException $e){

        }
    }
}