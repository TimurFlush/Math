<?php 

use TimurFlush\Math\Number as n;
use TimurFlush\Math\Exception as e;
use Codeception\Stub;

class NumberCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function closedConstructorTest(UnitTester $I)
    {
        $I->expectThrowable(Error::class, function () {
            new n();
        });
    }

    public function createFromSelfTest(UnitTester $I)
    {
        $mock = Stub::make(n::class);
        $clone = n::create($mock);

        $I->assertTrue(is_object($mock) && is_object($clone));
        $I->assertFalse(spl_object_id($mock) === spl_object_id($clone));
    }

    public function createFromTest(UnitTester $I)
    {
        $array = [
            [
                'in' => null,
                'out' => 0,
            ],
            [
                'in' => 1,
                'out' => '1'
            ],
            [
                'in' => 1.15,
                'out' => '1.15'
            ],
            [
                'in' => -1.51,
                'out' => '-1.51'
            ],
            [
                'in' => '-215.51',
                'out' => '-215.51',
            ],
            [
                'in' => '15',
                'out' => 15,
            ]
        ];

        foreach ($array as $item) {
            $n =  n::create($item['in']);
            $I->assertEquals($item['out'], strval($n));
        }
    }

    public function createFromInvalidDataTest(UnitTester $I)
    {
        $I->expectThrowable(e::class, function () {
            n::create('kek');
        });
        $I->expectThrowable(e::class, function () {
            n::create(false);
        });
        $I->expectThrowable(e::class, function () {
            n::create('0.');
        });
    }

    public function afterPointGetterTest(UnitTester $I)
    {
        $array = [
            '0.00' => 2,
            '0' => 0,
            '0.0' => 1
        ];

        foreach ($array as $in => $out) {
            $n = n::create($in);
            $I->assertEquals($out, $n->afterPoint());
        }
    }

    public function afterPointSetterTest(UnitTester $I)
    {
        $array = [
            [
                'in' => ['-54.55451', -228],
                'exception' => true,
            ],
            [
                'in' => ['-6.41', 1],
                'out' => '-6.4'
            ],
            [
                'in' => ['5.1001511', 4],
                'out' => '5.1001'
            ],
            [
                'in' => ['5.60', 6],
                'out' => '5.600000'
            ],
            [
                'in' => ['515.240', 4],
                'out' => '515.2400'
            ],
            [
                'in' => ['15', 5],
                'out' => '15.00000'
            ],
            [
                'in' => ['15.0151', 0],
                'out' => '15',
            ],
            [
                'in' => ['-191.01241667', 0],
                'out' => '-191'
            ],
            [
                'in' => ['0', 3],
                'out' => '0.000'
            ]
        ];

        foreach ($array as $item) {
            if (isset($item['exception'])) {
                $I->expectThrowable(e::class, function () use ($item) {
                    $n = n::create($item['in'][0])->afterPoint($item['in'][1]);
                });
            } else {
                $n = n::create($item['in'][0])->afterPoint($item['in'][1]);
                $I->assertEquals($item['out'], strval($n));
            }
        }
    }

    public function addTest(UnitTester $I)
    {
        $array = [
            [
                'scale' => 3,
                'in' => ['-9.251', '9.515'],
                'out' => '0.264'
            ],
            [
                'scale' => null,
                'in' => ['5', '2.5'],
                'out' => '7.5'
            ],
            [
                'scale' => 4,
                'in' => ['2.23481', '10.23415'],
                'out' => '12.4689'
            ],
            [
                'scale' => null,
                'in' => ['5.51215', '2.51509'],
                'out' => '8.02724'
            ],
        ];

        foreach ($array as $item) {
            $n = n::create($item['in'][0]);
            $add = $n->add($item['in'][1], $item['scale']);

            if ($add instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($add)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertEquals($item['out'], strval($add));
        }
    }

    public function subTest(UnitTester $I)
    {
        $array = [
            [
                'scale' => 3,
                'in' => ['-9.251', '9.515'],
                'out' => '-18.766'
            ],
            [
                'scale' => null,
                'in' => ['5', '2.5'],
                'out' => '2.5'
            ],
            [
                'scale' => 4,
                'in' => ['2.23481', '10.23415'],
                'out' => '-7.9993'
            ],
            [
                'scale' => null,
                'in' => ['5.51215', '2.51509'],
                'out' => '2.99706'
            ],
        ];

        foreach ($array as $item) {
            $n = n::create($item['in'][0]);
            $sub = $n->sub($item['in'][1], $item['scale']);

            if ($sub instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($sub)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertEquals($item['out'], strval($sub));
        }
    }

    public function divTest(UnitTester $I)
    {
        $array = [
            [
                'scale' => 3,
                'in' => ['-9.251', '9.515'],
                'out' => '-0.972'
            ],
            [
                'scale' => null,
                'in' => ['5', '2.5'],
                'out' => '2'
            ],
            [
                'scale' => 4,
                'in' => ['2.23481', '10.23415'],
                'out' => '0.2183'
            ],
            [
                'scale' => 11,
                'in' => ['5.51215', '2.51509'],
                'out' => '2.19163131339'
            ],
        ];

        foreach ($array as $item) {
            $n = n::create($item['in'][0]);
            $div = $n->div($item['in'][1], $item['scale']);

            if ($div instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($div)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertEquals($item['out'], strval($div));
        }
    }

    public function mulTest(UnitTester $I)
    {
        $array = [
            [
                'scale' => 3,
                'in' => ['-9.251', '9.515'],
                'out' => '-88.023'
            ],
            [
                'scale' => null,
                'in' => ['5', '2.5'],
                'out' => '12.5'
            ],
            [
                'scale' => 4,
                'in' => ['2.23481', '10.23415'],
                'out' => '22.8713'
            ],
            [
                'scale' => 10,
                'in' => ['5.51215', '2.51509'],
                'out' => '13.8635533435'
            ],
        ];

        foreach ($array as $item) {
            $n = n::create($item['in'][0]);
            $div = $n->mul($item['in'][1], $item['scale']);

            if ($div instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($div)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertEquals($item['out'], strval($div));
        }
    }

    public function isZeroTest(UnitTester $I)
    {
        $array = [
            '0' => true,
            '0.00000001' => false,
            '0.000000000000000000001' => false,
        ];

        foreach ($array as $in => $out) {
            $n = n::create($in);
            $I->assertEquals($out, $n->isZero());
        }
    }

    public function isNegativeTest(UnitTester $I)
    {
        $array = [
            '0' => false,
            '1' => false,
            '515.411' => false,
            '-1' => true,
            '-969.0511' => true
        ];

        foreach ($array as $in => $out) {
            $n = n::create($in);
            $I->assertEquals($out, $n->isNegative());
        }
    }

    public function isPositiveTest(UnitTester $I)
    {
        $array = [
            '0' => false,
            '1' => true,
            '319.051' => true,
            '-1' => false,
            '-919.0091' => false
        ];

        foreach ($array as $in => $out) {
            $n = n::create($in);
            $I->assertEquals($out, $n->isPositive());
        }
    }

    public function toPositiveTest(UnitTester $I)
    {
        $array = [
            '-19.210' => '19.210',
            '-15' => '15',
            '29' => '29',
            '150.055' => '150.055'
        ];

        foreach ($array as $in => $out) {
            $n = n::create($in);
            $toPositive = $n->toPositive();

            if ($toPositive instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($toPositive)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertTrue($toPositive->isPositive());
            $I->assertEquals($out, strval($toPositive));
        }
    }

    public function toNegativeTest(UnitTester $I)
    {
        $array = [
            '-19.210' => '-19.210',
            '-15' => '-15',
            '29' => '-29',
            '150.055' => '-150.055'
        ];

        foreach ($array as $in => $out) {
            $n = n::create($in);
            $toNegative = $n->toNegative();

            if ($toNegative instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($toNegative)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertTrue($toNegative->isNegative());
            $I->assertEquals($out, strval($toNegative));
        }
    }

    public function roundTest(UnitTester $I)
    {
        $array = [
            [
                'scale' => 0,
                'in' => '2.0005',
                'out' => '2'
            ],
            [
                'scale' => 1,
                'in' => '15.565',
                'out' => '15.6'
            ],
            [
                'scale' => 2,
                'in' => '15.456',
                'out' => '15.46'
            ],
            [
                'scale' => 3,
                'in' => '-29.54999',
                'out' => '-29.550'
            ],
            [
                'scale' => 0,
                'in' => '1.555',
                'out' => 2,
            ]
        ];

        foreach ($array as $item) {
            $n = n::create($item['in']);
            $round = $n->round($item['scale']);

            if ($round instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($round)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertEquals($item['out'], strval($round));
        }
    }

    public function floorTest(UnitTester $I)
    {
        $array = [
            '2.5' => '2',
            '3.67' => '3',
            '4.15' => '4',
            '5.99' => '5',
            '-5' => '-5',
            '-6.1' => '-7',
            '-81.95' => '-82'
        ];

        foreach ($array as $in => $out) {
            $n = n::create($in);
            $floor = $n->floor();

            if ($floor instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($floor)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertEquals($out, strval($floor));
        }
    }

    public function ceilTest(UnitTester $I)
    {
        $array = [
            '2.5' => '3',
            '1' => '1',
            '3.67' => '4',
            '4.15' => '5',
            '5.99' => '6',
            '-2' => '-2',
            '-5.45' => '-5',
            '-6.50' => '-6',
            '-915.6151' => '-915'
        ];

        foreach ($array as $in => $out) {
            $n = n::create($in);
            $ceil = $n->ceil();

            if ($ceil instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($ceil)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertEquals($out, strval($ceil));
        }
    }

    public function compareTest(UnitTester $I)
    {
        $array = [
            [
                'in' => ['2', '2'],
                'out' => n::COMPARE_EQUAL
            ],
            [
                'in' => ['1', '2'],
                'out' => n::COMPARE_LESS
            ],
            [
                'in' => ['2', '1'],
                'out' => n::COMPARE_MORE
            ],
            [
                'in' => ['2.00001', '2.001'],
                'out' => n::COMPARE_LESS
            ],
            [
                'in' => ['2.00001', '2.001'],
                'out' => n::COMPARE_EQUAL,
                'scale' => 2
            ]
        ];

        foreach ($array as $item) {
            $n = n::create($item['in'][0]);
            $I->assertEquals($item['out'], $n->compare($item['in'][1], $item['scale'] ?? null));
        }
    }

    public function isMoreThanZeroTest(UnitTester $I)
    {
        $array = [
            '2.5' => true,
            '0' => false,
            '0.00000001' => true,
            '3.67' => true,
            '-2' => false,
            '-5.45' => false,
            '-915.6151' => false
        ];

        foreach ($array as $in => $out) {
            $n = n::create($in);
            $I->assertEquals($out, $n->isMoreThanZero());
        }
    }

    public function isLessThanZeroTest(UnitTester $I)
    {
        $array = [
            '2.5' => false,
            '0' => false,
            '3.67' => false,
            '-2' => true,
            '-5.45' => true,
            '-915.6151' => true,
            '0.00000001' => false,
            '-0.00000001' => true,
        ];

        foreach ($array as $in => $out) {
            $n = n::create($in);
            $I->assertEquals($out, $n->isLessThanZero());
        }
    }

    public function isLessThanTest(UnitTester $I)
    {
        $array = [
            [
                'in' => ['2', '3'],
                'out' => true
            ],
            [
                'in' => ['2', '1'],
                'out' => false
            ],
            [
                'in' => ['-5', '-1'],
                'out' => true
            ],
            [
                'in' => ['1.0005', '1.000006'],
                'out' => false
            ],
            [
                'in' => ['1.0005', '1.000006'],
                'out' => false,
                'scale' => 2
            ]
        ];

        foreach ($array as $item) {
            $n = n::create($item['in'][0]);
            $I->assertEquals($item['out'], $n->isLessThan($item['in'][1], $item['scale'] ?? null));
        }
    }

    public function isEqualTest(UnitTester $I)
    {
        $array = [
            [
                'in' => ['2', '3'],
                'out' => false
            ],
            [
                'in' => ['2', '2'],
                'out' => true
            ],
            [
                'in' => ['-2', '-1'],
                'out' => false
            ],
            [
                'in' => ['-2', '-2'],
                'out' => true,
            ],
            [
                'in' => ['1.00005', '1.0005'],
                'out' => true,
                'scale' => 2
            ],
            [
                'in' => ['1.00005', '1.0005'],
                'out' => false,
            ],
        ];

        foreach ($array as $item) {
            $n = n::create($item['in'][0]);
            $I->assertEquals($item['out'], $n->isEqual($item['in'][1], $item['scale'] ?? null));
        }
    }

    public function isMoreThanTest(UnitTester $I)
    {
        $array = [
            [
                'in' => ['3', '2'],
                'out' => true
            ],
            [
                'in' => ['2', '2'],
                'out' => false
            ],
            [
                'in' => ['-2', '-1'],
                'out' => false
            ],
            [
                'in' => ['-2', '-3'],
                'out' => true,
            ],
            [
                'in' => ['1.00005', '1.0005'],
                'out' => false,
                'scale' => 2
            ],
            [
                'in' => ['1.005', '1.0005'],
                'out' => true,
            ],
            [
                'in' => ['1.00005', '1.0005'],
                'out' => false,
            ],
        ];

        foreach ($array as $item) {
            $n = n::create($item['in'][0]);
            $I->assertEquals($item['out'], $n->isMoreThan($item['in'][1], $item['scale'] ?? null));
        }
    }

    public function modTest(UnitTester $I)
    {
        $array = [
            [
                'scale' => 3,
                'in' => ['10', '7'],
                'out' => '3'
            ],
            [
                'scale' => null,
                'in' => ['-10', '6'],
                'out' => '-4'
            ],
            [
                'scale' => null,
                'in' => ['10', '-6'],
                'out' => '4'
            ],
        ];

        foreach ($array as $item) {
            $n = n::create($item['in'][0]);
            $mod = $n->mod($item['in'][1], $item['scale']);

            if ($mod instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($mod)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertEquals($item['out'], strval($mod));
        }
    }

    public function sqrtTest(UnitTester $I)
    {
        $array = [
            [
                'scale' => 3,
                'in' => 4,
                'out' => '2'
            ],
            [
                'scale' => null,
                'in' => '-16',
                'out' => '4'
            ],
        ];

        foreach ($array as $item) {
            $n = n::create($item['in']);
            $sqrt = $n->sqrt($item['scale']);

            if ($sqrt instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($sqrt)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertEquals($item['out'], strval($sqrt));
        }
    }

    public function powTest(UnitTester $I)
    {
        $array = [
            [
                'scale' => 3,
                'in' => [4, 2],
                'out' => '16'
            ],
            [
                'scale' => null,
                'in' => [2, 3],
                'out' => '8'
            ],
            [
                'scale' => null,
                'in' => [-2, 3],
                'out' => '-8'
            ],
        ];

        foreach ($array as $item) {
            $n = n::create($item['in'][0]);
            $pow = $n->pow($item['in'][1], $item['scale']);

            if ($pow instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($pow)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertEquals($item['out'], strval($pow));
        }
    }

    public function powmodTest(UnitTester $I)
    {
        $array = [
            [
                'scale' => 3,
                'in' => [4, 2, 10],
                'out' => '6'
            ],
            [
                'scale' => null,
                'in' => [2, 3, -5],
                'out' => '3'
            ],
            [
                'scale' => null,
                'in' => [-2, 3, 5],
                'out' => '-3'
            ]
        ];

        foreach ($array as $item) {
            $n = n::create($item['in'][0]);
            $powmod = $n->powmod($item['in'][1], $item['in'][2], $item['scale']);

            if ($powmod instanceof n === false) {
                $I->fail('Result of sub() is not Number class.');
            } elseif (spl_object_id($n) === spl_object_id($powmod)) {
                $I->fail('Result of sub() cannot be itself.');
            }

            $I->assertEquals($item['out'], strval($powmod));
        }
    }

    public function isIntegerTest(UnitTester $I)
    {
        $array = [
            '0.1' => false,
            '15.51' => false,
            '-19195.0515' => false,
            '1' => true,
            '-51' => true,
            '0' => false,
        ];

        foreach ($array as $number => $expected) {
            $n = n::create($number);
            $I->assertEquals($expected, $n->isInteger());
        }
    }

    public function isDecimalTest(UnitTester $I)
    {
        $array = [
            '0.1' => true,
            '15.51' => true,
            '-19195.0515' => true,
            '1' => false,
            '-51' => false,
            '0' => false,
        ];

        foreach ($array as $number => $expected) {
            $n = n::create($number);
            $I->assertEquals($expected, $n->isDecimal());
        }
    }
}
