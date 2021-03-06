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
 * Custom tokenizer used to tokenize logic operators.
 *
 * @author Jiaming LIANG (jiaming.liang@gomoob.com)
 */
class LogicOperatorTokenizer extends AbstractTokenizer
{
    /**
     * Creates a new instance of the logic operator tokenizer.
     *
     * @return \Gomoob\Filter\Tokenizer\LogicOperatorTokenizer the created instance.
     */
    public function __construct()
    {
        // This allows to clean our matched tokens a little
        $this->trim = true;

        // WARNING, ORDER IS VERY IMPORTANT

        // Logic operator
        $this->addTokenInfo('(\+)', LogicOperatorToken::AND_OPERATOR);
        $this->addTokenInfo('(-)', LogicOperatorToken::OR_OPERATOR);

        // "Raw" values
        $this->addTokenInfo('([0-9.]+)', LogicOperatorToken::NUMBER);
        $this->addTokenInfo('(\'[^\']+\')', LogicOperatorToken::STRING);

        // Values prefixed with Simple operators
        $this->addTokenInfo('(~\'[^\']+\')', LogicOperatorToken::STRING);
        $this->addTokenInfo('(=\'[^\']+\')', LogicOperatorToken::STRING);
        $this->addTokenInfo('(<\'[^\']+\')', LogicOperatorToken::STRING);
        $this->addTokenInfo('(<=\'[^\']+\')', LogicOperatorToken::STRING);
        $this->addTokenInfo('(>\'[^\']+\')', LogicOperatorToken::STRING);
        $this->addTokenInfo('(>=\'[^\']+\')', LogicOperatorToken::STRING);

        // Values prefixed with Not operator
        $this->addTokenInfo('(!\'[^\']+\')', LogicOperatorToken::STRING);

        $this->addTokenInfo('([^\'\+-]+)', LogicOperatorToken::STRING);
    }
}
