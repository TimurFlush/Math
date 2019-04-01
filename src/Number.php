<?php declare(strict_types = 1);

namespace TimurFlush\Math;

class Number
{
    /**
     * @var string Number.
     */
    protected $_number;

    public const COMPARE_EQUAL = 0;
    public const COMPARE_MORE = 1;
    public const COMPARE_LESS = -1;

    final protected function __construct()
    {
        //NOOP
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->_number;
    }

    /**
     * Create self from number.
     *
     * @param   mixed       $number     Number.
     * @return  Number                  Number class.
     * @throws  Exception
     */
    public static function create($number = null)
    {
        if ($number instanceof self) {
            return clone $number;
        }

        if ($number === null) {
            $number = '0';
        }

        if (is_string($number)) {
            $number = trim($number);
            if (!preg_match('/^\-?\d+(\.\d{1,})?$/', $number)) {
                throw Exception::invalidNumber();
            }
        } elseif (is_numeric($number)) {
            $number = (string)$number;
        } else {
            throw Exception::invalidNumber();
        }

        $self = new self();
        $self->_number = $number;

        return $self;
    }

    protected function removeZeroPart(string $in): string
    {
        if (($pos = strpos($in, '.')) !== false) {
            $lowPart = '0' . substr($in, $pos);

            if ((double)$lowPart == 0) { #Is zero.
                $in = substr($in, 0, $pos);
            } else {
                $in = rtrim($in, '0');
            }
        }

        return $in;
    }

    public function isDecimal(): bool
    {
        return !$this->isZero() && strpos($this->_number, '.') !== false;
    }

    public function isInteger(): bool
    {
        return !$this->isZero() && !$this->isDecimal();
    }

    protected function pointPosition(): ?int
    {
        if (($pos = strpos($this->_number, '.')) !== false) {
            return $pos;
        }

        return null;
    }

    public function afterPoint(int $number = null)
    {
        if ($number === null) {
            if (($pos = $this->pointPosition()) !== null) {
                return strlen(
                    substr(
                        $this->_number,
                        $pos + 1
                    )
                );
            }

            return 0;
        } else {
            if ($number < 0) {
                throw Exception::invalidNumber();
            } elseif ($number > 0) {
                if ($this->isInteger() || ($isZero = $this->isZero())) {
                    if (isset($isZero) && $isZero) {
                        $this->_number = '0';
                    }

                    $this->_number = $this->_number . '.' . str_repeat('0', $number);
                } elseif ($this->isDecimal()) {
                    $count = $this->afterPoint();

                    if ($number > $count) {
                        $this->_number = $this->_number . str_repeat('0', $number - $count);
                    } elseif ($number < $count) {
                        $this->_number = substr($this->_number, 0, $this->pointPosition() + 1 + $number);
                    }
                }
            } elseif ($number === 0) {
                if (($pos = $this->pointPosition()) !== null) {
                    $this->_number = substr($this->_number, 0, $pos);
                }
            }

            return $this;
        }
    }

    protected function computeScale(self $number = null)
    {
        $firstScale = $this->afterPoint();

        if ($number === null) {
            return $firstScale;
        }

        $secondScale = $number->afterPoint();

        switch ($firstScale <=> $secondScale) {
            case 1:
                $scale = $firstScale;
                break;

            case 0:
                $scale = $firstScale;
                break;

            case -1:
                $scale = $secondScale;
                break;
        }

        return $scale;
    }

    public function add($number, int $scale = null)
    {
        $number = self::create($number);

        if ($scale === null) {
            $scale = $this->computeScale($number);
        }

        return self::create(
            $this->removeZeroPart(
                \bcadd(
                    $this->_number,
                    (string)$number,
                    $scale
                )
            )
        );
    }

    public function sub($number, int $scale = null)
    {
        $number = self::create($number);

        if ($scale === null) {
            $scale = $this->computeScale($number);
        }

        return self::create(
            $this->removeZeroPart(
                \bcsub(
                    $this->_number,
                    (string)$number,
                    $scale
                )
            )
        );
    }

    public function div($number, int $scale = null)
    {
        $number = self::create($number);

        if ($scale === null) {
            $scale = $this->computeScale($number);
        }

        return self::create(
            $this->removeZeroPart(
                \bcdiv(
                    $this->_number,
                    (string)$number,
                    $scale
                )
            )
        );
    }

    public function mul($number, int $scale = null)
    {
        $number = self::create($number);

        if ($scale === null) {
            $scale = $this->computeScale($number);
        }

        return self::create(
            $this->removeZeroPart(
                \bcmul(
                    $this->_number,
                    (string)$number,
                    $scale
                )
            )
        );
    }

    public function isZero(): bool
    {
        return (double)$this->_number == 0;
    }

    public function isNegative(): bool
    {
        return $this->_number{0} === '-' && !$this->isZero();
    }

    public function isPositive(): bool
    {
        return $this->_number{0} !== '-' && !$this->isZero();
    }

    public function toNegative(): self
    {
        if ($this->isPositive()) {
            return self::create('-' . $this->_number);
        }

        return clone $this;
    }

    public function toPositive(): self
    {
        if ($this->isNegative()) {
            return self::create(\substr($this->_number, 1));
        }

        return clone $this;
    }

    public function floor()
    {
        if ($this->isPositive()) {
            if (strpos($this->_number, '.') !== false) {
                return self::create(
                    \strtok(
                        $this->_number,
                        '.'
                    )
                );
            } else {
                return clone $this;
            }
        } else {
            return $this->toPositive()->ceil()->toNegative();
        }
    }

    public function ceil(): self
    {
        if ($this->isPositive()) {
            if (strpos($this->_number, '.') !== false) {
                return self::create(
                    \bcadd(
                        \strtok($this->_number, '.'),
                        \strval(
                            strtok($this->_number, '.') != 0
                        )
                    )
                );
            } else {
                return clone $this;
            }
        } else {
            return $this->toPositive()->floor()->toNegative();
        }
    }

    /**
     * @param   int $scale Scale.
     * @see     https://stackoverflow.com/questions/1642614/how-to-ceil-floor-and-round-bcmath-numbers
     * @return  Number
     * @throws  Exception
     */
    public function round(int $scale = 0)
    {
        $e = \bcpow('10', strval($scale + 1));
        return self::create(
            \bcdiv(
                \bcadd(
                    \bcmul(
                        $this->_number,
                        $e,
                        0
                    ),
                    ($this->isNegative() ? '-5' : '5')
                ),
                $e,
                $scale
            )
        );
    }

    public function compare($number, int $scale = null): int
    {
        $number = self::create($number);

        if ($scale === null) {
            $scale = $this->computeScale($number);
        }

        return bccomp($this->_number, strval($number), $scale);
    }

    public function isMoreThanZero(): bool
    {
        if ($this->isZero()) {
            return false;
        }

        return $this->compare('0') === self::COMPARE_MORE;
    }

    public function isLessThanZero(): bool
    {
        if ($this->isZero()) {
            return false;
        }

        return $this->compare('0') === self::COMPARE_LESS;
    }

    public function isLessThan($number, int $scale = null): bool
    {
        return $this->compare($number, $scale) === self::COMPARE_LESS;
    }

    public function isEqual($number, int $scale = null): bool
    {
        return $this->compare($number, $scale) === self::COMPARE_EQUAL;
    }

    public function isMoreThan($number, int $scale = null): bool
    {
        return $this->compare($number, $scale) === self::COMPARE_MORE;
    }

    public function mod($divisor, int $scale = null)
    {
        $number = self::create($divisor);

        if ($scale === null) {
            $scale = $this->computeScale($number);
        }

        return self::create(
            $this->removeZeroPart(
                \bcmod(
                    $this->_number,
                    strval($number),
                    $scale
                )
            )
        );
    }

    public function sqrt(int $scale = null)
    {
        if ($scale === null) {
            $scale = $this->computeScale();
        }

        return self::create(
            $this->removeZeroPart(
                \bcsqrt(
                    strval($this->toPositive()),
                    $scale
                )
            )
        );
    }

    public function pow(int $exp, int $scale = null)
    {
        if ($scale === null) {
            $scale = $this->computeScale();
        }

        return self::create(
            $this->removeZeroPart(
                \bcpow(
                    $this->_number,
                    (string)$exp,
                    $scale
                )
            )
        );
    }

    public function powmod(int $exp, $module, int $scale = null)
    {
        $number = self::create($module);

        if ($scale === null) {
            $scale = $this->computeScale();
        }

        return self::create(
            $this->removeZeroPart(
                \bcpowmod(
                    $this->_number,
                    (string)$exp,
                    strval($number),
                    $scale
                )
            )
        );
    }
}
