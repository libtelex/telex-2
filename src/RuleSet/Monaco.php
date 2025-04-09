<?php declare(strict_types=1);

namespace Libtelex\Telex2\RuleSet;

use Libtelex\Telex2\RuleSetInterface;
use Libtelex\Telex2\TelephoneNumber;

use function implode;
use function str_starts_with;
use function str_split;

use const false;
use const null;
use const true;

/**
 * See https://en.wikipedia.org/wiki/Telephone_numbers_in_Monaco
 */
final class Monaco implements RuleSetInterface
{
    /**
     * @override
     */
    public function getIsoAlpha2CountryCode(): string
    {
        return 'MC';
    }

    /**
     * @override
     */
    public function getCountryCallingCode(): string
    {
        return '377';
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
     * The [Monaco government](https://en.gouv.mc/layout/set/popup/Government-Institutions/The-Government/The-Ministry-of-State#Coordonnees) --
     * at least -- employs the format commonly used in France
     *
     * @override
     */
    public function formatIntl(TelephoneNumber $telephoneNumber): bool
    {
        // (By this point, the national number should contain only digits and be the correct length)
        $nationalNumber = $telephoneNumber->getNationalNumber();

        $telephoneNumberType = match (true) {
            str_starts_with($nationalNumber, '9')
                => TelephoneNumber::TYPE_FIXED_LINE,

            str_starts_with($nationalNumber, '6')
                => TelephoneNumber::TYPE_MOBILE,

            default
                => null,
        };

        if (!$telephoneNumberType) {
            $telephoneNumber->noMatchForCountry();

            return false;
        }

        $countryCallingCode = $this->getCountryCallingCode();
        $numberClusters = str_split($nationalNumber, 2);

        $telephoneNumber->matchForCountry(
            $this->getIsoAlpha2CountryCode(),
            $countryCallingCode,
            $telephoneNumberType,
            "+{$countryCallingCode} " . implode(' ', $numberClusters),
        );

        return true;
    }
}
