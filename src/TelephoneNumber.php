<?php declare(strict_types=1);

namespace Libtelex\Telex2;

use function preg_replace;
use function strlen;

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
     * Just the digits of the source string
     */
    private string $sourceDigits;

    private int $numSourceDigits;

    /**
     * The ITU-T E.164 code
     */
    private string $countryCallingCode;

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

            ->setType(null)
            ->setFormatted(null)
        ;
    }

    public function setSource(string $string): self
    {
        $this->source = $string;
        // For convenience:
        /** @phpstan-ignore-next-line For now, allow it to break */
        $this->sourceDigits = preg_replace('~\D~', '', $this->source);
        $this->numSourceDigits = strlen($this->sourceDigits);

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getSourceDigits(): string
    {
        return $this->sourceDigits;
    }

    public function getNumSourceDigits(): int
    {
        return $this->numSourceDigits;
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

    private function setNationalNumber(string $string = ''): self
    {
        $this->nationalNumber = $string;

        return $this;
    }

    public function getNationalNumber(): string
    {
        return $this->nationalNumber;
    }

    public function setType(int|null $id): self
    {
        $this->type = $id;

        return $this;
    }

    public function getType(): int|null
    {
        return $this->type;
    }

    public function setFormatted(string|null $formatted): self
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
     * N.B. At least a national number is required
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
            ->setCountryCallingCode()
            ->setType(null)
            ->setFormatted(null)
        ;
    }

    /**
     * For convenience and clarity
     */
    public function matchForCountry(
        string $countryCallingCode,
        int $type,
        string $formatted,
    ): self {
        return $this
            ->setCountryCallingCode($countryCallingCode)
            ->setType($type)
            ->setFormatted($formatted)
        ;
    }
}
