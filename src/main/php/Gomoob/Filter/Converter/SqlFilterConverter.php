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
namespace Gomoob\Filter\Converter;

use Gomoob\Filter\SqlFilterConverterInterface;
use Gomoob\Filter\TokenInterface;

/**
 * Class which represents a converter to convert Gomoob query filters into SQL.
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 */
class SqlFilterConverter implements SqlFilterConverterInterface {

    /**
     * {@inheritDoc}
     */
    public function transform($key, /* string */ $value) /* : array */ {
        return $this->transform(key, value, new HashMap<String, Object>());
    }

    /**
     * {@inheritDoc}
     */
    public function transform($key, /* string */ $value, /* array */ $context) /* : array */ {
        $sqlFilterWithParams = null;

        // If the key is a string then the filter is a simple filter
        if (key instanceof String) {
            $sqlFilterWithParams = $this->transformSimpleFilter((String) key, value, context);
        }

        // If the key is an integer then the filter is a complex filter
        else if (key instanceof Integer) {
            throw new ConverterException('Complex filters are currently not implemented !');
        }

        // Otherwise this is an error
        else {
            throw new ConverterException('Invalid filter key class !');
        }

        return sqlFilterWithParams;
    }

    /**
     * Utility method used to convert a simple operator token into an equivalent SQL operator.
     *
     * @param token the token to convert.
     *
     * @return string the resulting SQL operator.
     */
    private function convertSimpleOperatorTokenToSqlString( /* TokenInterface */ $token) /* : string */ {
        $sqlString = '';

        switch ($token->getTokenCode()) {
        case FilterToken.EQUAL_TO:
            $sqlString = '=';
            break;
        case FilterToken.GREATER_THAN:
            $sqlString = '>';
            break;
        case FilterToken.GREATER_THAN_OR_EQUAL:
            $sqlString = '>=';
            break;
        case FilterToken.LESS_THAN:
            $sqlString = '<';
            break;
        case FilterToken.LESS_THAN_OR_EQUAL:
            $sqlString = '<=';
            break;
        case FilterToken.NOT:
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
    private function extractUnquotedString(/* TokenInterface */ $token) /* : string */ {
        $string = $token->getSequence();

        return $string.substring(1, $string.length() - 1);
    }

    /**
     * Parse a filter expression from the first encountered token.
     *
     * @param key the filter key.
     * @param value the filter expression.
     * @param tokens the tokens extracted by the filter expression tokenize.
     * @param afterNot boolean used to indicate if the filter to analyse is a sub part of a filter containing a '!'
     *            operator.
     *
     * @return a key / value pair which maps the resulting SQL filter with its prepared statement parameters.
     */
    private Map.Entry<String, List<? extends Serializable>> parseFromFirstToken(final String key, final String value,
            final List<IToken> tokens, final boolean afterNot) {
        StringBuilder sb = new StringBuilder();
        List<Serializable> args = new ArrayList<>();

        IToken firstToken = tokens.get(0);
        IToken secondToken = null;

        switch (firstToken.getTokenCode()) {
        // The first token is a simple operator
        case FilterToken.EQUAL_TO:

            // If their is not only 2 token then this is an error (this will not be the case when we'll have
            // support for parenthesis)
            if (tokens.size() != 2) {
                throw new ConverterException("Invalid filter expression '" + value + "' !");
            }

            // Now parse the value
            secondToken = tokens.get(1);

            switch (secondToken.getTokenCode()) {
            case FilterToken.NUMBER:

                // If the '=' expression is not after a '!' operator
                if (!afterNot) {
                    sb.append(key);
                    sb.append(' ');
                }

                sb.append(this.convertSimpleOperatorTokenToSqlString(firstToken));
                sb.append(" ?");
                args.add(this.parseNumberToken(secondToken));
                break;
            case FilterToken.STRING:

                // Try to find star tokens to know if the query if for a 'like'
                ITokenizer starTokenizer = new StarTokenizer();
                List<IToken> starTokens = starTokenizer.tokenize(this.extractUnquotedString(secondToken));

                // The SQL instruction to build must contain a 'like'
                if (starTokens.size() > 1) {
                    String likeString = "";

                    // If the '=' expression is not after a '!' operator
                    if (!afterNot) {
                        sb.append(key);
                    }

                    // If the '=' expression is after a '!' operator
                    else {
                        sb.append("not");
                    }

                    sb.append(" like ?");

                    for (IToken starToken : starTokens) {
                        if ("*".equals(starToken.getSequence())) {
                            likeString += "%";
                        } else {
                            likeString += starToken.getSequence();
                        }
                    }

                    args.add(likeString);
                }

                // The SQL instruction to construct is a simple equality
                else {

                    // If the '=' expression is not after a '!' operator
                    if (!afterNot) {
                        sb.append(key);
                        sb.append(' ');
                    }

                    sb.append(this.convertSimpleOperatorTokenToSqlString(firstToken));
                    sb.append(" ?");
                    args.add(this.extractUnquotedString(secondToken));
                }

                break;
            default:
                throw new ConverterException("Invalid use of operator !");
            }

            break;

        case FilterToken.GREATER_THAN:
        case FilterToken.GREATER_THAN_OR_EQUAL:
        case FilterToken.LESS_THAN:
        case FilterToken.LESS_THAN_OR_EQUAL:

            // If their is not only 2 token then this is an error (this will not be the case when we'll have
            // support for parenthesis)
            if (tokens.size() != 2) {
                throw new ConverterException("Invalid filter expression '" + value + "' !");
            }

            // Its not possible to apply a '!' operator with the '>', '>=', '<' or '<=', in any cases its a non
            // sense
            if (afterNot) {
                throw new ConverterException("Using the '!' operator before the '"
                        + this.convertSimpleOperatorTokenToSqlString(firstToken) + "' operator is forbidden !");
            }

            // Now parse the value
            secondToken = tokens.get(1);

            switch (secondToken.getTokenCode()) {
            case FilterToken.NUMBER:
                sb.append(key);
                sb.append(" ");
                sb.append(this.convertSimpleOperatorTokenToSqlString(firstToken));
                sb.append(" ?");

                args.add(this.parseNumberToken(secondToken));

                break;

            // FIXME: Ceci est fait pour les comparaisons de dates, dans l'idéal il faudrait ici lever une
            // exception très claire si la chaîne de caractères n'est pas dans un format ISO-8601.
            // Attention ici on a également un problème car les formats de date sont spécifiques à la
            // base de données utilisée.
            case FilterToken.STRING:
                sb.append(key);
                sb.append(" ");
                sb.append(this.convertSimpleOperatorTokenToSqlString(firstToken));
                sb.append(" ?");

                args.add(this.extractUnquotedString(secondToken));

                break;
            default:
                throw new ConverterException("Invalid use of operator !");
            }

            break;

        // The first token express a like
        case FilterToken.LIKE:

            // If their is not only 2 token then this is an error (this will not be the case when we'll have
            // support for parenthesis)
            if (tokens.size() != 2) {
                throw new ConverterException("Invalid filter expression '" + value + "' !");
            }

            // Now parse the value
            secondToken = tokens.get(1);

            switch (secondToken.getTokenCode()) {
            case FilterToken.NUMBER:
                sb.append("cast(");
                sb.append(key);
                sb.append(" as varchar(32)) like ?");
                args.add("%" + secondToken.getSequence() + "%");
                break;
            case FilterToken.STRING:

                // The '~' operator can be combined with a string having '*' symbols
                ITokenizer starTokenizer = new StarTokenizer();
                List<IToken> starTokens = starTokenizer.tokenize(this.extractUnquotedString(secondToken));

                String likeString = "%";
                sb.append(key);
                sb.append(" like ?");

                // The string contains '*' symbols
                if (starTokens.size() > 1) {
                    int i = 0;

                    for (IToken starToken : starTokens) {
                        if ("*".equals(starToken.getSequence())) {
                            // If the star is not positionned as the first or last token (this is because
                            // the '~' operator already adds '%' at start and end of target string)
                            if (i != 0 && i != starTokens.size() - 1) {
                                likeString += "%";
                            }
                        } else {
                            likeString += starToken.getSequence();
                        }

                        i++;
                    }
                }

                // The string does not contain '*' symbols
                else {
                    likeString += this.extractUnquotedString(secondToken);
                }

                likeString += "%";
                args.add(likeString);

                break;
            default:
                throw new ConverterException("Invalid use of '~' operator !");
            }

            break;

        // The first token is a '!' operator
        case FilterToken.NOT:

            secondToken = tokens.get(1);
            sb.append(key);

            // The end of string is not a number or a string
            if (tokens.size() > 2) {
                // Re-apply the same processing to the string after '!'
                Map.Entry<String, List<? extends Serializable>> endOfFilterConverted = null;

                // Manage the '!~' cases
                if (secondToken.getTokenCode() == FilterToken.LIKE) {
                    throw new ConverterException(
                            "Using the '!' operator before the '~' is currently not supported, please used the '!' "
                                    + "operator with a string having '*' characters instead !");
                }

                // Else
                else {
                    endOfFilterConverted = this.parseFromFirstToken(key, value.substring(1),
                            tokens.subList(1, tokens.size()), true);
                }

                sb.append(" ");

                // If the "sub-filter" is not a like expression
                if (endOfFilterConverted.getKey().indexOf("not like ") == -1
                        && endOfFilterConverted.getKey().indexOf("not in") == -1) {
                    sb.append("!");
                }

                sb.append(endOfFilterConverted.getKey());
                args.addAll(endOfFilterConverted.getValue());
            }

            // The end of string is a number or a string
            else if (tokens.size() == 2) {
                sb.append(" = ");

                switch (secondToken.getTokenCode()) {
                case FilterToken.NUMBER:
                    sb.append("!?");
                    args.add(this.parseNumberToken(secondToken));
                    break;
                case FilterToken.STRING:
                    sb.append("!?");
                    args.add(this.extractUnquotedString(secondToken));
                    break;
                default:
                    throw new ConverterException("Invalid filter expression '" + value + "' !");
                }

            }

            // Otherwise we are on an invalid filter expression
            else {
                throw new ConverterException("Invalid filter expression '" + value + "' !");
            }

            break;

        // The first token is an 'in' operator
        case FilterToken.IN:

            // If their is not at least 4 tokens then the 'in' expression is not well formed
            if (tokens.size() < 4) {
                throw new ConverterException("Invalid filter expression '" + value + "' !");
            }

            // The second token must be an open bracket
            secondToken = tokens.get(1);
            if (secondToken.getTokenCode() != FilterToken.OPEN_BRAKET) {
                throw new ConverterException("Invalid filter expression '" + value + "' !");
            }

            // If the 'in' expression is not after a '!' operator
            if (!afterNot) {
                sb.append(key);
                sb.append(" ");
            } else {
                sb.append("not ");
            }

            sb.append("in(");

            // Loop through the list of values
            boolean canEncounterComma = false;
            int i = 2;
            IToken currentToken = tokens.get(i);

            while (currentToken != null && currentToken.getTokenCode() != FilterToken.CLOSE_BRAKET) {
                if (currentToken.getTokenCode() == FilterToken.CLOSE_BRAKET) {
                    break;
                }

                // If a ',' is expected and the current token is not a comma this is an error
                if (canEncounterComma && currentToken.getTokenCode() != FilterToken.COMMA) {
                    throw new ConverterException("Invalid filter expression '" + value + "' !");
                }

                // If a ',' is expected and encountered
                else if (canEncounterComma && currentToken.getTokenCode() == FilterToken.COMMA) {
                    sb.append(",");
                    ++i;
                    try {
                        currentToken = tokens.get(i);
                        canEncounterComma = false;
                        continue;
                    } catch (IndexOutOfBoundsException ioobex) {
                        throw new ConverterException("Invalid filter expression '" + value + "' !", ioobex);
                    }
                }

                // Otherwise
                else {
                    switch (currentToken.getTokenCode()) {
                    case FilterToken.NUMBER:
                        sb.append("?");
                        args.add(this.parseNumberToken(currentToken));
                        break;
                    case FilterToken.STRING:
                        sb.append("?");
                        args.add(this.extractUnquotedString(currentToken));
                        break;
                    default:
                        throw new ConverterException("Invalid filter expression '" + value + "' !");
                    }

                    ++i;

                    try {
                        currentToken = tokens.get(i);
                        canEncounterComma = true;
                        continue;
                    } catch (IndexOutOfBoundsException ioobex) {
                        throw new ConverterException("Invalid filter expression '" + value + "' !", ioobex);
                    }
                }
            }

            sb.append(")");
            break;

        // The first token is a string
        case FilterToken.STRING:
            throw new ConverterException("Invalid filter expression '" + value + "' !");

            // The first token is a number
        case FilterToken.NUMBER:

            // If their is more than one tohen then this is an error
            if (tokens.size() != 1) {
                throw new ConverterException("Invalid filter expression '" + value + "' !");
            }

            sb.append(key + " = ?");
            args.add(this.parseNumberToken(firstToken));

            break;

        // All other first token are currently not supported
        default:
            break;
        }

        return new AbstractMap.SimpleImmutableEntry<>(sb.toString(), args);
    }

    /**
     * Utility method used to parse the value of a number token and return a Java {@link Integer} instance if the number
     * is an integer and a Java {@link Double} instance if the number is a double.
     *
     * @param token the token to parse.
     *
     * @return an {@link Integer} or a {@link Double} depending on the type of the parsed number.
     */
    private Serializable parseNumberToken(final IToken token) {
        Serializable parsed = null;

        try {
            // If successful the number is an integer
            parsed = Integer.parseInt(token.getSequence());
        } catch (NumberFormatException nfex) {
            // If successful the number is a double
            parsed = Double.parseDouble(token.getSequence());
        }

        return parsed;
    }

    /**
     * Transforms a simple filter into an SQL equivalent instruction.
     *
     * @param key the filter key.
     * @param value the filter value.
     * @param context additional context variable to replace.
     *
     * @return a key / value pair which maps the resulting SQL filter with its prepared statement parameters.
     */
    private Map.Entry<String, List<? extends Serializable>> transformSimpleFilter(final String key, final String value,
            final Map<String, Object> context) {
        Map.Entry<String, List<? extends Serializable>> result = new AbstractMap.SimpleImmutableEntry<>("",
                new ArrayList<Serializable>());

        try {
            // Creates a tokenizer to tokenize the filter value
            ITokenizer tokenizer = new FilterTokenizer();

            // Tokenize the filter
            List<IToken> tokens = tokenizer.tokenize(value);

            // Now parse the tokens
            if (!tokens.isEmpty()) {
                result = this.parseFromFirstToken(key, value, tokens, false);
            }

        } catch (TokenizerException tex) {
            // If an exception is encountered at tokenization then we consider the value to be a simple string
            result = new AbstractMap.SimpleImmutableEntry<>(key + " = ?", Arrays.asList(value));
        }

        return result;
    }
}
