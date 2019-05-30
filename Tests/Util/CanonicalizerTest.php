<?php

namespace API\UserBundle\Tests\Util;

use API\UserBundle\Util\Canonicalizer;
use PHPUnit\Framework\TestCase;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
class CanonicalizerTest extends TestCase
{
    /**
     * @dataProvider canonicalizeProvider
     *
     * @param $source
     * @param $expectedResult
     */
    public function testCanonicalize($source, $expectedResult): void
    {
        $canonicalizer = new Canonicalizer();
        $this->assertSame($expectedResult, $canonicalizer->canonicalize($source));
    }

    public function canonicalizeProvider(): \Generator
    {
        yield [null, null];
        yield ['FOO', 'foo'];
        yield [chr(171), '?'];
    }
}
