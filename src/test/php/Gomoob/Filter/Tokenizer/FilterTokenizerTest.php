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
class FilterTokenizerTest extends TestCase {

    /**
     * Test with a complex '!in' and '(' and ')' operators.
     *
     * @group FilterTokenizerTest.testTokenizeComplexNotIn
     */
    public function testTokenizeComplexNotIn() {
        $tokenizer = new FilterTokenizer();

        // Test with a simple integer
        $tokens = $tokenizer->tokenize('!in(5,7,9)');

        assertThat($tokens->size()).isEqualTo(9);
        assertThat($tokens->get(0).getSequence()).isEqualTo("!");
        assertThat($tokens->get(1).getSequence()).isEqualTo("in");
        assertThat($tokens->get(2).getSequence()).isEqualTo("(");
        assertThat($tokens->get(3).getSequence()).isEqualTo("5");
        assertThat($tokens->get(4).getSequence()).isEqualTo(",");
        assertThat($tokens->get(5).getSequence()).isEqualTo("7");
        assertThat($tokens->get(6).getSequence()).isEqualTo(",");
        assertThat($tokens->get(7).getSequence()).isEqualTo("9");
        assertThat($tokens->get(8).getSequence()).isEqualTo(")");

        // Test with a simple float
        $tokens = $tokenizer->tokenize("!in(5.23,2.96,1.47)");

        assertThat($tokens->size()).isEqualTo(9);
        assertThat($tokens->get(0).getSequence()).isEqualTo("!");
        assertThat($tokens->get(1).getSequence()).isEqualTo("in");
        assertThat($tokens->get(2).getSequence()).isEqualTo("(");
        assertThat($tokens->get(3).getSequence()).isEqualTo("5.23");
        assertThat($tokens->get(4).getSequence()).isEqualTo(",");
        assertThat($tokens->get(5).getSequence()).isEqualTo("2.96");
        assertThat($tokens->get(6).getSequence()).isEqualTo(",");
        assertThat($tokens->get(7).getSequence()).isEqualTo("1.47");
        assertThat($tokens->get(8).getSequence()).isEqualTo(")");

        // Test with a simple string
        $tokens = $tokenizer->tokenize("!in('string1','string 2','string_3')");

        assertThat($tokens->size()).isEqualTo(9);
        assertThat($tokens->get(0).getSequence()).isEqualTo("!");
        assertThat($tokens->get(1).getSequence()).isEqualTo("in");
        assertThat($tokens->get(2).getSequence()).isEqualTo("(");
        assertThat($tokens->get(3).getSequence()).isEqualTo("'string1'");
        assertThat($tokens->get(4).getSequence()).isEqualTo(",");
        assertThat($tokens->get(5).getSequence()).isEqualTo("'string 2'");
        assertThat($tokens->get(6).getSequence()).isEqualTo(",");
        assertThat($tokens->get(7).getSequence()).isEqualTo("'string_3'");
        assertThat($tokens->get(8).getSequence()).isEqualTo(")");
    }

    /**
     * Test with a simple '=' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleEqual
     */
    public function testTokenizeSimpleEqual() {
        $tokenizer = new FilterTokenizer();

        // Test with a simple integer
        $tokens = $tokenizer->tokenize("=5");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("=");
        assertThat($tokens->get(1).getSequence()).isEqualTo("5");

        // Test with a simple integer and a star (this must always be quoted)
        $tokens = $tokenizer->tokenize("='*5'");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("=");
        assertThat($tokens->get(1).getSequence()).isEqualTo("'*5'");

        $tokens = $tokenizer->tokenize("='5*'");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("=");
        assertThat($tokens->get(1).getSequence()).isEqualTo("'5*'");

        // Test with a simple float
        $tokens = $tokenizer->tokenize("=14.69");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("=");
        assertThat($tokens->get(1).getSequence()).isEqualTo("14.69");

        // Test with a simple string and stars (this must always be quoted)
        $tokens = $tokenizer->tokenize("='*word1 *word2*'");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("=");
        assertThat($tokens->get(1).getSequence()).isEqualTo("'*word1 *word2*'");
    }

    /**
     * Test with a simple 'in' and '(' and ')' operators.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleIn
     */
    public function testTokenizeSimpleIn() {
        $tokenizer = new FilterTokenizer();

        // Test with a simple integer
        $tokens = $tokenizer->tokenize("in(5,7,9)");

        assertThat($tokens->size()).isEqualTo(8);
        assertThat($tokens->get(0).getSequence()).isEqualTo("in");
        assertThat($tokens->get(1).getSequence()).isEqualTo("(");
        assertThat($tokens->get(2).getSequence()).isEqualTo("5");
        assertThat($tokens->get(3).getSequence()).isEqualTo(",");
        assertThat($tokens->get(4).getSequence()).isEqualTo("7");
        assertThat($tokens->get(5).getSequence()).isEqualTo(",");
        assertThat($tokens->get(6).getSequence()).isEqualTo("9");
        assertThat($tokens->get(7).getSequence()).isEqualTo(")");

        // Test with a simple float
        $tokens = $tokenizer->tokenize("in(5.23,2.96,1.47)");

        assertThat($tokens->size()).isEqualTo(8);
        assertThat($tokens->get(0).getSequence()).isEqualTo("in");
        assertThat($tokens->get(1).getSequence()).isEqualTo("(");
        assertThat($tokens->get(2).getSequence()).isEqualTo("5.23");
        assertThat($tokens->get(3).getSequence()).isEqualTo(",");
        assertThat($tokens->get(4).getSequence()).isEqualTo("2.96");
        assertThat($tokens->get(5).getSequence()).isEqualTo(",");
        assertThat($tokens->get(6).getSequence()).isEqualTo("1.47");
        assertThat($tokens->get(7).getSequence()).isEqualTo(")");

        // Test with a simple string
        $tokens = $tokenizer->tokenize("in('string1','string 2','string_3')");

        assertThat($tokens->size()).isEqualTo(8);
        assertThat($tokens->get(0).getSequence()).isEqualTo("in");
        assertThat($tokens->get(1).getSequence()).isEqualTo("(");
        assertThat($tokens->get(2).getSequence()).isEqualTo("'string1'");
        assertThat($tokens->get(3).getSequence()).isEqualTo(",");
        assertThat($tokens->get(4).getSequence()).isEqualTo("'string 2'");
        assertThat($tokens->get(5).getSequence()).isEqualTo(",");
        assertThat($tokens->get(6).getSequence()).isEqualTo("'string_3'");
        assertThat($tokens->get(7).getSequence()).isEqualTo(")");
    }

    /**
     * Test with a simple '<=' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleLessThanOrEqual
     */
    public function testTokenizeSimpleLessThanOrEqual() {
        $tokenizer = new FilterTokenizer();

        // Test with a simple integer
        $tokens = $tokenizer->tokenize("<=5");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("<=");
        assertThat($tokens->get(1).getSequence()).isEqualTo("5");

        // Test with a simple float
        $tokens = $tokenizer->tokenize("<=14.69");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("<=");
        assertThat($tokens->get(1).getSequence()).isEqualTo("14.69");
    }

    /**
     * Test with a simple '<' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleLess
     */
    public function testTokenizeSimpleLess() {
        $tokenizer = new FilterTokenizer();

        // Test with a simple integer
        $tokens = $tokenizer->tokenize("<5");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("<");
        assertThat($tokens->get(1).getSequence()).isEqualTo("5");

        // Test with a simple float
        $tokens = $tokenizer->tokenize("<14.69");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("<");
        assertThat($tokens->get(1).getSequence()).isEqualTo("14.69");
    }

    /**
     * Test with a simple '>' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleGreater
     */
    public function testTokenizeSimpleGreater() {
        $tokenizer = new FilterTokenizer();

        // Test with a simple integer
        $tokens = $tokenizer->tokenize(">5");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo(">");
        assertThat($tokens->get(1).getSequence()).isEqualTo("5");

        // Test with a simple float
        $tokens = $tokenizer->tokenize(">14.69");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo(">");
        assertThat($tokens->get(1).getSequence()).isEqualTo("14.69");
    }

    /**
     * Test with a simple '>=' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleGreaterThanOrEqual
     */
    public function testTokenizeSimpleGreaterThanOrEqual() {
        $tokenizer = new FilterTokenizer();

        // Test with a simple integer
        $tokens = $tokenizer->tokenize(">=5");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo(">=");
        assertThat($tokens->get(1).getSequence()).isEqualTo("5");

        // Test with a simple float
        $tokens = $tokenizer->tokenize(">=14.69");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo(">=");
        assertThat($tokens->get(1).getSequence()).isEqualTo("14.69");
    }

    /**
     * Test with a simple '~' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleLike
     */
    public function testTokenizeSimpleLike() {
        $tokenizer = new FilterTokenizer();

        // Test with a simple string
        $tokens = $tokenizer->tokenize("~'Nantes'");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("~");
        assertThat($tokens->get(1).getSequence()).isEqualTo("'Nantes'");
    }

    /**
     * Test with a simple '!' operator.
     *
     * @group FilterTokenizerTest.testTokenizeSimpleNot
     */
    public function testTokenizeSimpleNot() {
        $tokenizer = new FilterTokenizer();

        // Test with a simple integer
        $tokens = $tokenizer->tokenize("!5");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("!");
        assertThat($tokens->get(1).getSequence()).isEqualTo("5");

        // Test with a simple float
        $tokens = $tokenizer->tokenize("!14.69");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("!");
        assertThat($tokens->get(1).getSequence()).isEqualTo("14.69");

        // Test with a simple string
        $tokens = $tokenizer->tokenize("!'This is a test'");

        assertThat($tokens->size()).isEqualTo(2);
        assertThat($tokens->get(0).getSequence()).isEqualTo("!");
        assertThat($tokens->get(1).getSequence()).isEqualTo("'This is a test'");
    }
}
