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

namespace Assert\Tests;

use acgrid\Assert\AssertionFailedException;

class AssertionChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_chains_assertions()
    {
        \acgrid\Assert\that(10)->notEmpty()->integer();
    }

    /**
     * @test
     */
    public function it_shifts_arguments_to_assertions_by_one()
    {
        \acgrid\Assert\that(10)->eq(10);
    }

    /**
     * @test
     */
    public function it_knowns_default_error_message()
    {
        $this->setExpectedException(AssertionFailedException::class, 'Not Null and such');

        \acgrid\Assert\that(null, 'Not Null and such')->notEmpty();
    }

    /**
     * @test
     */
    public function it_skips_assertions_on_valid_null()
    {
        \acgrid\Assert\that(null)->nullOr()->integer()->eq(10);
    }

    /**
     * @test
     */
    public function it_validates_all_inputs()
    {
        \acgrid\Assert\that(array(1, 2, 3))->all()->integer();
    }

    /**
     * @test
     */
    public function it_has_thatall_shortcut()
    {
        \acgrid\Assert\thatAll(array(1, 2, 3))->integer();
    }

    /**
     * @test
     */
    public function it_has_nullor_shortcut()
    {
        \acgrid\Assert\thatNullOr(null)->integer()->eq(10);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Assertion 'unknownAssertion' does not exist.
     * @test
     */
    public function it_throws_exception_for_unknown_assertion()
    {
        \acgrid\Assert\that(null)->unknownAssertion();
    }

    /**
     * @test
     */
    public function it_has_satisfy_shortcut()
    {
        \acgrid\Assert\that(null)->satisfy(function ($value) {
            return is_null($value);
        });
    }
}
