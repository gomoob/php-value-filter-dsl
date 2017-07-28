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
 * Test class for the {@link StarTokenizer} class.
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 * @group StarTokenizerTest
 */
class StarTokenizerTest extends TestCase {

    /**
     * Test method for {@link StarTokenizer#tokenize(String)}.
     *
     * @group StarTokenizerTest.testTokenize
     */
    public function testTokenize()
    {
        $tokenizer = new StarTokenizer();

        // Test with a string without any stars
        $tokens = $tokenizer->tokenize('this is a test string');

        $this->assertCount(1, $tokens);
        $this->assertSame('this is a test string', $tokens[0]->getSequence());

        // Test with a simple string having only one star at the begining
        $tokens = $tokenizer->tokenize('*word');

        $this->assertCount(2, $tokens);
        $this->assertSame('*', $tokens[0]->getSequence());
        $this->assertSame('word', $tokens[1]->getSequence());

        // Test with a simple string having only one star at the end
        $tokens = $tokenizer->tokenize("word*");

        $this->assertCount(2, $tokens);
        $this->assertSame('word', $tokens[0]->getSequence());
        $this->assertSame('*', $tokens[1]->getSequence());

        // Test with a simple string having only one star at the middle
        $tokens = $tokenizer->tokenize("word1*word2");

        $this->assertCount(3, $tokens);
        $this->assertSame('word1', $tokens[0]->getSequence());
        $this->assertSame('*', $tokens[1]->getSequence());
        $this->assertSame('word2', $tokens[2]->getSequence());

        // Test with a simple string having several stars
        $tokens = $tokenizer->tokenize("*word1 *word2*");

        $this->assertCount(5, $tokens);
        $this->assertSame('*', $tokens[0]->getSequence());
        $this->assertSame('word1 ', $tokens[1]->getSequence());
        $this->assertSame('*', $tokens[2]->getSequence());
        $this->assertSame('word2', $tokens[3]->getSequence());
        $this->assertSame('*', $tokens[4]->getSequence());
    }
}
