<?php declare(strict_types=1);

namespace Libtelex\Telex2\Tests;

use Libtelex\Telex2\TelephoneNumber;
use Libtelex\Telex2\Telex2;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const null;

class Telex2Test extends TestCase
{
    /** @return array<mixed[]> */
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
                null,
                '+34 (0)971 897 542',  // Invalid because Spain doesn't use a trunk code
            ],
            [
                (new TelephoneNumber('+34 644 450 285'))
                    ->setMainNumbers('34', '644450285')
                    ->matchForCountry('34', TelephoneNumber::TYPE_MOBILE, '+34 644 450 285')
                ,
                '+34 644 450 285',
            ],
            [
                (new TelephoneNumber('+34 971 897 542'))
                    ->setMainNumbers('34', '971897542')
                    ->matchForCountry('34', TelephoneNumber::TYPE_FIXED_LINE, '+34 971 897 542')
                ,
                '+34 971 897 542',
            ],
            [
                (new TelephoneNumber('(+34) 917 206 710'))
                    ->setMainNumbers('34', '917206710')
                    ->matchForCountry('34', TelephoneNumber::TYPE_FIXED_LINE, '+34 91 720 67 10')
                ,
                '(+34) 917 206 710',  // (Atypical format)
            ],
            [
                (new TelephoneNumber('(00 34) 871 716 162'))
                    ->setMainNumbers('34', '871716162')
                    ->matchForCountry('34', TelephoneNumber::TYPE_FIXED_LINE, '+34 871 716 162')
                ,
                '(00 34) 871 716 162',
            ],
            [
                null,
                '+34 492 023 377',  // Invalid: reserved number
            ],
            // #########< Spain #########

            // #########> France #########
            [
                null,
                '+33 (0)4 67 30 33 12 36',  // Invalid: too long
            ],
            [
                (new TelephoneNumber('+33 (0)4 50 79 09 41'))
                    ->setMainNumbers('33', '0450790941')
                    ->matchForCountry('33', TelephoneNumber::TYPE_FIXED_LINE, '+33 4 50 79 09 41')
                ,
                '+33 (0)4 50 79 09 41',
            ],
            [
                (new TelephoneNumber('+33(0)4 90 86 16 50'))
                    ->setMainNumbers('33', '0490861650')
                    ->matchForCountry('33', TelephoneNumber::TYPE_FIXED_LINE, '+33 4 90 86 16 50')
                ,
                '+33(0)4 90 86 16 50',
            ],
            [
                (new TelephoneNumber('+33 6 34 04 04 67'))
                    ->setMainNumbers('33', '0634040467')
                    ->matchForCountry('33', TelephoneNumber::TYPE_MOBILE, '+33 6 34 04 04 67')
                ,
                '+33 6 34 04 04 67',
            ],
            [
                (new TelephoneNumber('+33 (0)9 83 02 14 16'))
                    ->setMainNumbers('33', '0983021416')
                    ->matchForCountry('33', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+33 9 83 02 14 16')
                ,
                '+33 (0)9 83 02 14 16',
            ],
            [
                (new TelephoneNumber('+33 (0)8 10 00 80 60'))
                    ->setMainNumbers('33', '0810008060')
                    ->matchForCountry('33', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+33 8 10 00 80 60')
                ,
                '+33 (0)8 10 00 80 60',
            ],
            // #########< France #########

            // #########> Switzerland #########
            [
                (new TelephoneNumber('+41 (0) 277 71 24 58'))
                    ->setMainNumbers('41', '0277712458')
                    ->matchForCountry('41', TelephoneNumber::TYPE_FIXED_LINE, '+41 27 771 24 58')
                ,
                '+41 (0) 277 71 24 58',
            ],
            [
                (new TelephoneNumber('+41 (0)76 575 23 94'))
                    ->setMainNumbers('41', '0765752394')
                    ->matchForCountry('41', TelephoneNumber::TYPE_MOBILE, '+41 76 575 23 94')
                ,
                '+41 (0)76 575 23 94',
            ],
            [
                (new TelephoneNumber('0041 800 333 313'))
                    ->setMainNumbers('41', '0800333313')
                    ->matchForCountry('41', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+41 80 033 33 13')
                ,
                '0041 800 333 313',
            ],
            [
                (new TelephoneNumber('+41 (0)5 88 66 42 30'))
                    ->setMainNumbers('41', '0588664230')
                    ->matchForCountry('41', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+41 58 866 42 30')
                ,
                '+41 (0)5 88 66 42 30',
            ],
            [
                (new TelephoneNumber('+41 (0)8 48 09 10 91'))
                    ->setMainNumbers('41', '0848091091')
                    ->matchForCountry('41', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+41 84 809 10 91')
                ,
                '+41 (0)8 48 09 10 91',
            ],
            // #########< Switzerland #########

            // #########> Italy #########
            [
                (new TelephoneNumber('+39 (0)3 93 94 21'))
                    ->setMainNumbers('39', '03939421')
                    ->matchForCountry('39', TelephoneNumber::TYPE_FIXED_LINE, '+39 039 39421')
                ,
                '+39 (0)3 93 94 21',
            ],
            [
                (new TelephoneNumber('+39 351 6784830'))
                    ->setMainNumbers('39', '3516784830')
                    ->matchForCountry('39', TelephoneNumber::TYPE_MOBILE, '+39 3516 784830')
                ,
                '+39 351 6784830',
            ],
            [
                (new TelephoneNumber('+39 3 38 25 83 328'))
                    ->setMainNumbers('39', '3382583328')
                    ->matchForCountry('39', TelephoneNumber::TYPE_MOBILE, '+39 338 2583328')
                ,
                '+39 3 38 25 83 328',
            ],
            // #########< Italy #########

            // #########> Monaco #########
            [
                (new TelephoneNumber('+377 93 15 36 00'))
                    ->setMainNumbers('377', '93153600')
                    ->matchForCountry('377', TelephoneNumber::TYPE_FIXED_LINE, '+377 93 15 36 00')
                ,
                '+377 93 15 36 00',
            ],
            [
                (new TelephoneNumber('+377 93 50 12 12'))
                    ->setMainNumbers('377', '93501212')
                    ->matchForCountry('377', TelephoneNumber::TYPE_FIXED_LINE, '+377 93 50 12 12')
                ,
                '+377 93 50 12 12',
            ],
            // #########< Monaco #########

            // #########> United Kingdom #########
            [
                (new TelephoneNumber('+44 (0)785 056 6978'))
                    ->setMainNumbers('44', '07850566978')
                    ->matchForCountry('44', TelephoneNumber::TYPE_MOBILE, '+44 7850 566978')
                ,
                '+44 (0)785 056 6978',
            ],
            [
                (new TelephoneNumber('+44 (0)1572 823352'))
                    ->setMainNumbers('44', '01572823352')
                    ->matchForCountry('44', TelephoneNumber::TYPE_FIXED_LINE, '+44 1572 823352')
                ,
                '+44 (0)1572 823352',
            ],
            [
                (new TelephoneNumber('+44 (0)20 7223 1200'))
                    ->setMainNumbers('44', '02072231200')
                    ->matchForCountry('44', TelephoneNumber::TYPE_FIXED_LINE, '+44 20 7223 1200')
                ,
                '+44 (0)20 7223 1200',
            ],
            [
                (new TelephoneNumber('+44 (0)118 907 1816'))
                    ->setMainNumbers('44', '01189071816')
                    ->matchForCountry('44', TelephoneNumber::TYPE_FIXED_LINE, '+44 118 907 1816')
                ,
                '+44 (0)118 907 1816',
            ],
            [
                (new TelephoneNumber('+44 (0)184 421 5822'))
                    ->setMainNumbers('44', '01844215822')
                    ->matchForCountry('44', TelephoneNumber::TYPE_FIXED_LINE, '+44 1844 215822')
                ,
                '+44 (0)184 421 5822',
            ],
            [
                (new TelephoneNumber('+44 (0)1752 837 734'))
                    ->setMainNumbers('44', '01752837734')
                    ->matchForCountry('44', TelephoneNumber::TYPE_FIXED_LINE, '+44 1752 837734')
                ,
                '+44 (0)1752 837 734',  // ???
            ],
            [
                (new TelephoneNumber('+44 (0) 1786 833 908'))
                    ->setMainNumbers('44', '01786833908')
                    ->matchForCountry('44', TelephoneNumber::TYPE_FIXED_LINE, '+44 1786 833908')
                ,
                '+44 (0) 1786 833 908',  // ???
            ],
            [
                (new TelephoneNumber('+44 (0)1654 634 123'))
                    ->setMainNumbers('44', '01654634123')
                    ->matchForCountry('44', TelephoneNumber::TYPE_FIXED_LINE, '+44 1654 634123')
                ,
                '+44 (0)1654 634 123',  // ???
            ],
            [
                (new TelephoneNumber('+44 (0)345 021 0222'))
                    ->setMainNumbers('44', '03450210222')
                    ->matchForCountry('44', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+44 345 021 0222')
                ,
                '+44 (0)345 021 0222',
            ],
            [
                (new TelephoneNumber('+44 (0)800 410 1181'))
                    ->setMainNumbers('44', '08004101181')
                    ->matchForCountry('44', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+44 800 410 1181')
                ,
                '+44 (0)800 410 1181',
            ],
            [
                (new TelephoneNumber('+44 (0)845 519 2494'))
                    ->setMainNumbers('44', '08455192494')
                    ->matchForCountry('44', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+44 845 519 2494')
                ,
                '+44 (0)845 519 2494',
            ],
            [
                (new TelephoneNumber('+44 (0)8707 541779'))
                    ->setMainNumbers('44', '08707541779')
                    ->matchForCountry('44', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+44 870 754 1779')
                ,
                '+44 (0)8707 541779',
            ],
            // #########< United Kingdom #########

            // #########> Denmark #########
            [
                (new TelephoneNumber('+45 70 10 50 95'))
                    ->setMainNumbers('45', '70105095')
                    ->matchForCountry('45', TelephoneNumber::TYPE_NON_GEOGRAPHIC, '+45 70 10 50 95')
                ,
                '+45 70 10 50 95',
            ],
            [
                (new TelephoneNumber('+45 40682739'))
                    ->setMainNumbers('45', '40682739')
                    ->matchForCountry('45', TelephoneNumber::TYPE_MOBILE, '+45 40 68 27 39')
                ,
                '+45 40682739',
            ],
            [
                (new TelephoneNumber('+45 28 49 52 05'))
                    ->setMainNumbers('45', '28495205')
                    ->matchForCountry('45', TelephoneNumber::TYPE_MOBILE, '+45 28 49 52 05')
                ,
                '+45 28 49 52 05',
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
}
