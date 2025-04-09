<?php declare(strict_types=1);

namespace Libtelex\Telex2\Tests;

use Libtelex\Telex2\TelephoneNumber;
use Libtelex\Telex2\Telex2;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const null;

class Telex2Test extends TestCase
{
    /**
     * @return array<mixed[]>
     * @todo Add a variety of non-geographic numbers for each country
     */
    public static function providesTelephoneNumbers(): array
    {
        return [
            // #########> Spain #########
            [
                null,
                '+34 (0)661 745 414',  // Invalid because Spain doesn't use a trunk code
            ],
            [
                null,
                '+34 (0) 689 469 899',  // Invalid because Spain doesn't use a trunk code
            ],
            [
                (new TelephoneNumber('+34 971 897 542'))
                    ->setMainNumbers('34', '971897542')
                    ->matchForCountry('ES', '34', TelephoneNumber::TYPE_FIXED_LINE, '+34 971 897 542')
                ,
                '+34 971 897 542',
            ],
            [
                (new TelephoneNumber('+34 644 450 285'))
                    ->setMainNumbers('34', '644450285')
                    ->matchForCountry('ES', '34', TelephoneNumber::TYPE_MOBILE, '+34 644 450 285')
                ,
                '+34 644 450 285',
            ],
            [
                (new TelephoneNumber('+34 902 101 001'))
                    ->setMainNumbers('34', '902101001')
                    ->matchForCountry('ES', '34', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+34 902 101 001')
                ,
                '+34 902 101 001',
            ],
            // #########< Spain #########

            // #########> France #########
            [
                null,
                '+33 (0)4 67 30 33 12 36',  // Invalid: too long
            ],
            // @todo 1 x invalid
            [
                (new TelephoneNumber('+33 (0)4 50 79 09 41'))
                    ->setMainNumbers('33', '0450790941')
                    ->matchForCountry('FR', '33', TelephoneNumber::TYPE_FIXED_LINE, '+33 4 50 79 09 41')
                ,
                '+33 (0)4 50 79 09 41',
            ],
            [
                (new TelephoneNumber('+33 6 34 04 04 67'))
                    ->setMainNumbers('33', '0634040467')
                    ->matchForCountry('FR', '33', TelephoneNumber::TYPE_MOBILE, '+33 6 34 04 04 67')
                ,
                '+33 6 34 04 04 67',
            ],
            [
                (new TelephoneNumber('+33 (0)9 83 02 14 16'))
                    ->setMainNumbers('33', '0983021416')
                    ->matchForCountry('FR', '33', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+33 9 83 02 14 16')
                ,
                '+33 (0)9 83 02 14 16',
            ],
            // #########< France #########

            // #########> Switzerland #########
            // @todo 2 x invalid
            [
                (new TelephoneNumber('+41 (0) 277 71 24 58'))
                    ->setMainNumbers('41', '0277712458')
                    ->matchForCountry('CH', '41', TelephoneNumber::TYPE_FIXED_LINE, '+41 27 771 24 58')
                ,
                '+41 (0) 277 71 24 58',
            ],
            [
                (new TelephoneNumber('+41 (0)76 575 23 94'))
                    ->setMainNumbers('41', '0765752394')
                    ->matchForCountry('CH', '41', TelephoneNumber::TYPE_MOBILE, '+41 76 575 23 94')
                ,
                '+41 (0)76 575 23 94',
            ],
            [
                (new TelephoneNumber('0041 800 333 313'))
                    ->setMainNumbers('41', '0800333313')
                    ->matchForCountry('CH', '41', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+41 80 033 33 13')
                ,
                '0041 800 333 313',
            ],
            // #########< Switzerland #########

            // #########> Italy #########
            // @todo 2 x invalid
            [
                (new TelephoneNumber('+39 (0)3 93 94 21'))
                    ->setMainNumbers('39', '03939421')
                    ->matchForCountry('IT', '39', TelephoneNumber::TYPE_FIXED_LINE, '+39 039 39421')
                ,
                '+39 (0)3 93 94 21',
            ],
            [
                (new TelephoneNumber('+39 351 6784830'))
                    ->setMainNumbers('39', '3516784830')
                    ->matchForCountry('IT', '39', TelephoneNumber::TYPE_MOBILE, '+39 3516 784830')
                ,
                '+39 351 6784830',
            ],
            // @todo 1 x non-geographic
            // #########< Italy #########

            // #########> Monaco #########
            // @todo 2 x invalid
            [
                (new TelephoneNumber('+377 93 15 36 00'))
                    ->setMainNumbers('377', '93153600')
                    ->matchForCountry('MC', '377', TelephoneNumber::TYPE_FIXED_LINE, '+377 93 15 36 00')
                ,
                '+377 93 15 36 00',
            ],
            [
                (new TelephoneNumber('+377 93 50 12 12'))
                    ->setMainNumbers('377', '93501212')
                    ->matchForCountry('MC', '377', TelephoneNumber::TYPE_FIXED_LINE, '+377 93 50 12 12')
                ,
                '+377 93 50 12 12',
            ],
            // #########< Monaco #########

            // #########> United Kingdom #########
            // @todo 2 x invalid
            [
                (new TelephoneNumber('+44 (0)1572 823352'))
                    ->setMainNumbers('44', '01572823352')
                    ->matchForCountry('GB', '44', TelephoneNumber::TYPE_FIXED_LINE, '+44 1572 823352')
                ,
                '+44 (0)1572 823352',
            ],
            [
                (new TelephoneNumber('+44 (0)785 056 6978'))
                    ->setMainNumbers('44', '07850566978')
                    ->matchForCountry('GB', '44', TelephoneNumber::TYPE_MOBILE, '+44 7850 566978')
                ,
                '+44 (0)785 056 6978',
            ],
            [
                (new TelephoneNumber('+44 (0)345 021 0222'))
                    ->setMainNumbers('44', '03450210222')
                    ->matchForCountry('GB', '44', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+44 345 021 0222')
                ,
                '+44 (0)345 021 0222',
            ],
            // #########< United Kingdom #########

            // #########> Denmark #########
            // @todo 2 x invalid
            [
                (new TelephoneNumber('+45 35 36 66 00'))
                    ->setMainNumbers('45', '35366600')
                    ->matchForCountry('DK', '45', TelephoneNumber::TYPE_FIXED_LINE, '+45 35 36 66 00')
                ,
                '+45 35 36 66 00',
            ],
            [
                (new TelephoneNumber('+45 40682739'))
                    ->setMainNumbers('45', '40682739')
                    ->matchForCountry('DK', '45', TelephoneNumber::TYPE_MOBILE, '+45 40 68 27 39')
                ,
                '+45 40682739',
            ],
            [
                (new TelephoneNumber('+45 70 10 50 95'))
                    ->setMainNumbers('45', '70105095')
                    ->matchForCountry('DK', '45', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+45 70 10 50 95')
                ,
                '+45 70 10 50 95',
            ],
            // #########< Denmark #########
        ];
    }

    #[DataProvider('providesTelephoneNumbers')]
    public function testMatch(
        TelephoneNumber|null $expected,
        string $input,
    ): void {
        $this->assertEquals(
            $expected,
            (new Telex2())->match($input),
        );
    }

    public function testFormatintlIsAConvenienceMethodThatCallsMatch(): void
    {
        $input = '0044 (0)1234 567890';
        $output = '+44 1234 567890';

        $mockTelephoneNumber = $this
            ->getMockBuilder(TelephoneNumber::class)
            ->onlyMethods(['getFormatted'])
            ->setConstructorArgs([$input])
            ->getMock()
        ;

        $mockTelephoneNumber
            ->expects($this->once())
            ->method('getFormatted')
            ->willReturn($output)
        ;

        $mockTelex = $this
            ->getMockBuilder(Telex2::class)
            ->onlyMethods(['match'])
            ->getMock()
        ;

        $mockTelex
            ->expects($this->once())
            ->method('match')
            ->with($input)
            ->willReturn($mockTelephoneNumber)
        ;

        $this->assertSame(
            $output,
            $mockTelex->formatintl($input),
        );
    }

    /** @return array<mixed[]> */
    public static function providesFormattedTelephoneNumbers(): array
    {
        return [
            [null, '+34 (0)971 897 542'],
            [null, '+34 492 023 377'],  // Reserved number
            [null, '+34 971 427 07'],  // Too short
            ['+34 91 720 67 10', '(+34) 917 206 710'],
            ['+34 871 716 162', '(00 34) 871 716 162'],
            ['+34 902 404 444', '+34 902 404 444'],
            ['+34 902 343 432', '+34 902 343 432'],
            ['+34 871 530 363', '871530363'],
            ['+34 971 670 238', '(971) 670 238'],

            ['+33 8 10 00 80 60', '+33 (0)8 10 00 80 60'],
            ['+33 4 90 86 16 50', '+33(0)4 90 86 16 50'],

            ['+41 58 866 42 30', '+41 (0)5 88 66 42 30'],
            ['+41 84 809 10 91', '+41 (0)8 48 09 10 91'],

            ['+39 338 2583328', '+39 3 38 25 83 328'],

            ['+44 20 7223 1200', '+44 (0)20 7223 1200'],
            ['+44 118 907 1816', '+44 (0)118 907 1816'],
            ['+44 1844 215822', '+44 (0)184 421 5822'],
            ['+44 1752 837734', '+44 (0)1752 837 734'],
            ['+44 1786 833908', '+44 (0) 1786 833 908'],
            ['+44 1654 634123', '+44 (0)1654 634 123'],
            ['+44 800 410 1181', '+44 (0)800 410 1181'],
            ['+44 845 519 2494', '+44 (0)845 519 2494'],
            ['+44 870 754 1779', '+44 (0)8707 541779'],

            ['+45 28 49 52 05', '+45 28 49 52 05'],

            [null, '+32 (0)2 583 19 43'],  // Unsupported at present
        ];
    }

    #[DataProvider('providesFormattedTelephoneNumbers')]
    public function testCanFormatATelephoneNumberInASingleCall(
        string|null $expected,
        string $input,
    ): void {
        $this->assertSame(
            $expected,
            (new Telex2())->formatIntl($input),
        );
    }
}
