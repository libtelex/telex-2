<?php declare(strict_types=1);

namespace Libtelex\Telex2\Tests;

use Libtelex\Telex2\StringObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Stringable;

class StringObjectTest extends TestCase
{
    public function testIsInstantiable(): void
    {
        $stringObject = new StringObject('foo');

        $this->assertSame('foo', $stringObject->getValue());
        $this->assertSame($stringObject->getValue(), (string) $stringObject);
    }

    public function testIsStringable(): void
    {
        $class = new ReflectionClass(StringObject::class);

        $this->assertTrue($class->implementsInterface(Stringable::class));
    }

    /** @return array<mixed[]> */
    public static function providesStringsContainingDigits(): array
    {
        return [
            'Blank' => [
                /* Expected output => */'',
                /* Input => */          '',
            ],
            'Only digits' => [
                '1234567890',
                '1234567890',
            ],
            'Alphanumeric' => [
                '123',
                '1a2b3c',
            ],
            'Telephone number' => [
                '4401234567890',
                '+44 (0)1234 567890',
            ],
        ];
    }

    #[DataProvider('providesStringsContainingDigits')]
    public function testOnlydigitsReturnsANewInstanceContainingOnlyTheDigitsFromTheSourceString(
        string $expectedValue,
        string $inputString,
    ): void {
        $stringObject = new StringObject($inputString);
        $something = $stringObject->onlyDigits();

        $this->assertInstanceOf(StringObject::class, $something);
        $this->assertNotSame($stringObject, $something);
        $this->assertSame($expectedValue, $something->getValue());
    }

    /** @return array<mixed[]> */
    public static function providesStringMeta(): array
    {
        return [
            [
                /* Expected length => */0,
                /* Input => */          '',
            ],
            [
                1,
                'a',
            ],
            [
                2,
                '12',
            ],
        ];
    }

    #[DataProvider('providesStringMeta')]
    public function testGetlengthReturnsTheNumberOfCharsInTheSourceString(
        int $expectedLength,
        string $inputString,
    ): void {
        $stringObject = new StringObject($inputString);

        $this->assertSame($expectedLength, $stringObject->getLength());
    }

    /** @return array<mixed[]> */
    public static function providesStringsToLeftDelete(): array
    {
        return [
            [
                '',
                '',
                'foo',
            ],
            [
                '',
                'foo',
                'foo',
            ],
            [
                'bar',
                'foobar',
                'foo',
            ],
            [
                'foobar',
                'foobar',
                'bar',
            ],
        ];
    }

    #[DataProvider('providesStringsToLeftDelete')]
    public function testDeleteleftRemovesTheSubstringFromTheStartOfTheSourceString(
        string $expectedValue,
        string $inputString,
        string $stringToDelete,
    ): void {
        $stringObject = new StringObject($inputString);
        $something = $stringObject->deleteLeft($stringToDelete);

        $this->assertInstanceOf(StringObject::class, $something);
        $this->assertNotSame($stringObject, $something);
        $this->assertSame($expectedValue, $something->getValue());
    }
}
