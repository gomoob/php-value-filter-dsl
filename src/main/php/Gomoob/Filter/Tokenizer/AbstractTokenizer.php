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

use Gomoob\Filter\TokenizerInterface;
use Gomoob\Filter\TokenInfoInterface;

/**
 * Abstract class common to all tokenizers.
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 */
abstract class AbstractTokenizer implements TokenizerInterface {

    /**
     * List which holds all our token informations.
     *
     * @var array
     */
    private $tokenInfos = [];

    /**
     * Boolean which indicates if matched tokens should always be trimmed. This can be useful to clean matched tokens
     * but this is not always wanted. This is false by default.
     *
     * @var boolean
     */
    protected $trim = false;

    /**
     * {@inheritDoc}
     */
    public function addTokenInfo(/* string */ $regex, /* int */ $tokenCode) {
        // The user can pass a regular expression string and a token code to the method. The method will then the "^"
        // character to the user supplied regular expression. It causes the regular expression to match only the
        // beginning of a string. This is needed because we will be removing any token always looking for the next token
        // at the beginning of the input string.
        $this->tokenInfos->add(new TokenInfo('^' + $regex, tokenCode));
    }

    /**
     * {@inheritDoc}
     */
    public function tokenize(/* string */ $string) /* : array */ {
        $tokens = [];

        // First we clean our string
        $s = new String($string);

        // Boolean variable used to indicate if a token is matched in the string to analyze, this is used to detect
        // unexpected tokens
        $match = false;

        // While their are tokens to extract / analyze
        while ($s !== '') {
            foreach ($this->tokenInfos as $info) {
                Matcher matcher = info.getRegex().matcher(s);

                // If a known token has been encountered
                if (matcher.find()) {
                    // A token has been found
                    $match = true;

                    // The sequence of characters which forms the detected token
                    $sequence = matcher.group();

                    if ($this->trim) {
                        $sequence = trim($sequence);
                    }

                    // Creates our token
                    $token = new Token();
                    $token->setTokenCode($info->getTokenCode());
                    $token->setSequence($sequence);

                    // Add our token to detected tokens
                    $tokens[] = $token;

                    $s = matcher.replaceFirst("");
                    break;
                }
            }

            // The provided string is invalid
            if (!$match) {
                throw new TokenizerException("Unexpected character in input : " + $s);
            }

            // Reinitialize match for next loop
            $match = false;
        }

        return $tokens;
    }
}
