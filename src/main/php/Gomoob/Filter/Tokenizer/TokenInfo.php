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

use Gomoob\Filter\TokenInfoInterface;

class TokenInfo implements TokenInfoInterface {

    /**
     * The regular expression that is used to match the input string against the token.
     *
     * @var string
     */
    private $regex;

    /**
     * The technical code of the token.
     *
     * Please note that in the whole grammar each kind of token should have its technical code.
     *
     * @var int
     */
    private $tokenCode;

    /**
     * Creates a new instance of the token info class.
     *
     * @param string $regex the regular expression that is used to match the input string against the token.
     * @param int $tokenCode the technical code of the token.
     *
     * @return \Gomoob\Filter\Tokenizer\TokenInfo the created instance.
     */
    public function __construct(/* string */ $regex, /* int */ $tokenCode) /* TokenInfo */ {
        $this->regex = $regex;
        $this->tokenCode = $tokenCode;
    }

    /**
     * {@inheritDoc}
     */
    public function getRegex() /* : string */ {
        return $this->regex;
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenCode() /* : int */ {
        return $this->tokenCode;
    }

    /**
     * {@inheritDoc}
     */
    public function setRegex(/* string */ $regex) {
        $this->regex = $regex;
    }

    /**
     * {@inheritDoc}
     */
    public function setTokenCode(/* int */ $tokenCode) {
        $this->tokenCode = $tokenCode;
    }
}
