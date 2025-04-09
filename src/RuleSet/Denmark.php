<?php declare(strict_types=1);

namespace Libtelex\Telex2\RuleSet;

use Libtelex\Telex2\RuleSetInterface;
use Libtelex\Telex2\TelephoneNumber;
use Libtelex\Telex2\Utils;

use function implode;
use function str_split;
use function str_starts_with;

use const false;
use const null;
use const true;

/**
 * See https://en.wikipedia.org/wiki/Telephone_numbers_in_Denmark
 */
final class Denmark implements RuleSetInterface
{
    /**
     * @var string[]
     */
    private const array FIXED_LINE_PREFIXES = [
        '3[2-68-9]',
        '4[3-9]',
        '5[4-9]',
        '6[2-69]',
        '7[2-9]',
        '82',
        '8[6-9]',
        '9[6-9]',
    ];

    /**
     * @var string[]
     */
    private const array MOBILE_PREFIXES = [
        '2[0-9]',
        '3[01]',
        '4[0-2]',
        '4911',
        '5[0-5]',
        '6[01]',
        '71',
        '81',
        '9[1-3]',
    ];

    /**
     * @override
     */
    public function getIsoAlpha2CountryCode(): string
    {
        return 'DK';
    }

    /**
     * @override
     */
    public function getCountryCallingCode(): string
    {
        return '45';
    }

    /**
     * @override
     */
    public function getTrunkCode(): string
    {
        return '';
    }

    /**
     * @override
     */
    public function getNationalNumberLength(): int
    {
        return 8;
    }

    /**
     * See https://en.wikipedia.org/wiki/National_conventions_for_writing_telephone_numbers#Denmark
     *
     * @override
     */
    public function formatIntl(TelephoneNumber $telephoneNumber): bool
    {
        // (By this point, the national number should contain only digits and be the correct length)
        $nationalNumber = $telephoneNumber->getNationalNumber();

        $telephoneNumberType = match (true) {
            Utils::startsWith(self::FIXED_LINE_PREFIXES, $nationalNumber)
                => TelephoneNumber::TYPE_FIXED_LINE,

            Utils::startsWith(self::MOBILE_PREFIXES, $nationalNumber)
                => TelephoneNumber::TYPE_MOBILE,

            str_starts_with($nationalNumber, '70')
                => TelephoneNumber::TYPE_NON_GEOGRAPHIC,

            default
                => null,
        };

        if (!$telephoneNumberType) {
            $telephoneNumber->noMatchForCountry();

            return false;
        }

        $countryCallingCode = $this->getCountryCallingCode();

        $telephoneNumber->matchForCountry(
            $this->getIsoAlpha2CountryCode(),
            $countryCallingCode,
            $telephoneNumberType,
            "+{$countryCallingCode} " . implode(' ', str_split($nationalNumber, 2)),
        );

        return true;
    }
}
