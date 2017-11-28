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
 * Test case for the `\Gomoob\Filter\Tokenizer\LogicOperatorTokenizer` class.
 *
 * @author Jiaming LIANG (jiaming.liang@gomoob.com)
 * @group LogicOperatorTokenizerTest
 */
class LogicOperatorTokenizerTest extends TestCase
{

    /**
     * An instance of the logic operator tokenizer to test.
     *
     * @var \Gomoob\Filter\Tokenizer\LogicOperatorTokenizer
     */
    private $tokenizer;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->tokenizer = new LogicOperatorTokenizer();
    }

    /**
     * Test with a complex '+' and '-' operators.
     *
     * @group LogicOperatorTokenizerTest.testTokenizeComplexAndOr
     */
    public function testTokenizeComplexAndOr()
    {

        // Test with a simple integer '+'
        $tokens = $this->tokenizer->tokenize('>=10+<50');

        $this->assertCount(3, $tokens);
        $this->assertSame('>=10', $tokens[0]->getSequence());
        $this->assertSame('+', $tokens[1]->getSequence());
        $this->assertSame('<50', $tokens[2]->getSequence());

        // Test with a simple float '-'
        $tokens = $this->tokenizer->tokenize('>=10.1-<50.2');

        $this->assertCount(3, $tokens);
        $this->assertSame('>=10.1', $tokens[0]->getSequence());
        $this->assertSame('-', $tokens[1]->getSequence());
        $this->assertSame('<50.2', $tokens[2]->getSequence());

        // Test with complex '+' '-'
        $tokens = $this->tokenizer->tokenize('>=10.1+<50.2-=60.3');

        $this->assertCount(5, $tokens);
        $this->assertSame('>=10.1', $tokens[0]->getSequence());
        $this->assertSame('+', $tokens[1]->getSequence());
        $this->assertSame('<50.2', $tokens[2]->getSequence());
        $this->assertSame('-', $tokens[3]->getSequence());
        $this->assertSame('=60.3', $tokens[4]->getSequence());
    }
}
