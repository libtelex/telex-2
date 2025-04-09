<?php declare(strict_types=1);

namespace Libtelex\Telex2;

interface RuleSetInterface
{
    /**
     * Returns the ISO 3166-1 alpha-2 code of the country to which the rule-set relates
     */
    public function getIsoAlpha2CountryCode(): string;

    /**
     * Returns the ITU-T E.164 code
     *
     * @todo Rename this?
     */
    public function getCountryCallingCode(): string;

    /**
     * Returns the [trunk] code that would be used in making national calls
     */
    public function getTrunkCode(): string;

    /**
     * A 'national number' includes the trunk code, if the country has one
     *
     * @return int|int[]
     */
    public function getNationalNumberLength(): int|array;

    /**
     * For convenience, returns `true` if successful, or `false` otherwise
     *
     * @todo Rename this!!
     */
    public function formatIntl(TelephoneNumber $telephoneNumber): bool;
}
