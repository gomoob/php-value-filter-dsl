# php-value-filter-dsl

> Powerful filter DSL PHP library for REST Web Services query / URL parameters or other filtering needs.

[![Total Downloads](https://img.shields.io/packagist/dt/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://packagist.org/packages/gomoob/php-value-filter-dsl)
[![Latest Stable Version](https://img.shields.io/packagist/v/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://packagist.org/packages/gomoob/php-value-filter-dsl)
[![Build Status](https://img.shields.io/travis/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://travis-ci.org/gomoob/php-value-filter-dsl)
[![Coverage](https://img.shields.io/coveralls/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://coveralls.io/r/gomoob/php-value-filter-dsl?branch=master)
[![Code Climate](https://img.shields.io/codeclimate/github/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://codeclimate.com/github/gomoob/php-value-filter-dsl)
[![License](https://img.shields.io/packagist/l/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://packagist.org/packages/gomoob/php-value-filter-dsl)

## Sample with not in

Suppose you have a Web Service accessible using `https://api.myserver.com/users` and want to create a filter to find
users not having a first name equals to `Jack`, `Joe`, `Willian` or `Averell`.

To do this you'll have to request your relationnal database with a `not in` SQL request. The `php-value-filter-dsl`
library allows you to parse a custom filter expression from an URL query parameter and convert it into an equivalent SQL
expression you can use in your SQL query builder.

Our filter expression language is a custom one designed by Gomoob to respond to lots of REST Web Services API filtering
needs, this filter expression is thorougly described in our documentation.

Filtering to exclude the 4 first names described previously would be done using the following GET HTTP request.

```
https://api.myserver.com/users?first_name=!in('Jack','Joe','Willian','Averell')
```

The PHP source code used to parse the filter expression (i.e the `first_name` URL query parameter value) is the
following.

```php
// Suppose we are inside a controller (for example inside a PSR-7 Middleware) and we got the value of the 'first_name'
// URL query parameter from "https://api.myserver.com/users?first_name=!in('Jack','Joe','Willian','Averell')"
$urlParameterName = 'first_name';
$urlParameterValue = "!in('Jack','Joe','Willian','Averell')";

// Parsing the filter expression
$filterConverter = new SqlFilterConverter();
$sqlFilter = $this->filterConverter->transform($urlParameterName, $urlParameterValue);

// Use the parsed result to build our SQL query
$preparedStatement = $pdo->prepare('select * from users where ' . $sqlFilter->getExpression());

// Bind our prepared statement parameters
$i = 1;
foreach($sqlFilter->getParams() as $param) {
    $preparedStatement->bindParam($i++, $param);
}

// Executes our query
$preparedStatement->execute();
```

The previous sample will execute the SQL query `select * from users where first_name not in('?','?','?','?')` with the
prepared statement parameters `Jack`, `Joe`, `Willian`, `Averell`.

Very simple and useful, isn't it ?

Please note that for now we only provide convertion of filter expressions in SQL. Later we'll extend the library to
provide additional converters to transform the filters into other formats.

## Documentation

### Standard operators

The expression language provides the following operators.

| Operator | ASCII value | Name                     | Value type(s)                          |
|----------|-------------|--------------------------|----------------------------------------|
| `=`      | `%3D%       | Equals                   | Integer, Float, String                 |
| `<`      | `%3C`       | Less than                | Integer, Float                         |
| `<=`     | `%3C%3D`    | Less than or equal to    | Integer, Float                         |
| `>`      | `%3E`       | Greater than             | Integer, Float                         |
| `>=`     | `%3E%3D`    | Greater than or equal to | Integer, Float                         |
| `in`     |             | In                       | Integer list, Double list, String list |
| `~`      | `%7E%`      | Like                     | String                                 |
| `!`      |             | Not                      | _see description above_                |

### Not operator

The `!` operator is special, it can be used directly before a value string or in combination with the `=` or `in`
operators.

For exemple `!5` or `!=5` to express "no equals to 5" or `!in('Paris','London')` ro express "no equals to Paris or
London".

### Like operator

The `~` operator allows to create like SQL requests, but it is always converted to expressions equals to
`my_property like ?` with a value equals to `%word%` which is not always wanted.

To express more complex like expressions you can use the `*` string operator in the value associated to the `=` or `~`
operators.

For example `property=~'*Nantes*France*'` or `property='Nantes*France*'` will be translated to `property like ?` with
a parameter equals to `%Nantes%France%`.

### Values

The following values can be used.

* `null`
* `true`, converted to the string "true" if the associated property is a string, to 1 if the property is an integer and
   to 1.0 if the property is a double
* `false`, converted to the string "false" if the associated property is a string, to 0 if the property is an integer
   and to 0.0 if the property is a double
* integer
* floting number
* string (must be quoted with simple quotes ')
* string with an ISO 8601 format for the dates

## About Gomoob

At [Gomoob](https://www.gomoob.com) we build high quality software with awesome Open Source frameworks everyday. Would
you like to start your next project with us? That's great! Give us a call or send us an email and we will get back to
you as soon as possible !

You can contact us by email at [contact@gomoob.com](mailto:contact@gomoob.com) or by phone number
[(+33) 6 85 12 81 26](tel:+33685128126) or [(+33) 6 28 35 04 49](tel:+33685128126).

Visit also http://gomoob.github.io to discover more Open Source softwares we develop.
