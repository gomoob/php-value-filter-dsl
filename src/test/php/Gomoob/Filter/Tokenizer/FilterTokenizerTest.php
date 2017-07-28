<?php

/**
 * BSD 3-Clause License
 *
 * Copyright (c) 2017, GOMOOB All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this list of conditions and the following
 * disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following
 * disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * * Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote
 * products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
namespace Gomoob\Filter\Tokenizer;

use PHPUnit\Framework\TestCase;

/**
 * Test case for the `\Gomoob\Filter\Tokenizer\FilterTokenizer` class.
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 * @group FilterTokenizerTest
 */
class FilterTokenizerTest extends TestCase
{

    /**
     * An instance of the filter tokenizer to test.
     *
     * @var \Gomoob\Filter\Tokenizer\FilterTokenizer
     */
    private $tokenizer;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->tokenizer = new FilterTokenizer();
    }

    /**
     * Test with a complex '!in' and '(' and ')' operators.
     *
     * @group FilterTokenizerTest.testTokenizeComplexNotIn
     */
    public function testTokenizeComplexNotIn()
    {

        // Test with a simple integer
        $tokens = $this->tokenizer->tokenize('!in(5,7,9)');

        $this->assertCount(9, $tokens);
        $this->assertSame('!', $tokens[0]->getSequence());
        $this->assertSame('in', $tokens[1]->getSequence());
        $this->assertSame('(', $tokens[2]->getSequence());
        $this->assertSame('5', $tokens[3]->getSequence());
        $this->assertSame(',', $tokens[4]->getSequence());
        $this->assertSame('7', $tokens[5]->getSequence());
        $this->assertSame(',', $tokens[6]->getSequence());
        $this->assertSame('9', $tokens[7]->getSequence());
        $this->assertSame(')', $tokens[8]->getSequence());

        // Test with a simple float
        $tokens = $this->tokenizer->tokenize('!in(5.23,2.96,1.47)');

        $this->assertCount(9, $tokens);
        $this->assertSame('!', $tokens[0]->getSequence());
        $this->assertSame('in', $tokens[1]->getSequence());
        $this->assertSame('(', $tokens[2]->getSequence());
        $this->assertSame('5.23', $tokens[3]->getSequence());
        $this->assertSame(',', $tokens[4]->getSequence());
        $this->assertSame('2.96', $tokens[5]->getSequence());
        $this->assertSame(',', $tokens[6]->getSequence());
        $this->assertSame('1.47', $tokens[7]->getSequence());
        $this->assertSame(')', $tokens[8]->getSequence());

        // Test with a simple string
        $tokens = $this->tokenizer->tokenize("!in('string1','string 2','string_3')");

        $this->assertCount(9, $tokens);
        $this->assertSame('!', $tokens[0]->getSequence());
        $this->assertSame('in', $tokens[1]->getSequence());
        $this->assertSame('(', $tokens[2]->getSequence());
        $this->assertSame('\'string1\'', $tokens[3]->getSequence());
        $this->assertSame(',', $tokens[4]->getSequence());
        $this->assertSame('\'string 2\'', $tokens[5]->getSequence());
        $this->assertSame(',', $tokens[6]->getSequence());
        $this->assertSame('\'string_3\'', $tokens[7]->getSequence());
        $this->assertSame(')', $tokens[8]->getSequence());
    }

    /**
     * Test with a simple '=' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleEqual
     */
    public function testTokenizeSimpleEqual()
    {

        // Test with a simple integer
        $tokens = $this->tokenizer->tokenize('=5');

        $this->assertCount(2, $tokens);
        $this->assertSame('=', $tokens[0]->getSequence());
        $this->assertSame('5', $tokens[1]->getSequence());

        // Test with a simple integer and a star (this must always be quoted)
        $tokens = $this->tokenizer->tokenize('=\'*5\'');

        $this->assertCount(2, $tokens);
        $this->assertSame('=', $tokens[0]->getSequence());
        $this->assertSame('\'*5\'', $tokens[1]->getSequence());

        $tokens = $this->tokenizer->tokenize('=\'5*\'');

        $this->assertCount(2, $tokens);
        $this->assertSame('=', $tokens[0]->getSequence());
        $this->assertSame('\'5*\'', $tokens[1]->getSequence());

        // Test with a simple float
        $tokens = $this->tokenizer->tokenize('=14.69');

        $this->assertCount(2, $tokens);
        $this->assertSame('=', $tokens[0]->getSequence());
        $this->assertSame('14.69', $tokens[1]->getSequence());

        // Test with a simple string and stars (this must always be quoted)
        $tokens = $this->tokenizer->tokenize("='*word1 *word2*'");

        $this->assertCount(2, $tokens);
        $this->assertSame('=', $tokens[0]->getSequence());
        $this->assertSame('\'*word1 *word2*\'', $tokens[1]->getSequence());
    }

    /**
     * Test with a simple 'in' and '(' and ')' operators.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleIn
     */
    public function testTokenizeSimpleIn()
    {

        // Test with a simple integer
        $tokens = $this->tokenizer->tokenize('in(5,7,9)');

        $this->assertCount(8, $tokens);
        $this->assertSame('in', $tokens[0]->getSequence());
        $this->assertSame('(', $tokens[1]->getSequence());
        $this->assertSame('5', $tokens[2]->getSequence());
        $this->assertSame(',', $tokens[3]->getSequence());
        $this->assertSame('7', $tokens[4]->getSequence());
        $this->assertSame(',', $tokens[5]->getSequence());
        $this->assertSame('9', $tokens[6]->getSequence());
        $this->assertSame(')', $tokens[7]->getSequence());

        // Test with a simple float
        $tokens = $this->tokenizer->tokenize("in(5.23,2.96,1.47)");

        $this->assertCount(8, $tokens);
        $this->assertSame('in', $tokens[0]->getSequence());
        $this->assertSame('(', $tokens[1]->getSequence());
        $this->assertSame('5.23', $tokens[2]->getSequence());
        $this->assertSame(',', $tokens[3]->getSequence());
        $this->assertSame('2.96', $tokens[4]->getSequence());
        $this->assertSame(',', $tokens[5]->getSequence());
        $this->assertSame('1.47', $tokens[6]->getSequence());
        $this->assertSame(')', $tokens[7]->getSequence());

        // Test with a simple string
        $tokens = $this->tokenizer->tokenize("in('string1','string 2','string_3')");

        $this->assertCount(8, $tokens);
        $this->assertSame('in', $tokens[0]->getSequence());
        $this->assertSame('(', $tokens[1]->getSequence());
        $this->assertSame('\'string1\'', $tokens[2]->getSequence());
        $this->assertSame(',', $tokens[3]->getSequence());
        $this->assertSame('\'string 2\'', $tokens[4]->getSequence());
        $this->assertSame(',', $tokens[5]->getSequence());
        $this->assertSame('\'string_3\'', $tokens[6]->getSequence());
        $this->assertSame(')', $tokens[7]->getSequence());
    }

    /**
     * Test with a simple '<=' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleLessThanOrEqual
     */
    public function testTokenizeSimpleLessThanOrEqual()
    {

        // Test with a simple integer
        $tokens = $this->tokenizer->tokenize('<=5');

        $this->assertCount(2, $tokens);
        $this->assertSame('<=', $tokens[0]->getSequence());
        $this->assertSame('5', $tokens[1]->getSequence());

        // Test with a simple float
        $tokens = $this->tokenizer->tokenize("<=14.69");

        $this->assertCount(2, $tokens);
        $this->assertSame('<=', $tokens[0]->getSequence());
        $this->assertSame('14.69', $tokens[1]->getSequence());
    }

    /**
     * Test with a simple '<' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleLess
     */
    public function testTokenizeSimpleLess()
    {

        // Test with a simple integer
        $tokens = $this->tokenizer->tokenize("<5");

        $this->assertCount(2, $tokens);
        $this->assertSame('<', $tokens[0]->getSequence());
        $this->assertSame('5', $tokens[1]->getSequence());

        // Test with a simple float
        $tokens = $this->tokenizer->tokenize("<14.69");

        $this->assertCount(2, $tokens);
        $this->assertSame('<', $tokens[0]->getSequence());
        $this->assertSame('14.69', $tokens[1]->getSequence());
    }

    /**
     * Test with a simple '>' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleGreater
     */
    public function testTokenizeSimpleGreater()
    {

        // Test with a simple integer
        $tokens = $this->tokenizer->tokenize(">5");

        $this->assertCount(2, $tokens);
        $this->assertSame('>', $tokens[0]->getSequence());
        $this->assertSame('5', $tokens[1]->getSequence());

        // Test with a simple float
        $tokens = $this->tokenizer->tokenize(">14.69");

        $this->assertCount(2, $tokens);
        $this->assertSame('>', $tokens[0]->getSequence());
        $this->assertSame('14.69', $tokens[1]->getSequence());
    }

    /**
     * Test with a simple '>=' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleGreaterThanOrEqual
     */
    public function testTokenizeSimpleGreaterThanOrEqual()
    {

        // Test with a simple integer
        $tokens = $this->tokenizer->tokenize(">=5");

        $this->assertCount(2, $tokens);
        $this->assertSame('>=', $tokens[0]->getSequence());
        $this->assertSame('5', $tokens[1]->getSequence());

        // Test with a simple float
        $tokens = $this->tokenizer->tokenize(">=14.69");

        $this->assertCount(2, $tokens);
        $this->assertSame('>=', $tokens[0]->getSequence());
        $this->assertSame('14.69', $tokens[1]->getSequence());
    }

    /**
     * Test with a simple '~' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleLike
     */
    public function testTokenizeSimpleLike()
    {

        // Test with a simple string
        $tokens = $this->tokenizer->tokenize("~'Nantes'");

        $this->assertCount(2, $tokens);
        $this->assertSame('~', $tokens[0]->getSequence());
        $this->assertSame('\'Nantes\'', $tokens[1]->getSequence());
    }

    /**
     * Test with a simple '!' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleNot
     */
    public function testTokenizeSimpleNot()
    {

        // Test with a simple integer
        $tokens = $this->tokenizer->tokenize("!5");

        $this->assertCount(2, $tokens);
        $this->assertSame('!', $tokens[0]->getSequence());
        $this->assertSame('5', $tokens[1]->getSequence());


        // Test with a simple float
        $tokens = $this->tokenizer->tokenize("!14.69");

        $this->assertCount(2, $tokens);
        $this->assertSame('!', $tokens[0]->getSequence());
        $this->assertSame('14.69', $tokens[1]->getSequence());

        // Test with a simple string
        $tokens = $this->tokenizer->tokenize("!'This is a test'");

        $this->assertCount(2, $tokens);
        $this->assertSame('!', $tokens[0]->getSequence());
        $this->assertSame('\'This is a test\'', $tokens[1]->getSequence());
    }
}
