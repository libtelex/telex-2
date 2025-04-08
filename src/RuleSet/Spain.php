<?php declare(strict_types=1);

namespace Libtelex\Telex2\RuleSet;

use Libtelex\Telex2\RuleSetInterface;
use Libtelex\Telex2\TelephoneNumber;
use Libtelex\Telex2\Utils;

use function implode;
use function str_split;

use const false;
use const null;
use const true;

/**
 * See https://en.wikipedia.org/wiki/Telephone_numbers_in_Spain#Current_numbering_plan
 */
final class Spain implements RuleSetInterface
{
    /**
     * 3 digits
     *
     * Initial source: https://countrycode.org/countryCode/downloadCityCodes?country=ES
     *
     * See also: https://en.wikipedia.org/wiki/Telephone_numbers_in_Spain#Area_codes
     *
     * @var array<string,string>
     */
    private const array AREA_CODE_PATTERNS = [
        'Geographic expansion'       => '8[1-9]\d',
        'A Coruña'                   => '981',
        'Alava'                      => '945',
        'Albacete'                   => '967',
        'Alicante'                   => '96[5-6]',
        'Almeria'                    => '950',
        'Asturias'                   => '985',
        'Ávila'                      => '920',
        'Badajoz'                    => '924',
        'Baleares'                   => '971',
        // 'Palma De Mallorca'          => '971',
        'Barcelona'                  => '93[1-8]',
        // 'Igualada'                   => '93',
        // 'Bilbao'                     => '94',
        // 'Vizcaya'                    => '94',
        'Biscay (Bizkaia/Vizcaya)'   => '94[46]',
        'Burgos'                     => '947',
        'Caceres'                    => '927',
        'Cadiz'                      => '956',
        'Cantabria'                  => '942',
        // 'Santander'                  => '942',
        'Castellon'                  => '964',
        'Cordoba'                    => '957',
        'Cuenca'                     => '969',
        'Girona'                     => '972',
        'Granada'                    => '958',
        'Guadalajara'                => '949',
        'Guipuzcoa'                  => '943',
        'Huelva'                     => '959',
        'Huesca'                     => '974',
        'Jaen'                       => '953',
        'La Rioja'                   => '941',
        'Las Palmas'                 => '928',
        // 'Las Palmas de Gran Canaria' => '928',
        'León'                       => '987',
        'Lerida'                     => '973',
        'Lugo'                       => '982',
        'Madrid'                     => '91[1-8]',
        'Málaga'                     => '95[12]',
        // 'Ceuta'                      => '952',
        // 'Melilla'                    => '952',
        // 'Torremolinos'               => '952',
        'Murcia'                     => '968',
        'Navarra'                    => '948',
        // 'Pamplona'                   => '948',
        'Orense'                     => '988',
        'Palencia'                   => '979',
        'Pontevedra'                 => '986',
        'Salamanca'                  => '923',
        'Santa Cruz de Tenerife'     => '922',
        // 'Tenerife'                   => '922',
        'Segovia'                    => '921',
        'Seville'                    => '95[45]',
        'Soria'                      => '975',
        'Tarragona'                  => '977',
        'Toledo'                     => '925',
        'Turuel'                     => '978',
        'Valencia'                   => '96[0-3]',
        'Valladolid'                 => '983',
        'Zamora'                     => '980',
        'Zaragoza'                   => '976',
    ];

    /**
     * @override
     */
    public function getCountryCallingCode(): string
    {
        return '34';
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
        return 9;
    }

    /**
     * See https://en.wikipedia.org/wiki/National_conventions_for_writing_telephone_numbers#Spain
     *
     * @override
     */
    public function formatIntl(TelephoneNumber $telephoneNumber): bool
    {
        // (By this point, the national number should contain only digits and be the correct length)
        $nationalNumber = $telephoneNumber->getNationalNumber();

        $telephoneNumberType = match (true) {
            Utils::startsWith(self::AREA_CODE_PATTERNS, $nationalNumber)
                => TelephoneNumber::TYPE_FIXED_LINE,

            Utils::startsWith('(?:6|7[1-9])', $nationalNumber)
                => TelephoneNumber::TYPE_MOBILE,

            default
                => null,
        };

        if (!$telephoneNumberType) {
            $telephoneNumber->noMatchForCountry();

            return false;
        }

        $numberClusters = [];

        if (TelephoneNumber::TYPE_FIXED_LINE === $telephoneNumberType && Utils::startsWith('(?:91|93|94|95|96)', $nationalNumber)) {
            // (Special fixed-line-only format)
            $numberClusters = Utils::chunk($nationalNumber, patternOrLength: [2, 3, 2, 2]);
        } else {
            // Use the default, 3-3-3 format
            $numberClusters = str_split($nationalNumber, 3);
        }

        $countryCallingCode = $this->getCountryCallingCode();

        $telephoneNumber->matchForCountry(
            $countryCallingCode,
            $telephoneNumberType,
            "+{$countryCallingCode} " . implode(' ', $numberClusters),
        );

        return true;
    }
}
