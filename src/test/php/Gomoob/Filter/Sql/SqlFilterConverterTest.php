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

use Gomoob\Filter\Converter\ConverterException;

use PHPUnit\Framework\TestCase;

/**
 * Test case for the `\Gomoob\Filter\Converter\SqlFilterConverter` class.
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 * @group SqlFilterConverterTest
 */
class SqlFilterConverterTest extends TestCase
{
    /**
     * An instance of the SQL filter converter to test.
     *
     * @var \Gomoob\Filter\Sql\SqlFilterConverter
     */
    private $filterConverter;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->filterConverter = new SqlFilterConverter();
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransform
     */
    public function testTransform()
    {
        // Test with a simple integer
        $sqlFilter = $this->filterConverter->transform('property', '10');
        $this->assertSame('property = ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(10, $sqlFilter->getParams()[0]);

        // Test with a simple float
        $sqlFilter = $this->filterConverter->transform('property', '54.12');
        $this->assertSame('property = ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(54.12, $sqlFilter->getParams()[0]);

        // Test with a simple string
        $sqlFilter = $this->filterConverter->transform('property', 'Sample string');
        $this->assertSame('property = ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('Sample string', $sqlFilter->getParams()[0]);

        // Test with a key which has a bad type
        try {
            $this->filterConverter->transform(0.26, '>10');
            $this->fail('Must have thrown a ConverterException !');
        } catch (ConverterException $cex) {
            $this->assertSame('Invalid filter key type !', $cex->getMessage());
        }
    }


    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformAnd
     */
    public function testTransformAnd()
    {
        // Test with integers
        $sqlFilter = $this->filterConverter->transform('property', '<10+>2');
        $this->assertSame('property < ? AND property > ?', $sqlFilter->getExpression());
        $this->assertCount(2, $sqlFilter->getParams());
        $this->assertSame(10, $sqlFilter->getParams()[0]);
        $this->assertSame(2, $sqlFilter->getParams()[1]);

        // Test with floats
        $sqlFilter = $this->filterConverter->transform('property', '<5.3+>3.4');
        $this->assertSame('property < ? AND property > ?', $sqlFilter->getExpression());
        $this->assertCount(2, $sqlFilter->getParams());
        $this->assertSame(5.3, $sqlFilter->getParams()[0]);
        $this->assertSame(3.4, $sqlFilter->getParams()[1]);

        // Test with strings
        $sqlFilter = $this->filterConverter->transform('property', "Handball+Football");
        $this->assertSame('property = ? AND property = ?', $sqlFilter->getExpression());
        $this->assertCount(2, $sqlFilter->getParams());
        $this->assertSame('Handball', $sqlFilter->getParams()[0]);
        $this->assertSame('Football', $sqlFilter->getParams()[1]);

        // Test with strings and the like operator
        $sqlFilter = $this->filterConverter->transform('property', "~'*ball*'+~'*tennis*'");
        $this->assertSame('property like ? AND property like ?', $sqlFilter->getExpression());
        $this->assertCount(2, $sqlFilter->getParams());
        $this->assertSame('%ball%', $sqlFilter->getParams()[0]);
        $this->assertSame('%tennis%', $sqlFilter->getParams()[1]);
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformComplex
     */
    public function testTransformComplex()
    {
        // Test with a complex filter with multiple properties (currently not supported and will fail)
        try {
            $this->filterConverter->transform(0, 'price:<90-validity:>=3');
            $this->fail('Must have thrown a ConverterException !');
        } catch (ConverterException $cex) {
            $this->assertSame('Complex filters are currently not implemented !', $cex->getMessage());
        }
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformEqualTo
     */
    public function testTransformEqualTo()
    {
        // Test with a simple integer
        $sqlFilter = $this->filterConverter->transform('property', '=10');
        $this->assertSame('property = ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(10, $sqlFilter->getParams()[0]);

        // Test with a simple float
        $sqlFilter = $this->filterConverter->transform('property', "=54.12");
        $this->assertSame('property = ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(54.12, $sqlFilter->getParams()[0]);

        // Test with a simple string
        $sqlFilter = $this->filterConverter->transform('property', "='Sample string'");
        $this->assertSame('property = ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('Sample string', $sqlFilter->getParams()[0]);

        $sqlFilter = $this->filterConverter->transform('property', "='>=<!+-in()~,0123456789abc'");
        $this->assertSame('property = ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('>=<!+-in()~,0123456789abc', $sqlFilter->getParams()[0]);

        // Test with a string having '*' symbols
        $sqlFilter = $this->filterConverter->transform('property', "='*word1 *word2*'");
        $this->assertSame('property like ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('%word1 %word2%', $sqlFilter->getParams()[0]);
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformGreaterThan
     */
    public function testTransformGreaterThan()
    {
        // Test with a simple integer
        $sqlFilter = $this->filterConverter->transform('property', ">10");
        $this->assertSame('property > ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(10, $sqlFilter->getParams()[0]);

        // Test with a simple float
        $sqlFilter = $this->filterConverter->transform('property', ">54.12");
        $this->assertSame('property > ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(54.12, $sqlFilter->getParams()[0]);

        // Test with a simple date and time
        $sqlFilter = $this->filterConverter->transform('property', ">'2017-01-01T00:09:01+01:00'");
        $this->assertSame('property > ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('2017-01-01T00:09:01+01:00', $sqlFilter->getParams()[0]);
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformGreaterThanOrEqual
     */
    public function testTransformGreaterThanOrEqual()
    {
        // Test with a simple integer
        $sqlFilter = $this->filterConverter->transform('property', ">=10");
        $this->assertSame('property >= ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(10, $sqlFilter->getParams()[0]);

        // Test with a simple float
        $sqlFilter = $this->filterConverter->transform('property', ">=54.12");
        $this->assertSame('property >= ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(54.12, $sqlFilter->getParams()[0]);

        // Test with a simple date and time
        $sqlFilter = $this->filterConverter->transform('property', ">='2017-01-01T00:09:01+01:00'");
        $this->assertSame('property >= ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('2017-01-01T00:09:01+01:00', $sqlFilter->getParams()[0]);
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformLessThan
     */
    public function testTransformLessThan()
    {
        // Test with a simple integer
        $sqlFilter = $this->filterConverter->transform('property', "<10");
        $this->assertSame('property < ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(10, $sqlFilter->getParams()[0]);

        // Test with a simple float
        $sqlFilter = $this->filterConverter->transform('property', "<54.12");
        $this->assertSame('property < ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(54.12, $sqlFilter->getParams()[0]);

        // Test with a simple date and time
        $sqlFilter = $this->filterConverter->transform('property', "<'2017-01-01T00:09:01+01:00'");
        $this->assertSame('property < ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('2017-01-01T00:09:01+01:00', $sqlFilter->getParams()[0]);
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformLessThanOrEqual
     */
    public function testTransformLessThanOrEqual()
    {
        // Test with a simple integer
        $sqlFilter = $this->filterConverter->transform('property', "<=10");
        $this->assertSame('property <= ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(10, $sqlFilter->getParams()[0]);

        // Test with a simple float
        $sqlFilter = $this->filterConverter->transform('property', "<=54.12");
        $this->assertSame('property <= ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(54.12, $sqlFilter->getParams()[0]);

        // Test with a simple date and time
        $sqlFilter = $this->filterConverter->transform('property', "<='2017-01-01T00:09:01+01:00'");
        $this->assertSame('property <= ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('2017-01-01T00:09:01+01:00', $sqlFilter->getParams()[0]);
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformIn
     */
    public function testTransformIn()
    {
        // Test with an in and only integers
        $sqlFilter = $this->filterConverter->transform('property', "in(5,12,3)");
        $this->assertSame('property in(?,?,?)', $sqlFilter->getExpression());
        $this->assertCount(3, $sqlFilter->getParams());
        $this->assertSame(5, $sqlFilter->getParams()[0]);
        $this->assertSame(12, $sqlFilter->getParams()[1]);
        $this->assertSame(3, $sqlFilter->getParams()[2]);

        // Test with an in and only floats
        $sqlFilter = $this->filterConverter->transform('property', "in(5.34,2.1,3.89)");
        $this->assertSame('property in(?,?,?)', $sqlFilter->getExpression());
        $this->assertCount(3, $sqlFilter->getParams());
        $this->assertSame(5.34, $sqlFilter->getParams()[0]);
        $this->assertSame(2.1, $sqlFilter->getParams()[1]);
        $this->assertSame(3.89, $sqlFilter->getParams()[2]);

        // Test with an in and only strings
        $sqlFilter = $this->filterConverter->transform('property', "in('string 1','string 2','string 3')");
        $this->assertSame('property in(?,?,?)', $sqlFilter->getExpression());
        $this->assertCount(3, $sqlFilter->getParams());
        $this->assertSame('string 1', $sqlFilter->getParams()[0]);
        $this->assertSame('string 2', $sqlFilter->getParams()[1]);
        $this->assertSame('string 3', $sqlFilter->getParams()[2]);
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformLike
     */
    public function testTransformLike()
    {
        // Test with a simple integer
        $sqlFilter = $this->filterConverter->transform('property', "~10");
        $this->assertSame('cast(property as varchar(32)) like ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('%10%', $sqlFilter->getParams()[0]);

        // Test with a simple float
        $sqlFilter = $this->filterConverter->transform('property', "~54.12");
        $this->assertSame('cast(property as varchar(32)) like ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('%54.12%', $sqlFilter->getParams()[0]);

        // Test with a simple string
        $sqlFilter = $this->filterConverter->transform('property', "~'Sample string'");
        $this->assertSame('property like ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('%Sample string%', $sqlFilter->getParams()[0]);

        $sqlFilter = $this->filterConverter->transform('property', "~'>=<!+-in()~,0123456789abc'");
        $this->assertSame('property like ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('%>=<!+-in()~,0123456789abc%', $sqlFilter->getParams()[0]);

        // Test with a string having '*' symbols
        $sqlFilter = $this->filterConverter->transform('property', "~'word1 *word word3'");
        $this->assertSame('property like ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('%word1 %word word3%', $sqlFilter->getParams()[0]);

        // Test with a string having '*' symbols
        $sqlFilter = $this->filterConverter->transform('property', "~'*word1 *word word3*'");
        $this->assertSame('property like ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('%word1 %word word3%', $sqlFilter->getParams()[0]);
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformNot
     */
    public function testTransformNot()
    {
        // Test with a simple integer
        $sqlFilter = $this->filterConverter->transform('property', "!10");
        $this->assertSame('property = !?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(10, $sqlFilter->getParams()[0]);

        // Test with a simple float
        $sqlFilter = $this->filterConverter->transform('property', "!54.12");
        $this->assertSame('property = !?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(54.12, $sqlFilter->getParams()[0]);

        // Test with a simple string
        $sqlFilter = $this->filterConverter->transform('property', "!'Sample string'");
        $this->assertSame('property = !?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('Sample string', $sqlFilter->getParams()[0]);
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformNotEqualTo
     */
    public function testTransformNotEqualTo()
    {
        // Test with a simple integer
        $sqlFilter = $this->filterConverter->transform('property', '!=10');
        $this->assertSame('property != ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(10, $sqlFilter->getParams()[0]);

        // Test with a simple float
        $sqlFilter = $this->filterConverter->transform('property', "!=54.12");
        $this->assertSame('property != ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame(54.12, $sqlFilter->getParams()[0]);

        // Test with a simple string
        $sqlFilter = $this->filterConverter->transform('property', "!='Sample string'");
        $this->assertSame('property != ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('Sample string', $sqlFilter->getParams()[0]);

        $sqlFilter = $this->filterConverter->transform('property', "!='>=<!+-in()~,0123456789abc'");
        $this->assertSame('property != ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('>=<!+-in()~,0123456789abc', $sqlFilter->getParams()[0]);

        // Test with a string having '*' symbols
        $sqlFilter = $this->filterConverter->transform('property', "!='*word1 *word2*'");
        $this->assertSame('property not like ?', $sqlFilter->getExpression());
        $this->assertCount(1, $sqlFilter->getParams());
        $this->assertSame('%word1 %word2%', $sqlFilter->getParams()[0]);
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformNotGreaterThan
     */
    public function testTransformNotGreaterThan()
    {
        try {
            $this->filterConverter->transform('property', "!>10");
        } catch (ConverterException $cex) {
            $this->assertSame("Using the '!' operator before the '>' operator is forbidden !", $cex->getMessage());
        }
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformNotGreaterThanOrEqual
     */
    public function testTransformNotGreaterThanOrEqual()
    {
        try {
            $this->filterConverter->transform('property', "!>=10");
        } catch (ConverterException $cex) {
            $this->assertSame("Using the '!' operator before the '>=' operator is forbidden !", $cex->getMessage());
        }
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformNotLessThan
     */
    public function testTransformNotLessThan()
    {
        try {
            $this->filterConverter->transform('property', "!<10");
        } catch (ConverterException $cex) {
            $this->assertSame("Using the '!' operator before the '<' operator is forbidden !", $cex->getMessage());
        }
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformNotLessThanOrEqual
     */
    public function testTransformNotLessThanOrEqual()
    {
        try {
            $this->filterConverter->transform('property', "!<=10");
        } catch (ConverterException $cex) {
            $this->assertSame("Using the '!' operator before the '<=' operator is forbidden !", $cex->getMessage());
        }
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformNotIn
     */
    public function testTransformNotIn()
    {
        // Test with an in and only integers
        $sqlFilter = $this->filterConverter->transform('property', "!in(5,12,3)");
        $this->assertSame('property not in(?,?,?)', $sqlFilter->getExpression());
        $this->assertCount(3, $sqlFilter->getParams());
        $this->assertSame(5, $sqlFilter->getParams()[0]);
        $this->assertSame(12, $sqlFilter->getParams()[1]);
        $this->assertSame(3, $sqlFilter->getParams()[2]);

        // Test with an in and only floats
        $sqlFilter = $this->filterConverter->transform('property', "!in(5.34,2.1,3.89)");
        $this->assertSame('property not in(?,?,?)', $sqlFilter->getExpression());
        $this->assertCount(3, $sqlFilter->getParams());
        $this->assertSame(5.34, $sqlFilter->getParams()[0]);
        $this->assertSame(2.1, $sqlFilter->getParams()[1]);
        $this->assertSame(3.89, $sqlFilter->getParams()[2]);

        // Test with an in and only strings
        $sqlFilter = $this->filterConverter->transform('property', "!in('string 1','string 2','string 3')");
        $this->assertSame('property not in(?,?,?)', $sqlFilter->getExpression());
        $this->assertCount(3, $sqlFilter->getParams());
        $this->assertSame('string 1', $sqlFilter->getParams()[0]);
        $this->assertSame('string 2', $sqlFilter->getParams()[1]);
        $this->assertSame('string 3', $sqlFilter->getParams()[2]);
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformNotLike
     */
    public function testTransformNotLike()
    {
        try {
            $this->filterConverter->transform('property', "!~10");
        } catch (ConverterException $cex) {
            $this->assertSame(
                "Using the '!' operator before the '~' is currently not supported, please used the '!' operator "
                . "with a string having '*' characters instead !",
                $cex->getMessage()
            );
        }
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     *
     * @group SqlFilterConverterTest.testTransformOr
     */
    public function testTransformOr()
    {
        // Test with integers
        $sqlFilter = $this->filterConverter->transform('property', '<10->2');
        $this->assertSame('property < ? OR property > ?', $sqlFilter->getExpression());
        $this->assertCount(2, $sqlFilter->getParams());
        $this->assertSame(10, $sqlFilter->getParams()[0]);
        $this->assertSame(2, $sqlFilter->getParams()[1]);

        // Test with floats
        $sqlFilter = $this->filterConverter->transform('property', '<5.3->3.4');
        $this->assertSame('property < ? OR property > ?', $sqlFilter->getExpression());
        $this->assertCount(2, $sqlFilter->getParams());
        $this->assertSame(5.3, $sqlFilter->getParams()[0]);
        $this->assertSame(3.4, $sqlFilter->getParams()[1]);

        // Test with strings
        $sqlFilter = $this->filterConverter->transform('property', "Handball-Football");
        $this->assertSame('property = ? OR property = ?', $sqlFilter->getExpression());
        $this->assertCount(2, $sqlFilter->getParams());
        $this->assertSame('Handball', $sqlFilter->getParams()[0]);
        $this->assertSame('Football', $sqlFilter->getParams()[1]);

        // Test with strings and the like operator
        $sqlFilter = $this->filterConverter->transform('property', "~'*ball*'-~'*tennis*'");
        $this->assertSame('property like ? OR property like ?', $sqlFilter->getExpression());
        $this->assertCount(2, $sqlFilter->getParams());
        $this->assertSame('%ball%', $sqlFilter->getParams()[0]);
        $this->assertSame('%tennis%', $sqlFilter->getParams()[1]);
    }
}
