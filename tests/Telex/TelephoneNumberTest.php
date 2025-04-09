<?php declare(strict_types=1);

namespace Libtelex\Telex2\Tests;

use Libtelex\Telex2\TelephoneNumber;
use PHPUnit\Framework\TestCase;

class TelephoneNumberTest extends TestCase
{
    public function testIsInstantiable(): void
    {
        $telephoneNumber = new TelephoneNumber('01234 567890');

        $this->assertSame('01234 567890', $telephoneNumber->getSource());
        $this->assertSame('', $telephoneNumber->getIsoAlpha2CountryCode());
        $this->assertSame('', $telephoneNumber->getCountryCallingCode());
        $this->assertSame('', $telephoneNumber->getNationalNumber());
        $this->assertNull($telephoneNumber->getType());
        $this->assertNull($telephoneNumber->getFormatted());
    }

    public function testMatchforcountrySetsPropertiesThatAssociateTheTelephoneNumberWithACountry(): void
    {
        $telephoneNumber = (new TelephoneNumber('01234 567890'))
            ->setMainNumbers('44', '01234 567890')
            ->matchForCountry('GB', '44', TelephoneNumber::TYPE_FIXED_LINE, '+44 1234 567890')
        ;

        $this->assertSame('01234 567890', $telephoneNumber->getSource());
        $this->assertSame('GB', $telephoneNumber->getIsoAlpha2CountryCode());
        $this->assertSame('44', $telephoneNumber->getCountryCallingCode());
        $this->assertSame('01234 567890', $telephoneNumber->getNationalNumber());
        $this->assertSame(TelephoneNumber::TYPE_FIXED_LINE, $telephoneNumber->getType());
        $this->assertSame('+44 1234 567890', $telephoneNumber->getFormatted());
    }

    public function testNomatchforcountryResetsPropertiesThatAssociateTheTelephoneNumberWithACountry(): void
    {
        $telephoneNumber = (new TelephoneNumber('01234 567890'))
            ->setMainNumbers('44', '01234 567890')
            ->noMatchForCountry()
        ;

        $this->assertSame('01234 567890', $telephoneNumber->getSource());
        $this->assertSame('', $telephoneNumber->getIsoAlpha2CountryCode());
        $this->assertSame('', $telephoneNumber->getCountryCallingCode());
        $this->assertSame('01234 567890', $telephoneNumber->getNationalNumber());
        $this->assertNull($telephoneNumber->getType());
        $this->assertNull($telephoneNumber->getFormatted());
    }
}
