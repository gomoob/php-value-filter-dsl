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

use Gomoob\Filter\SqlFilterInterface;

/**
 * Class which represents an SQL filter to be used to create an SQL query.
 *
 * @author Baptiste GAILLARD (baptiste.gaillard@gomoob.com)
 */
class SqlFilter implements SqlFilterInterface
{
    /**
     * The SQL filter expression to concat with an SQL query.
     *
     * @var string
     */
    private $expression;

    /**
     * The SQL prepared statement parameters to add, this array has one parameter for each `?` characters inside the
     * filter expression.
     *
     * @var array
     */
    private $params;

    /**
     * Creates a new instance of the `SqlFilter` class.
     *
     * @param string $expression the SQL filter expression to concat with an SQL query.
     * @param array $params the SQL prepared statement parameters to add, this array has one parameter for each `?`
     *        characters inside the filter expression.
     */
    public function __construct(/* string */ $expression, /* array */ $params)
    {
        $this->expression = $expression;
        $this->params = $params;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams()
    {
        return $this->params;
    }
}
