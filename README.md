# php-value-filter-dsl

> Powerful filter DSL PHP library for REST Web Services query / URL parameters or other filtering needs.

[![Total Downloads](https://img.shields.io/packagist/dt/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://packagist.org/packages/gomoob/php-value-filter-dsl)
[![Latest Stable Version](https://img.shields.io/packagist/v/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://packagist.org/packages/gomoob/php-value-filter-dsl)
[![Build Status](https://img.shields.io/travis/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://travis-ci.org/gomoob/php-value-filter-dsl)
[![Coverage](https://img.shields.io/coveralls/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://coveralls.io/r/gomoob/php-value-filter-dsl?branch=master)
[![Code Climate](https://img.shields.io/codeclimate/github/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://codeclimate.com/github/gomoob/php-value-filter-dsl)
[![License](https://img.shields.io/packagist/l/gomoob/php-value-filter-dsl.svg?style=flat-square)](https://packagist.org/packages/gomoob/php-value-filter-dsl)

## Sample with not in

Suppose you have a Web Service accessible using `https://api.myserver.com/users` and you want to create a filter to find
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

Please note that for now we only provide convertion of our filters into SQL, but will extend the library to provide
additional converters to transform the filters into other formats.

## Documentation

In progress.

## About Gomoob

At [Gomoob](https://www.gomoob.com) we build high quality software with awesome Open Source frameworks everyday. Would
you like to start your next project with us? That's great! Give us a call or send us an email and we will get back to
you as soon as possible !

You can contact us by email at [contact@gomoob.com](mailto:contact@gomoob.com) or by phone number
[(+33) 6 85 12 81 26](tel:+33685128126) or [(+33) 6 28 35 04 49](tel:+33685128126).

Visit also http://gomoob.github.io to discover more Open Source softwares we develop.
