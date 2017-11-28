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
namespace Gomoob\Filter\Sql;

use Gomoob\Filter\SqlFilterConverterInterface;

use Gomoob\Filter\Converter\ConverterException;

use Gomoob\Filter\Tokenizer\FilterToken;
use Gomoob\Filter\Tokenizer\FilterTokenizer;
use Gomoob\Filter\Tokenizer\TokenizerException;
use Gomoob\Filter\Tokenizer\StarTokenizer;
use Gomoob\Filter\Tokenizer\LogicOperatorTokenizer;
use Gomoob\Filter\Tokenizer\LogicOperatorToken;

/**
 * Class which represents a converter to convert Gomoob query filters into SQL.
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 */
class SqlFilterConverter implements SqlFilterConverterInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($key, /* string */ $value, /* array */ $context = []) /* : array */
    {
        $sqlFilterWithParams = null;

        // If the key is a string then the filter is a simple filter
        if (is_string($key)) {
            $sqlFilterWithParams = $this->transformComplexFilter($key, $value, $context);
        } // If the key is an integer then the filter is a complex filter
        elseif (is_int($key)) {
            throw new ConverterException('Complex filters are currently not implemented !');
        } // Otherwise this is an error
        else {
            throw new ConverterException('Invalid filter key type !');
        }

        return $sqlFilterWithParams;
    }

    /**
     * Utility method used to convert a simple operator token into an equivalent SQL operator.
     *
     * @param token the token to convert.
     *
     * @return string the resulting SQL operator.
     */
    private function convertSimpleOperatorTokenToSqlString(/* TokenInterface */ $token) /* : string */
    {
        $sqlString = '';

        switch ($token->getTokenCode()) {
            case FilterToken::EQUAL_TO:
                $sqlString = '=';
                break;
            case FilterToken::GREATER_THAN:
                $sqlString = '>';
                break;
            case FilterToken::GREATER_THAN_OR_EQUAL:
                $sqlString = '>=';
                break;
            case FilterToken::LESS_THAN:
                $sqlString = '<';
                break;
            case FilterToken::LESS_THAN_OR_EQUAL:
                $sqlString = '<=';
                break;
            case FilterToken::NOT:
                $sqlString = '!';
                break;
            default:
                throw new ConverterException('This function cannot be called with this token !');
        }

        return $sqlString;
    }

    /**
     * Utility function used to remove the single quotes around a STRING token and return the resulting unquoted string.
     *
     * @param token the string from which one to remove the first and last characters.
     *
     * @return string the resulting string.
     */
    private function extractUnquotedString(/* TokenInterface */ $token) /* : string */
    {
        $string = $token->getSequence();
        return substr($string, 1, strlen($string) - 2);
    }

    /**
     * Parse a filter expression from the first encountered token.
     *
     * @param string $key the filter key.
     * @param string $value the filter expression.
     * @param array $tokens the tokens extracted by the filter expression tokenize.
     * @param bool $afterNot boolean used to indicate if the filter to analyse is a sub part of a filter containing a
     *        '!' operator.
     *
     * @return array a key / value pair which maps the resulting SQL filter with its prepared statement parameters.
     */
    private function parseFromFirstToken(
        /* string */ $key,
        /* string */ $value,
        /* array */ $tokens,
        /* boolean */ $afterNot
    ) /* : array */ {
        $sb = '';
        $args = [];

        $firstToken = $tokens[0];
        $secondToken = null;

        switch ($firstToken->getTokenCode()) {
        // The first token is a simple operator
            case FilterToken::EQUAL_TO:
                // If their is not only 2 token then this is an error (this will not be the case when we'll have
                // support for parenthesis)
                if (count($tokens) !== 2) {
                    throw new ConverterException("Invalid filter expression '" . $value . "' !");
                }

                // Now parse the value
                $secondToken = $tokens[1];

                switch ($secondToken->getTokenCode()) {
                    case FilterToken::NUMBER:
                        // If the '=' expression is not after a '!' operator
                        if (!$afterNot) {
                            $sb .= $key;
                            $sb .= ' ';
                        }

                        $sb .= $this->convertSimpleOperatorTokenToSqlString($firstToken);
                        $sb .= ' ?';
                        $args[] = $this->parseNumberToken($secondToken);
                        break;
                    case FilterToken::STRING:
                        // Try to find star tokens to know if the query if for a 'like'
                        $starTokenizer = new StarTokenizer();
                        $starTokens = $starTokenizer->tokenize($this->extractUnquotedString($secondToken));

                        // The SQL instruction to build must contain a 'like'
                        if (count($starTokens) > 1) {
                            $likeString = '';

                            // If the '=' expression is not after a '!' operator
                            if (!$afterNot) {
                                $sb .= $key;
                            } // If the '=' expression is after a '!' operator
                            else {
                                $sb .= 'not';
                            }

                            $sb .= ' like ?';

                            foreach ($starTokens as $starToken) {
                                if ('*' === $starToken->getSequence()) {
                                    $likeString .= '%';
                                } else {
                                    $likeString .= $starToken->getSequence();
                                }
                            }

                            $args[] = $likeString;
                        } // The SQL instruction to construct is a simple equality
                        else {
                            // If the '=' expression is not after a '!' operator
                            if (!$afterNot) {
                                $sb .= $key;
                                $sb .= ' ';
                            }

                            $sb .= $this->convertSimpleOperatorTokenToSqlString($firstToken);
                            $sb .= ' ?';
                            $args[] = $this->extractUnquotedString($secondToken);
                        }

                        break;
                    default:
                        throw new ConverterException("Invalid use of operator !");
                }

                break;

            case FilterToken::GREATER_THAN:
            case FilterToken::GREATER_THAN_OR_EQUAL:
            case FilterToken::LESS_THAN:
            case FilterToken::LESS_THAN_OR_EQUAL:
                // If their is not only 2 token then this is an error (this will not be the case when we'll have
                // support for parenthesis)
                if (count($tokens) !== 2) {
                    throw new ConverterException("Invalid filter expression '" . $value . "' !");
                }

                // Its not possible to apply a '!' operator with the '>', '>=', '<' or '<=', in any cases its a non
                // sense
                if ($afterNot) {
                    throw new ConverterException("Using the '!' operator before the '"
                        . $this->convertSimpleOperatorTokenToSqlString($firstToken) . "' operator is forbidden !");
                }

                // Now parse the value
                $secondToken = $tokens[1];

                switch ($secondToken->getTokenCode()) {
                    case FilterToken::NUMBER:
                        $sb .= $key;
                        $sb .= ' ';
                        $sb .= $this->convertSimpleOperatorTokenToSqlString($firstToken);
                        $sb .= ' ?';

                        $args[] = $this->parseNumberToken($secondToken);

                        break;

                // FIXME: Ceci est fait pour les comparaisons de dates, dans l'idéal il faudrait ici lever une
                // exception très claire si la chaîne de caractères n'est pas dans un format ISO-8601.
                // Attention ici on a également un problème car les formats de date sont spécifiques à la
                // base de données utilisée.
                    case FilterToken::STRING:
                        $sb .= $key;
                        $sb .= ' ';
                        $sb .= $this->convertSimpleOperatorTokenToSqlString($firstToken);
                        $sb .= ' ?';

                        $args[] = $this->extractUnquotedString($secondToken);

                        break;
                    default:
                        throw new ConverterException("Invalid use of operator !");
                }

                break;

            // The first token express a like
            case FilterToken::LIKE:
                // If their is not only 2 token then this is an error (this will not be the case when we'll have
                // support for parenthesis)
                if (count($tokens) !== 2) {
                    throw new ConverterException("Invalid filter expression '" . $value . "' !");
                }

                // Now parse the value
                $secondToken = $tokens[1];

                switch ($secondToken->getTokenCode()) {
                    case FilterToken::NUMBER:
                        $sb .= 'cast(';
                        $sb .= $key;
                        $sb .= ' as varchar(32)) like ?';
                        $args[] = '%' . $secondToken->getSequence() . '%';
                        break;
                    case FilterToken::STRING:
                        // The '~' operator can be combined with a string having '*' symbols
                        $starTokenizer = new StarTokenizer();
                        $starTokens = $starTokenizer->tokenize($this->extractUnquotedString($secondToken));

                        $likeString = '%';
                        $sb .= $key;
                        $sb .= ' like ?';

                        // The string contains '*' symbols
                        if (count($starTokens) > 1) {
                            $i = 0;

                            foreach ($starTokens as $starToken) {
                                if ('*' === $starToken->getSequence()) {
                                    // If the star is not positionned as the first or last token (this is because
                                    // the '~' operator already adds '%' at start and end of target string)
                                    if ($i !== 0 && $i !== count($starTokens) - 1) {
                                        $likeString .= '%';
                                    }
                                } else {
                                    $likeString .= $starToken->getSequence();
                                }

                                $i++;
                            }
                        } // The string does not contain '*' symbols
                        else {
                            $likeString .= $this->extractUnquotedString($secondToken);
                        }

                        $likeString .= '%';
                        $args[] = $likeString;

                        break;
                    default:
                        throw new ConverterException("Invalid use of '~' operator !");
                }

                break;

            // The first token is a '!' operator
            case FilterToken::NOT:
                $secondToken = $tokens[1];
                $sb .= $key;

                // The end of string is not a number or a string
                if (count($tokens) > 2) {
                    // Re-apply the same processing to the string after '!'
                    $endOfFilterConverted = null;

                    // Manage the '!~' cases
                    if ($secondToken->getTokenCode() === FilterToken::LIKE) {
                        throw new ConverterException(
                            "Using the '!' operator before the '~' is currently not supported, please used the '!' "
                            . "operator with a string having '*' characters instead !"
                        );
                    } // Else
                    else {
                        $endOfFilterConverted = $this->parseFromFirstToken(
                            $key,
                            substr($value, 1),
                            array_slice($tokens, 1),
                            true
                        );
                    }

                    $sb .= ' ';

                    // If the "sub-filter" is not a like expression
                    if (strpos($endOfFilterConverted[0], 'not like ') === false &&
                    strpos($endOfFilterConverted[0], 'not in') === false) {
                        $sb .= '!';
                    }

                    $sb .= $endOfFilterConverted[0];

                    foreach ($endOfFilterConverted[1] as $arg) {
                        $args[] = $arg;
                    }
                } // The end of string is a number or a string
                elseif (count($tokens) === 2) {
                    $sb .= ' = ';

                    switch ($secondToken->getTokenCode()) {
                        case FilterToken::NUMBER:
                            $sb .= '!?';
                            $args[] = $this->parseNumberToken($secondToken);
                            break;
                        case FilterToken::STRING:
                            $sb .= '!?';
                            $args[] = $this->extractUnquotedString($secondToken);
                            break;
                        default:
                            throw new ConverterException("Invalid filter expression '" . $value . "' !");
                    }
                } // Otherwise we are on an invalid filter expression
                else {
                    throw new ConverterException("Invalid filter expression '" . $value . "' !");
                }

                break;

            // The first token is an 'in' operator
            case FilterToken::IN:
                // If their is not at least 4 tokens then the 'in' expression is not well formed
                if (count($tokens) < 4) {
                    throw new ConverterException("Invalid filter expression '" . $value . "' !");
                }

                // The second token must be an open bracket
                $secondToken = $tokens[1];
                if ($secondToken->getTokenCode() !== FilterToken::OPEN_BRAKET) {
                    throw new ConverterException("Invalid filter expression '" . $value . "' !");
                }

                // If the 'in' expression is not after a '!' operator
                if (!$afterNot) {
                    $sb .= $key;
                    $sb .= ' ';
                } else {
                    $sb .= 'not ';
                }

                $sb .= 'in(';

                // Loop through the list of values
                $canEncounterComma = false;
                $i = 2;
                $currentToken = $tokens[$i];

                while ($currentToken !== null && $currentToken->getTokenCode() !== FilterToken::CLOSE_BRAKET) {
                    if ($currentToken->getTokenCode() === FilterToken::CLOSE_BRAKET) {
                        break;
                    }

                    // If a ',' is expected and the current token is not a comma this is an error
                    if ($canEncounterComma && $currentToken->getTokenCode() != FilterToken::COMMA) {
                        throw new ConverterException("Invalid filter expression '" . $value . "' !");
                    } // If a ',' is expected and encountered
                    elseif ($canEncounterComma && $currentToken->getTokenCode() === FilterToken::COMMA) {
                        $sb .= ',';
                        ++$i;

                        if (!array_key_exists($i, $tokens)) {
                            throw new ConverterException("Invalid filter expression '" . $value . "' !", $ioobex);
                        }

                        $currentToken = $tokens[$i];
                        $canEncounterComma = false;
                        continue;
                    } // Otherwise
                    else {
                        switch ($currentToken->getTokenCode()) {
                            case FilterToken::NUMBER:
                                $sb .= '?';
                                $args [] = $this->parseNumberToken($currentToken);
                                break;
                            case FilterToken::STRING:
                                $sb .= '?';
                                $args[] = $this->extractUnquotedString($currentToken);
                                break;
                            default:
                                throw new ConverterException("Invalid filter expression '" . $value . "' !");
                        }

                        ++$i;

                        if (!array_key_exists($i, $tokens)) {
                            throw new ConverterException("Invalid filter expression '" . $value . "' !", ioobex);
                        }

                        $currentToken = $tokens[$i];
                        $canEncounterComma = true;
                    }
                }

                $sb .= ')';
                break;

            // The first token is a string
            case FilterToken::STRING:
                throw new ConverterException("Invalid filter expression '" . $value . "' !");

            // The first token is a number
            case FilterToken::NUMBER:
                // If their is more than one tohen then this is an error
                if (count($tokens) !== 1) {
                    throw new ConverterException("Invalid filter expression '" . $value . "' !");
                }

                $sb .= $key;
                $sb .= ' = ?';
                $args[] = $this->parseNumberToken($firstToken);

                break;

            // All other first token are currently not supported
            default:
                break;
        }

        return [$sb, $args];
    }

    /**
     * Utility method used to parse the value of a number token and return an integer instance if the number
     * is an integer and a float instance if the number is a float.
     *
     * @param token the token to parse.
     *
     * @return int | float an int or a float depending on the type of the parsed number.
     */
    private function parseNumberToken(/* TokenInterface */ $token) /* : int | float */
    {
        $parsed = null;

        // If the number is an integer
        if (ctype_digit($token->getSequence())) {
            $parsed = intval($token->getSequence());
        } // If successful the number is a double
        else {
            $parsed = floatval($token->getSequence());
        }

        return $parsed;
    }

    /**
     * Transforms a complex filter into an SQL equivalent instruction.
     *
     * @param key the filter key.
     * @param value the filter value.
     * @param context additional context variable to replace.
     *
     * @return array a key / value pair which maps the resulting SQL filter with its prepared statement parameters.
     */
    private function transformComplexFilter(
        /* string */ $key,
        /* string */ $value,
        /* array */ $context
    ) /* : array */ {
        $result = ['', []];

        try {
            // Creates a tokenizer to tokenize the filter value
            $tokenizer = new LogicOperatorTokenizer();

            // Tokenize the filter
            $tokens = $tokenizer->tokenize($value);

            // If a logical expression is expressed
            if (count($tokens) === 3 &&
                ($tokens[1]->getTokenCode() === LogicOperatorToken::AND ||
                 $tokens[1]->getTokenCode() === LogicOperatorToken::OR)) {
                // Transform the first part of the logical expression
                $sqlFilter1 = $this->transformSimpleFilter($key, $tokens[0]->getSequence(), $context);

                // Transform the second part of the logical expression
                $sqlFilter2 = $this->transformSimpleFilter($key, $tokens[2]->getSequence(), $context);

                // Creates the resulting SQL logical expression
                $result[0] = $sqlFilter1->getExpression();

                if ($tokens[1]->getTokenCode() === LogicOperatorToken::AND) {
                    $result[0] .= ' AND ';
                } elseif ($tokens[1]->getTokenCode() === LogicOperatorToken::OR) {
                    $result[0] .= ' OR ';
                }

                $result[0] .= $sqlFilter2->getExpression();

                // Creates the SQL parameters array
                $result[1] = array_merge($sqlFilter1->getParams(), $sqlFilter2->getParams());
            } else {
                return $this->transformSimpleFilter($key, $value, $context);
            }
        } catch (TokenizerException $tex) {
            // If an exception is encountered at tokenization then we consider the value to be a simple string
            $result = [$key . ' = ?', [$value]];
        }

        return new SqlFilter($result[0], $result[1]);
    }

    /**
     * Transforms a simple filter into an SQL equivalent instruction.
     *
     * @param key the filter key.
     * @param value the filter value.
     * @param context additional context variable to replace.
     *
     * @return array a key / value pair which maps the resulting SQL filter with its prepared statement parameters.
     */
    private function transformSimpleFilter(
        /* string */ $key,
        /* string */ $value,
        /* array */ $context
    ) /* : array */ {
        $result = ['', []];

        try {
            // Creates a tokenizer to tokenize the filter value
            $tokenizer = new FilterTokenizer();

            // Tokenize the filter
            $tokens = $tokenizer->tokenize($value);

            // Now parse the tokens
            if (!empty($tokens)) {
                $result = $this->parseFromFirstToken($key, $value, $tokens, false);
            }
        } catch (TokenizerException $tex) {
            // If an exception is encountered at tokenization then we consider the value to be a simple string
            $result = [$key . ' = ?', [$value]];
        }

        return new SqlFilter($result[0], $result[1]);
    }
}
