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
 * Token specific to the Gomoob filters.
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 */
class FilterToken extends Token {

    /**
     * Token code associated to the ')' terminal.
     */
    const CLOSE_BRAKET = 1;

    /**
     * Token code associated to the ',' terminal.
     */
    const COMMA = 2;

    /**
     * Token code associated to the '=' terminal.
     */
    const EQUAL_TO = 3;

    /**
     * Token code associated to the '>' terminal.
     */
    const GREATER_THAN = 4;

    /**
     * Token code associated to the '>=' terminal.
     */
    const GREATER_THAN_OR_EQUAL = 5;

    /**
     * Token code associated to the 'in' terminal.
     */
    const IN = 6;

    /**
     * Token code associated to the '<' terminal.
     */
    const LESS_THAN = 7;

    /**
     * Token code associated to the '<=' terminal.
     */
    const LESS_THAN_OR_EQUAL = 8;

    /**
     * Token code associated to the '~' terminal.
     */
    const LIKE = 9;

    /**
     * Token code associated to the '!' terminal.
     */
    const NOT = 10;

    /**
     * Token code associated to an integer or float number terminal.
     */
    const NUMBER = 11;

    /**
     * Token code associated to the '(' terminal.
     */
    const OPEN_BRAKET = 12;

    /**
     * Token code associated to a string terminal.
     */
    const STRING = 13;
}
