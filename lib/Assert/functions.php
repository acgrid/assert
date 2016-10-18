<?php
/**
 * Assert
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace acgrid\Assert;

if (!function_exists(__NAMESPACE__ . '\chainClass')){
    function chainClass($class = null)
    {
        static $className;
        return isset($class) && is_subclass_of($class, AssertionChain::class, true) ?
            ($className = $class) : ($className ?: AssertionChain::class);
    }
}

if (!function_exists(__NAMESPACE__ . '\lazyClass')){
    function lazyClass($class = null)
    {
        static $className;
        return isset($class) && is_subclass_of($class, LazyAssertion::class, true) ?
            ($className = $class) : ($className ?: LazyAssertion::class);
    }
}

if (!function_exists(__NAMESPACE__ . '\that')) {
    /**
     * Start validation on a value, returns {@link AssertionChain}
     *
     * The invocation of this method starts an assertion chain
     * that is happening on the passed value.
     *
     * @example
     *
     *  \Assert\that($value)->notEmpty()->integer();
     *  \Assert\that($value)->nullOr()->string()->startsWith("Foo");
     *
     * The assertion chain can be stateful, that means be careful when you reuse
     * it. You should never pass around the chain.
     *
     * @param mixed  $value
     * @param string $defaultMessage
     * @param string $defaultPropertyPath
     *
     * @return AssertionChain
     */
    function that($value, $defaultMessage = null, $defaultPropertyPath = null)
    {
        $class = chainClass();
        return new $class($value, $defaultMessage, $defaultPropertyPath);
    }
}

if (!function_exists(__NAMESPACE__ . '\thatAll')) {
    /**
     * Start validation on a set of values, returns {@link AssertionChain}
     *
     * @param mixed  $values
     * @param string $defaultMessage
     * @param string $defaultPropertyPath
     *
     * @return AssertionChain
     */
    function thatAll($values, $defaultMessage = null, $defaultPropertyPath = null)
    {
        return that($values, $defaultMessage, $defaultPropertyPath)->all();
    }
}

if (!function_exists(__NAMESPACE__ . '\thatNullOr')) {
    /**
     * Start validation and allow NULL, returns {@link AssertionChain}
     *
     * @param mixed  $value
     * @param string $defaultMessage
     * @param string $defaultPropertyPath
     *
     * @return AssertionChain
     */
    function thatNullOr($value, $defaultMessage = null, $defaultPropertyPath = null)
    {
        return that($value, $defaultMessage, $defaultPropertyPath)->nullOr();
    }
}

if (!function_exists(__NAMESPACE__ . '\lazy')) {
    /**
     * Create a lazy assertion object.
     *
     * @return LazyAssertion
     */
    function lazy()
    {
        $class = lazyClass();
        return new $class;
    }
}
