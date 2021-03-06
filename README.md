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
| `=`      | `%3D`       | Equals                   | Integer, Float, String                 |
| `<`      | `%3C`       | Less than                | Integer, Float                         |
| `<=`     | `%3C%3D`    | Less than or equal to    | Integer, Float                         |
| `>`      | `%3E`       | Greater than             | Integer, Float                         |
| `>=`     | `%3E%3D`    | Greater than or equal to | Integer, Float                         |
| `in`     |             | In                       | Integer list, Double list, String list |
| `~`      | `%7E`       | Like                     | String                                 |
| `!`      | `%21`       | Not                      | _see description above_                |
| `+`      | `%2B`       | And                      | _see description above_                |
| `-`      | `%2D`       | Or                       | _see description above_                |

### Not operator

The `!` operator is special, it can be used directly before a value string or in combination with the `=` or `in`
operators.

For exemple `!5` or `!=5` to express "not equals to 5" or `!in('Paris','London')` to express "not equals to Paris or
London".

### AND and OR operators

The `+` and `-` operator allow to create AND and OR SQL requests.

Here are sample expressions with logical operators.

* `property=>5.4+<12` is translated to `property >= ? AND property < ?` with 2 parameters `[5.4,12]` ;
* `property=~'*ball*'-~'*tennis*'` is translated to `property like ? OR property like ?` with 2 parameters
  `['%ball%','%tennis%'].

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

### Date and time parsing

By default when the `SqlFilterConverter` encounters a string inside an expression it simply takes it as a "standard"
string.

But you've probably business entities having date attributes and want to request those entities using data and time
filters. To do this you can set a date and time parser on the `SqlFilterConverter` to indicate him to parse date and 
time string and transform them to date and time string which are compliant with the database in use.

For example configuring the `SqlFilterConverter` to parse ISO 8601 strings and convert them to MySQL date and time format
is done with the following.

```php
$sqFilterConverter = new SqlFilterConverter();
$sqlFilterConverter->setDateTimeParser(new FormatDateTimeParser());
```

By default the `FormatDateTimeParser` class uses ISO 8601 date and time parsing, but you can change its behavior with 
the `FormatDateTimeParser->setFormat(string $format)` method. In generall you'll want to use one of the format provided
with the PHP `DateTime` class, that's to say one of `DateTime::ATOM`, `DateTime::COOKIE`, `DateTime::ISO8601`, 
`DateTime::RFC822`, `DateTime::RFC850`, `DateTime::RFC1036`, `DateTime::RFC1123`, `DateTime::RFC2822`, 
`DateTime::RFC3339`, `DateTime::RSS` or `DateTime::W3C`.

The parser parses date and time strings and convert them to PHP `DateTime` object, then internally the
`SqlFilterConverter` converts the `DateTime` object to a string which is compatible with Mysql.


For example the following transform will create a `property <= ?` expression with a value equals to
`2017-12-01 06:00:00` which is compatible with MySQL.

```php
$sqlFilter = $filterConverter->transform('property', "<='2017-12-01T06:00:00Z'");
```

## About Gomoob

At [Gomoob](https://www.gomoob.com) we build high quality software with awesome Open Source frameworks everyday. Would
you like to start your next project with us? That's great! Give us a call or send us an email and we will get back to
you as soon as possible !

You can contact us by email at [contact@gomoob.com](mailto:contact@gomoob.com) or by phone number
[(+33) 6 85 12 81 26](tel:+33685128126) or [(+33) 6 28 35 04 49](tel:+33685128126).

Visit also http://gomoob.github.io to discover more Open Source softwares we develop.
