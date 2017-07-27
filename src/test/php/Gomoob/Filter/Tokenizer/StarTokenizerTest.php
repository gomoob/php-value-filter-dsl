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

/**
 * Test class for the {@link StarTokenizer} class.
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 */
class StarTokenizerTest {

    /**
     * Test method for {@link StarTokenizer#tokenize(String)}.
     */
    @Test
    public void testTokenize()
    {
        ITokenizer tokenizer = new StarTokenizer();

        // Test with a string without any stars
        List<IToken> tokens = tokenizer.tokenize("this is a test string");

        assertThat(tokens.size()).isEqualTo(1);
        assertThat(tokens.get(0).getSequence()).isEqualTo("this is a test string");

        // Test with a simple string having only one star at the begining
        tokens = tokenizer.tokenize("*word");

        assertThat(tokens.size()).isEqualTo(2);
        assertThat(tokens.get(0).getSequence()).isEqualTo("*");
        assertThat(tokens.get(1).getSequence()).isEqualTo("word");

        // Test with a simple string having only one star at the end
        tokens = tokenizer.tokenize("word*");

        assertThat(tokens.size()).isEqualTo(2);
        assertThat(tokens.get(0).getSequence()).isEqualTo("word");
        assertThat(tokens.get(1).getSequence()).isEqualTo("*");

        // Test with a simple string having only one star at the middle
        tokens = tokenizer.tokenize("word1*word2");

        assertThat(tokens.size()).isEqualTo(3);
        assertThat(tokens.get(0).getSequence()).isEqualTo("word1");
        assertThat(tokens.get(1).getSequence()).isEqualTo("*");
        assertThat(tokens.get(2).getSequence()).isEqualTo("word2");

        // Test with a simple string having several stars
        tokens = tokenizer.tokenize("*word1 *word2*");

        assertThat(tokens.size()).isEqualTo(5);
        assertThat(tokens.get(0).getSequence()).isEqualTo("*");
        assertThat(tokens.get(1).getSequence()).isEqualTo("word1 ");
        assertThat(tokens.get(2).getSequence()).isEqualTo("*");
        assertThat(tokens.get(3).getSequence()).isEqualTo("word2");
        assertThat(tokens.get(4).getSequence()).isEqualTo("*");
    }
}
