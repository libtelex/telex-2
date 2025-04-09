<?php declare(strict_types=1);

namespace Libtelex\Telex2;

use const null;

/**
 * Intended as a (largely) passive data-object
 *
 * See:
 * - https://en.wikipedia.org/wiki/E.164#/media/File:Phone_number_setup.png
 * - https://en.wikipedia.org/wiki/E.164
 *
 * @todo Test this
 * @todo Validation
 */
class TelephoneNumber
{
    final public const int TYPE_FIXED_LINE = 1;
    final public const int TYPE_MOBILE = 2;
    /** Strictly non-geographic */
    final public const int TYPE_NON_GEOGRAPHIC = 3;

    /**
     * The raw, source string
     */
    private string $source;

    /**
     * ITU-T E.164 code
     */
    private string $countryCallingCode;

    /**
     * ISO 3166-1 alpha-2 code
     */
    private string $isoAlpha2CountryCode;

    /**
     * Fully-formed 'national number'
     */
    private string $nationalNumber;

    /**
     * A numeric ID -- see constants -- indicating the type of telephone number (e.g. mobile)
     */
    private int|null $type;

    private string|null $formatted;

    public function __construct(string $source)
    {
        $this
            ->setSource($source)

            ->setCountryCallingCode()
            ->setNationalNumber()

            ->setIsoAlpha2CountryCode()
            ->setType(null)
            ->setFormatted(null)
        ;
    }

    private function setSource(string $string): self
    {
        $this->source = $string;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    private function setCountryCallingCode(string $code = ''): self
    {
        $this->countryCallingCode = $code;

        return $this;
    }

    public function getCountryCallingCode(): string
    {
        return $this->countryCallingCode;
    }

    public function hasCountryCallingCode(): bool
    {
        return '' !== $this->getCountryCallingCode();
    }

    private function setIsoAlpha2CountryCode(string $code = ''): self
    {
        $this->isoAlpha2CountryCode = $code;

        return $this;
    }

    public function getIsoAlpha2CountryCode(): string
    {
        return $this->isoAlpha2CountryCode;
    }

    private function setNationalNumber(string $string = ''): self
    {
        $this->nationalNumber = $string;

        return $this;
    }

    public function getNationalNumber(): string
    {
        return $this->nationalNumber;
    }

    private function setType(int|null $id): self
    {
        $this->type = $id;

        return $this;
    }

    public function getType(): int|null
    {
        return $this->type;
    }

    private function setFormatted(string|null $formatted): self
    {
        $this->formatted = $formatted;

        return $this;
    }

    public function getFormatted(): string|null
    {
        return $this->formatted;
    }

    /**
     * For convenience and clarity
     *
     * @todo Rename this
     */
    public function setMainNumbers(
        string $countryCallingCode,
        string $nationalNumber,
    ): self {
        return $this
            ->setCountryCallingCode($countryCallingCode)
            ->setNationalNumber($nationalNumber)
        ;
    }

    /**
     * For convenience and clarity
     */
    public function noMatchForCountry(): self
    {
        return $this
            ->setIsoAlpha2CountryCode()
            ->setCountryCallingCode()
            ->setType(null)
            ->setFormatted(null)
        ;
    }

    /**
     * For convenience and clarity, 'confirms' a match
     */
    public function matchForCountry(
        string $isoAlpha2CountryCode,
        string $countryCallingCode,
        int $type,
        string $formatted,
    ): self {
        return $this
            ->setIsoAlpha2CountryCode($isoAlpha2CountryCode)
            ->setCountryCallingCode($countryCallingCode)
            ->setType($type)
            ->setFormatted($formatted)
        ;
    }
}
