<?php declare(strict_types=1);

namespace Libtelex\Telex2\RuleSet;

use Libtelex\Telex2\RuleSetInterface;
use Libtelex\Telex2\StringObject;
use Libtelex\Telex2\TelephoneNumber;
use Libtelex\Telex2\Utils;

use function array_keys;
use function implode;

use const false;
use const null;
use const true;

/**
 * See:
 * - https://en.wikipedia.org/wiki/Telephone_numbers_in_Switzerland
 * - https://krispcall.com/blog/switzerland-phone-number-format/
 */
final class Switzerland implements RuleSetInterface
{
    /**
     * Complete area codes.  3 digits, includes the trunk code.
     *
     * Initial source: https://countrycode.org/switzerland
     *
     * @var array<string,string>
     */
    private const array AREA_CODES = [
        '062' => 'Ammerswil (Aarau)',
        '041' => 'Andermatt, Lucerne, Zug',
        '081' => 'Arosa, Davos, Klosters, St. Moritz',
        '056' => 'Baden',
        '061' => 'Basel',
        '091' => 'Bellinzona, Chiasso, Locarno, Lugano',
        '031' => 'Berne',
        '032' => 'Biel (Bienne), La Chaux-de-Fonds, Neuchatel',
        '027' => 'Crans-sur-Sierre, Zermatt',
        '026' => 'Fribourg',
        '022' => 'Geneva',
        '024' => 'Gryon (Yverdon-les-Bains)',
        '033' => 'Gstaad, Interlaken, Lenk im Simmental, Obewil im Simmental, Wengen',
        '021' => 'Lausanne, Montreux, Vevey',
        '052' => 'Schaffhausen, Winterthur',
        '071' => 'St. Gallen',
        '043' => 'Zurich',
    ];

    /**
     * @override
     */
    public function getIsoAlpha2CountryCode(): string
    {
        return 'CH';
    }

    /**
     * @override
     */
    public function getCountryCallingCode(): string
    {
        return '41';
    }

    /**
     * @override
     */
    public function getTrunkCode(): string
    {
        return '0';
    }

    /**
     * @override
     */
    public function getNationalNumberLength(): int
    {
        return 10;
    }

    /**
     * See https://en.wikipedia.org/wiki/National_conventions_for_writing_telephone_numbers#Switzerland
     *
     * @override
     */
    public function formatIntl(TelephoneNumber $telephoneNumber): bool
    {
        // (By this point, the national number should contain only digits and be the correct length)
        $nationalNumber = $telephoneNumber->getNationalNumber();

        $telephoneNumberType = match (true) {
            Utils::startsWith(array_keys(self::AREA_CODES), $nationalNumber)
                => TelephoneNumber::TYPE_FIXED_LINE,

            Utils::startsWith('07[4-9]', $nationalNumber)
                => TelephoneNumber::TYPE_MOBILE,

            Utils::startsWith('058|0800|084[0248]', $nationalNumber)
                => TelephoneNumber::TYPE_NON_GEOGRAPHIC,

            default
                => null,
        };

        if (!$telephoneNumberType) {
            $telephoneNumber->noMatchForCountry();

            return false;
        }

        $countryCallingCode = $this->getCountryCallingCode();

        $numberClusters = Utils::chunk($nationalNumber, patternOrLength: [3, 3, 2, 2]);

        $numberClusters[0] = (new StringObject($numberClusters[0]))
            ->deleteLeft($this->getTrunkCode())
            ->getValue()
        ;

        $telephoneNumber->matchForCountry(
            $this->getIsoAlpha2CountryCode(),
            $countryCallingCode,
            $telephoneNumberType,
            "+{$countryCallingCode} " . implode(' ', $numberClusters),
        );

        return true;
    }
}
