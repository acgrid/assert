# Assert

Travis Status: [![Build Status](https://travis-ci.org/acgrid/assert.svg?branch=master)](https://travis-ci.org/acgrid/assert)

**This is a personal forked version. It has been rewritten so that all the components can be overridden in user land.**
**Also, this fork will ONLY work on PHP 5.5 or above due to class name constants.**

A simple php library which contains assertions and guard methods for input validation (not filtering!) in business-model, libraries and application low-level code.
The library can be used to implement pre-/post-conditions on input data.

Idea is to reduce the amount of code for implementing assertions in your model and also simplify the code paths to implement assertions. When assertions fail, an exception is thrown, removing the necessity for if-clauses in your code.

The library is not using Symfony or Zend Validators for a reason: The checks have to be low-level, fast, non-object-oriented code to be used everywhere necessary. Using any of the two libraries requires instantiation of several objects, using a locale component, translations, you name it. Its too much bloat.

## Installation

Using Composer:

```sh
composer require acgrid/assert
```

## Example usages

```php
<?php
use acgrid\Assert\Assertion;

function duplicateFile($file, $times)
{
    Assertion::file($file);
    Assertion::digit($times);

    for ($i = 0; $i < $times; $i++) {
        copy($file, $file . $i);
    }
}
```

Real time usage with [Azure Blob Storage](https://github.com/beberlei/azure-blob-storage/blob/master/lib/Beberlei/AzureBlobStorage/BlobClient.php#L571):

```php
<?php
use acgrid\Assert\Assertion;
class Foo{
public function putBlob($containerName = '', $blobName = '', $localFileName = '', $metadata = array(), $leaseId = null, $additionalHeaders = array())
{
    Assertion::notEmpty($containerName, 'Container name is not specified');
    self::assertValidContainerName($containerName);
    Assertion::notEmpty($blobName, 'Blob name is not specified.');
    Assertion::notEmpty($localFileName, 'Local file name is not specified.');
    Assertion::file($localFileName, 'Local file name is not specified.');
    self::assertValidRootContainerBlobName($containerName, $blobName);

    // Check file size
    if (filesize($localFileName) >= self::MAX_BLOB_SIZE) {
        return $this->putLargeBlob($containerName, $blobName, $localFileName, $metadata, $leaseId, $additionalHeaders);
    }

    // Put the data to Windows Azure Storage
    return $this->putBlobData($containerName, $blobName, file_get_contents($localFileName), $metadata, $leaseId, $additionalHeaders);
}
}
```

### NullOr helper

A helper method (`Assertion::nullOr*`) is provided to check if a value is null OR holds for the assertion:

```php
<?php
use acgrid\Assert\Assertion;

Assertion::nullOrMax(null, 42); // success
Assertion::nullOrMax(1, 42);    // success
Assertion::nullOrMax(1337, 42); // exception
```

### All helper

The `Assertion::all*` method checks if all provided values hold for the
assertion. It will throw an exception of the assertion does not hold for one of
the values:

```php
<?php
use acgrid\Assert\Assertion;
Assertion::allIsInstanceOf(array(new \stdClass, new \stdClass), 'stdClass'); // success
Assertion::allIsInstanceOf(array(new \stdClass, new \stdClass), 'PDO');      // exception
```

### \acgrid\Assert\that() Chaining

Using the static API on values is very verbose when checking values against multiple assertions.
Starting with 2.0 of Assert there is a much nicer fluent API for assertions, starting
with ``\acgrid\Assert\that($value)`` and then receiving the assertions you want to call
on the fluent interface. You only have to specify the `$value` once.

```php
<?php
\acgrid\Assert\that($value)->notEmpty()->integer();
\acgrid\Assert\that($value)->nullOr()->string()->startsWith("Foo");
\acgrid\Assert\that($values)->all()->float();
```

There are also two shortcut function ``\acgrid\Assert\thatNullOr()`` and ``\acgrid\Assert\thatAll()`` enabling
the "nullOr" or "all" helper respectively.

### Lazy Assertions

There are many cases in web development, especially when involving forms, you want to collect several errors
instead of aborting directly on the first error. This is what lazy assertions are for. Their API
works exactly like the fluent ``\acgrid\Assert\that()`` API, but instead of throwing an Exception directly,
they collect all errors and only trigger the exception when the method
``verifyNow()`` is called on the ``acgrid\Assert\SoftAssertion`` object.

```php
<?php
\acgrid\Assert\lazy()
    ->that(10, 'foo')->string()
    ->that(null, 'bar')->notEmpty()
    ->that('string', 'baz')->isArray()
    ->verifyNow();
```

The method ``that($value, $propertyPath)`` requires a property path (name), so that you know how to differentiate
the errors afterwards.

On failure ``verifyNow()`` will throw an exception
``acgrid\Assert\\LazyAssertionException`` with a combined message:

    The following 3 assertions failed:
    1) foo: Value "10" expected to be string, type integer given.
    2) bar: Value "<NULL>" is empty, but non empty value was expected.
    3) baz: Value "string" is not an array.

You can also retrieve all the ``AssertionFailedException``s by calling ``getErrorExceptions()``.
This can be useful for example to build a failure response for the user.

## List of assertions

```php
<?php
use acgrid\Assert\Assertion;

Assertion::alnum($value);
Assertion::between($value, $lowerLimit, $upperLimit);
Assertion::betweenExclusive($value, $lowerLimit, $upperLimit);
Assertion::betweenLength($value, $minLength, $maxLength);
Assertion::boolean($value);
Assertion::choice($value, $choices);
Assertion::choicesNotEmpty($values, $choices);
Assertion::classExists($value);
Assertion::contains($string, $needle);
Assertion::count($countable, $count);
Assertion::date($value, $format);
Assertion::digit($value);
Assertion::directory($value);
Assertion::e164($value);
Assertion::email($value);
Assertion::endsWith($string, $needle);
Assertion::eq($value, $value2);
Assertion::false($value);
Assertion::file($value);
Assertion::float($value);
Assertion::greaterOrEqualThan($value, $limit);
Assertion::greaterThan($value, $limit);
Assertion::implementsInterface($class, $interfaceName);
Assertion::inArray($value, $choices);
Assertion::integer($value);
Assertion::integerish($value);
Assertion::interfaceExists($value);
Assertion::ip($value, $flag = null);
Assertion::ipv4($value, $flag = null);
Assertion::ipv6($value, $flag = null);
Assertion::isArray($value);
Assertion::isArrayAccessible($value);
Assertion::isCallable($value);
Assertion::isInstanceOf($value, $className);
Assertion::isJsonString($value);
Assertion::isObject($value);
Assertion::isTraversable($value);
Assertion::keyExists($value, $key);
Assertion::keyIsset($value, $key);
Assertion::keyNotExists($value, $key);
Assertion::length($value, $length);
Assertion::lessOrEqualThan($value, $limit);
Assertion::lessThan($value, $limit);
Assertion::max($value, $maxValue);
Assertion::maxLength($value, $maxLength);
Assertion::methodExists($value, $object);
Assertion::min($value, $minValue);
Assertion::minLength($value, $minLength);
Assertion::noContent($value);
Assertion::notBlank($value);
Assertion::notEmpty($value);
Assertion::notEmptyKey($value, $key);
Assertion::notEq($value1, $value2);
Assertion::notInArray($value, $choices);
Assertion::notIsInstanceOf($value, $className);
Assertion::notNull($value);
Assertion::notSame($value1, $value2);
Assertion::null($value);
Assertion::numeric($value);
Assertion::range($value, $minValue, $maxValue);
Assertion::readable($value);
Assertion::regex($value, $pattern);
Assertion::same($value, $value2);
Assertion::satisfy($value, $callback);
Assertion::scalar($value);
Assertion::startsWith($string, $needle);
Assertion::string($value);
Assertion::subclassOf($value, $className);
Assertion::true($value);
Assertion::url($value);
Assertion::uuid($value);
Assertion::writeable($value);

```

Remember: When a configuration parameter is necessary, it is always passed AFTER the value. The value is always the first parameter.

## Exception & Error Handling

If any of the assertions fails a `acgrid\Assert\AssertionFailedException` is thrown.
You can pass an argument called ```$message``` to any assertion to control the
exception message. Every exception contains a default message and unique message code
by default.

```php
<?php
use acgrid\Assert\Assertion;
use acgrid\Assert\AssertionFailedException;

try {
    Assertion::integer($value, "The pressure of gas is measured in integers.");
} catch(AssertionFailedException $e) {
    // error handling
    $e->getValue(); // the value that caused the failure
    $e->getConstraints(); // the additional constraints of the assertion.
}
```

``acgrid\Assert\AssertionFailedException`` is just an interface and the default
implementation is ``acgrid\Assert\InvalidArgumentException`` which extends the SPL
``InvalidArgumentException``. You can change the exception being used on a
package based level.

## Your own Assertion class

To shield your library from possible bugs, misinterpretations or BC breaks
inside Assert you should introduce a library/project based assertion subclass,
where you can override the exception thrown as well. In addition, you can
override the ``acgrid\Assert\Assertion::stringify()`` method to provide your own
interpretations of the types during error handling.

```php
<?php
namespace MyProject;

use acgrid\Assert\Assertion as BaseAssertion;
use acgrid\Assert\LazyAssertion;

class Assertion extends BaseAssertion
{
    protected static $exceptionClass = 'MyProject\AssertionFailedException';
}

class MyLazyAssertion extends LazyAssertion 
{
     protected static $exceptionClass = 'MyProject\LazyAssertionFailedException';
}
```

And you can configure which static class will be called for the function helpers:
```php
<?php
// something like bootstrap.php, set the class to be used:
\acgrid\Assert\chainClass(Assert\Tests\MyChain::class);
\acgrid\Assert\lazyClass(Assert\Tests\MyLazyAssertion::class);
// as getter:
var_dump(\acgrid\Assert\chainClass());
var_dump(\acgrid\Assert\lazyClass());
```

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for more details.

