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
namespace Gomoob\Filter\Parser;

use Gomoob\Filter\DateTimeParserInterface;

/**
 * Class which represents `DateTime` parser to parse string dates.
 *
 * <p>The format of the string dates can be specified using the standard <tt>\DateTime</tt> format.</p>
 *
 * <p>If you want to choose a format which is compliant with a specification you can use one of the following: </p>
 * <ul>
 *     <li>DateTime::ATOM</li>
 *     <li>DateTime::COOKIE</li>
 *     <li>DateTime::ISO8601</li>
 *     <li>DateTime::RFC822</li>
 *     <li>DateTime::RFC850</li>
 *     <li>DateTime::RFC1036</li>
 *     <li>DateTime::RFC1123</li>
 *     <li>DateTime::RFC2822</li>
 *     <li>DateTime::RFC3339</li>
 *     <li>DateTime::RSS</li>
 *     <li>DateTime::W3C</li>
 * </ul>
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 */
class FormatDateTimeParser implements DateTimeParserInterface
{
    /**
     * The format used to parse `\DateTime` strings.
     *
     * @var string
     *
     * @see http://www.php.net/manual/en/datetime.createfromformat.php
     */
    private $format = \DateTime::ISO8601;

    /**
     * {@inheritDoc}
     */
    public function parse(/* string */ $str) /* : \DateTime */
    {
        $date = \DateTime::createFromFormat($this->format, $str);

        // If the conversion failed
        if ($date === false) {
            // If the format is ISO8601 then we try to parse with additional formats, this is because the PHP
            // \DateTime:ISO8601 does not accepts all ISO8601 formats.
            //
            // see https://stackoverflow.com/questions/4411340/
            //         php-datetimecreatefromformat-doesnt-parse-iso-8601-date-time
            // see https://stackoverflow.com/questions/6150280/
            //         get-the-iso-8601-with-seconds-decimal-fraction-of-second-date-in-php
            // see https://bugs.php.net/bug.php?id=51950
            if ($this->format === \DateTime::ISO8601) {
                $date = \DateTime::createFromFormat('Y-m-d\TH:i:s.uP', $str);
            }

            // If the conversion failed
            if ($date === false) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '%s method received an \'%s\' date string which is not compliant with the configured ' .
                        'format !',
                        __METHOD__,
                        $str
                    )
                );
            }
        }

        return $date;
    }

    /**
     * Sets the format to us to parse date and time strings.
     *
     * <p>If you want to choose a format which is compliant with a specification you can use one of the following: </p>
     * <ul>
     *     <li>DateTime::ATOM</li>
     *     <li>DateTime::COOKIE</li>
     *     <li>DateTime::ISO8601</li>
     *     <li>DateTime::RFC822</li>
     *     <li>DateTime::RFC850</li>
     *     <li>DateTime::RFC1036</li>
     *     <li>DateTime::RFC1123</li>
     *     <li>DateTime::RFC2822</li>
     *     <li>DateTime::RFC3339</li>
     *     <li>DateTime::RSS</li>
     *     <li>DateTime::W3C</li>
     * </ul>
     *
     * @param string $format the format to use to parse date and time strings.
     */
    public function setFormat(/* string */ $format)
    {
        $this->format = $format;
    }
}
