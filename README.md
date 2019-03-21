[![Build Status](https://travis-ci.org/TimurFlush/Math.svg?branch=master)](https://travis-ci.org/TimurFlush/Math)
[![Coverage Status](https://coveralls.io/repos/github/TimurFlush/Math/badge.svg?branch=master)](https://coveralls.io/github/TimurFlush/Math?branch=master)

Враппер для bcmath.

Если вы используете Composer
```
    php composer.phar require timur-flush/math
```

Или если вы инклюдите руками
```php
    require_once 'src/Exception.php';
    require_once 'src/Number.php';
```

```php

use TimurFlush\Math\Number as n;

$a = n::create('10.00');

$a->isPositive(); //true
$a->isNegative(); //false

(string)$a->toPositive(); //10.00 new object
(string)$a->toNegative(); //-10.00 new object

# В этих методах последний аргумент scale рассчитывается
# исходя из чисел после точки в обоих числах.
# Но, бывает такое, что результат может обрезаться, т.к.
# в результате чисел после точки может быть больше.
# (советую явно задавать точность(scale) операций)
(string)$a->add('1.00'); //11 new object (плюс)
(string)$a->sub('1.00'); //9 new object (минус)
(string)$a->div('2'); //5 new object (умножение)
(string)$a->mul(2); //20 new object (деление)
(string)$a->pow(2); //100 new object (в степень)
(string)$a->powmod(2, 60); //40 new object (в степень + остаток от деления)
(string)$a->mod(6); //4 new object (остаток от деления)
$a->compare('10') === n::COMPARE_EQUAL; //true (если равен 10)
$a->compare('10') === n::COMPARE_LESS; //false (если меньше 10)
$a->compare('10') === n::COMPARE_MORE; //false (если больше 10)
$a->isLess('10'); //false (если меньше 10)
$a->isMore('10'); //false (если больше 10)
$a->isEqual('10'); //true (если равен 10)

# Здесь scale не ведет себя так как в примерах выше.
(string)$a->round('1.545'); //1 new object
(string)$a->round('1.555'); //2 new object
(string)$a->round('1.555', 2); // 1.56 new object

(string)$a->floor('1.9'); //1 new object
(string)$a->ceil('1.9'); //2 new object

$a->isZero(); //false (если нуль)
$a->isMoreThanZero(); //true (если больше нуля)
$a->isLessThanZero(); //false (если меньше нуля)

$a->afterZero(); //2 (чисел после нуля)
```

Там где возвращается новый объект (отметил как new object)
можно вызывать эти же методы.

Если вам нужно вывести число как строку то приведите объект к string
```php
echo (string)$object;
```

## Requirements
PHP ^7.2.0

BCMath

## Authors
Timur Flush. (Developer)

Matt Raines. (Helper from Stackoverflow)

## License
New BSD License.