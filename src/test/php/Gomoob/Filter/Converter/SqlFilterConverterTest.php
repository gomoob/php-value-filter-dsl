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

/**
 * Test case for the {@link SqlFilterConverter} class.
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 */
class SqlFilterConverterTest {

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransform() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();
        Map.Entry<String, List<? extends Serializable>> res = null;

        // Test with a simple integer
        res = filterConverter.transform("property", "10");
        assertThat(res.getKey()).isEqualTo("property = ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(10);

        // Test with a simple float
        res = filterConverter.transform("property", "54.12");
        assertThat(res.getKey()).isEqualTo("property = ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(54.12);

        // Test with a simple string
        res = filterConverter.transform("property", "Sample string");
        assertThat(res.getKey()).isEqualTo("property = ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("Sample string");
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformEqualTo() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();
        Map.Entry<String, List<? extends Serializable>> res = null;

        // Test with a simple integer
        res = filterConverter.transform("property", "=10");
        assertThat(res.getKey()).isEqualTo("property = ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(10);

        // Test with a simple float
        res = filterConverter.transform("property", "=54.12");
        assertThat(res.getKey()).isEqualTo("property = ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(54.12);

        // Test with a simple string
        res = filterConverter.transform("property", "='Sample string'");
        assertThat(res.getKey()).isEqualTo("property = ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("Sample string");

        // Test with a string having '*' symbols
        res = filterConverter.transform("property", "='*word1 *word2*'");
        assertThat(res.getKey()).isEqualTo("property like ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("%word1 %word2%");
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformGreaterThan() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();
        Map.Entry<String, List<? extends Serializable>> res = null;

        // Test with a simple integer
        res = filterConverter.transform("property", ">10");
        assertThat(res.getKey()).isEqualTo("property > ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(10);

        // Test with a simple float
        res = filterConverter.transform("property", ">54.12");
        assertThat(res.getKey()).isEqualTo("property > ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(54.12);

        // Test with a simple date and time
        res = filterConverter.transform("property", ">'2017-01-01T00:09:01+01:00'");
        assertThat(res.getKey()).isEqualTo("property > ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("2017-01-01T00:09:01+01:00");
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformGreaterThanOrEqual() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();
        Map.Entry<String, List<? extends Serializable>> res = null;

        // Test with a simple integer
        res = filterConverter.transform("property", ">=10");
        assertThat(res.getKey()).isEqualTo("property >= ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(10);

        // Test with a simple float
        res = filterConverter.transform("property", ">=54.12");
        assertThat(res.getKey()).isEqualTo("property >= ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(54.12);

        // Test with a simple date and time
        res = filterConverter.transform("property", ">='2017-01-01T00:09:01+01:00'");
        assertThat(res.getKey()).isEqualTo("property >= ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("2017-01-01T00:09:01+01:00");
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformLessThan() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();
        Map.Entry<String, List<? extends Serializable>> res = null;

        // Test with a simple integer
        res = filterConverter.transform("property", "<10");
        assertThat(res.getKey()).isEqualTo("property < ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(10);

        // Test with a simple float
        res = filterConverter.transform("property", "<54.12");
        assertThat(res.getKey()).isEqualTo("property < ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(54.12);

        // Test with a simple date and time
        res = filterConverter.transform("property", "<'2017-01-01T00:09:01+01:00'");
        assertThat(res.getKey()).isEqualTo("property < ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("2017-01-01T00:09:01+01:00");
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformLessThanOrEqual() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();
        Map.Entry<String, List<? extends Serializable>> res = null;

        // Test with a simple integer
        res = filterConverter.transform("property", "<=10");
        assertThat(res.getKey()).isEqualTo("property <= ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(10);

        // Test with a simple float
        res = filterConverter.transform("property", "<=54.12");
        assertThat(res.getKey()).isEqualTo("property <= ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(54.12);

        // Test with a simple date and time
        res = filterConverter.transform("property", "<='2017-01-01T00:09:01+01:00'");
        assertThat(res.getKey()).isEqualTo("property <= ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("2017-01-01T00:09:01+01:00");
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformIn() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();
        Map.Entry<String, List<? extends Serializable>> res = null;

        // Test with an in and only integers
        res = filterConverter.transform("property", "in(5,12,3)");
        assertThat(res.getKey()).isEqualTo("property in(?,?,?)");
        assertThat(res.getValue()).hasSize(3);
        assertThat(res.getValue().get(0)).isEqualTo(5);
        assertThat(res.getValue().get(1)).isEqualTo(12);
        assertThat(res.getValue().get(2)).isEqualTo(3);

        // Test with an in and only floats
        res = filterConverter.transform("property", "in(5.34,2.1,3.89)");
        assertThat(res.getKey()).isEqualTo("property in(?,?,?)");
        assertThat(res.getValue()).hasSize(3);
        assertThat(res.getValue().get(0)).isEqualTo(5.34);
        assertThat(res.getValue().get(1)).isEqualTo(2.1);
        assertThat(res.getValue().get(2)).isEqualTo(3.89);

        // Test with an in and only strings
        res = filterConverter.transform("property", "in('string 1','string 2','string 3')");
        assertThat(res.getKey()).isEqualTo("property in(?,?,?)");
        assertThat(res.getValue()).hasSize(3);
        assertThat(res.getValue().get(0)).isEqualTo("string 1");
        assertThat(res.getValue().get(1)).isEqualTo("string 2");
        assertThat(res.getValue().get(2)).isEqualTo("string 3");
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformLike() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();
        Map.Entry<String, List<? extends Serializable>> res = null;

        // Test with a simple integer
        res = filterConverter.transform("property", "~10");
        assertThat(res.getKey()).isEqualTo("cast(property as varchar(32)) like ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("%10%");

        // Test with a simple float
        res = filterConverter.transform("property", "~54.12");
        assertThat(res.getKey()).isEqualTo("cast(property as varchar(32)) like ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("%54.12%");

        // Test with a simple string
        res = filterConverter.transform("property", "~'Sample string'");
        assertThat(res.getKey()).isEqualTo("property like ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("%Sample string%");

        // Test with a string having '*' symbols
        res = filterConverter.transform("property", "~'word1 *word word3'");
        assertThat(res.getKey()).isEqualTo("property like ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("%word1 %word word3%");

        // Test with a string having '*' symbols
        res = filterConverter.transform("property", "~'*word1 *word word3*'");
        assertThat(res.getKey()).isEqualTo("property like ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("%word1 %word word3%");
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformNot() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();
        Map.Entry<String, List<? extends Serializable>> res = null;

        // Test with a simple integer
        res = filterConverter.transform("property", "!10");
        assertThat(res.getKey()).isEqualTo("property = !?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(10);

        // Test with a simple float
        res = filterConverter.transform("property", "!54.12");
        assertThat(res.getKey()).isEqualTo("property = !?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(54.12);

        // Test with a simple string
        res = filterConverter.transform("property", "!'Sample string'");
        assertThat(res.getKey()).isEqualTo("property = !?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("Sample string");
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformNotEqualTo() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();
        Map.Entry<String, List<? extends Serializable>> res = null;

        // Test with a simple integer
        res = filterConverter.transform("property", "!=10");
        assertThat(res.getKey()).isEqualTo("property != ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(10);

        // Test with a simple float
        res = filterConverter.transform("property", "!=54.12");
        assertThat(res.getKey()).isEqualTo("property != ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo(54.12);

        // Test with a simple string
        res = filterConverter.transform("property", "!='Sample string'");
        assertThat(res.getKey()).isEqualTo("property != ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("Sample string");

        // Test with a string having '*' symbols
        res = filterConverter.transform("property", "!='*word1 *word2*'");
        assertThat(res.getKey()).isEqualTo("property not like ?");
        assertThat(res.getValue()).hasSize(1);
        assertThat(res.getValue().get(0)).isEqualTo("%word1 %word2%");
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformNotGreaterThan() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();

        try {
            filterConverter.transform("property", "!>10");
        } catch (ConverterException cex) {
            assertThat(cex.getMessage()).isEqualTo("Using the '!' operator before the '>' operator is forbidden !");
        }
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformNotGreaterThanOrEqual() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();

        try {
            filterConverter.transform("property", "!>=10");
        } catch (ConverterException cex) {
            assertThat(cex.getMessage()).isEqualTo("Using the '!' operator before the '>=' operator is forbidden !");
        }
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformNotLessThan() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();

        try {
            filterConverter.transform("property", "!<10");
        } catch (ConverterException cex) {
            assertThat(cex.getMessage()).isEqualTo("Using the '!' operator before the '<' operator is forbidden !");
        }
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformNotLessThanOrEqual() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();

        try {
            filterConverter.transform("property", "!<=10");
        } catch (ConverterException cex) {
            assertThat(cex.getMessage()).isEqualTo("Using the '!' operator before the '<=' operator is forbidden !");
        }
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformNotIn() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();
        Map.Entry<String, List<? extends Serializable>> res = null;

        // Test with an in and only integers
        res = filterConverter.transform("property", "!in(5,12,3)");
        assertThat(res.getKey()).isEqualTo("property not in(?,?,?)");
        assertThat(res.getValue()).hasSize(3);
        assertThat(res.getValue().get(0)).isEqualTo(5);
        assertThat(res.getValue().get(1)).isEqualTo(12);
        assertThat(res.getValue().get(2)).isEqualTo(3);

        // Test with an in and only floats
        res = filterConverter.transform("property", "!in(5.34,2.1,3.89)");
        assertThat(res.getKey()).isEqualTo("property not in(?,?,?)");
        assertThat(res.getValue()).hasSize(3);
        assertThat(res.getValue().get(0)).isEqualTo(5.34);
        assertThat(res.getValue().get(1)).isEqualTo(2.1);
        assertThat(res.getValue().get(2)).isEqualTo(3.89);

        // Test with an in and only strings
        res = filterConverter.transform("property", "!in('string 1','string 2','string 3')");
        assertThat(res.getKey()).isEqualTo("property not in(?,?,?)");
        assertThat(res.getValue()).hasSize(3);
        assertThat(res.getValue().get(0)).isEqualTo("string 1");
        assertThat(res.getValue().get(1)).isEqualTo("string 2");
        assertThat(res.getValue().get(2)).isEqualTo("string 3");
    }

    /**
     * Test method for {@link SqlFilterConverter#transform(Object, String)}.
     */
    @Test
    public void testTransformNotLike() {
        ISqlFilterConverter filterConverter = new SqlFilterConverter();

        try {
            filterConverter.transform("property", "!~10");
        } catch (ConverterException cex) {
            assertThat(cex.getMessage()).isEqualTo(
                    "Using the '!' operator before the '~' is currently not supported, please used the '!' operator "
                            + "with a string having '*' characters instead !");
        }
    }
}
