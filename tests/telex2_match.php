<?php declare(strict_types=1);

// phpcs:disable DanBettles.Debug.PHPDebugFunctions.Found

use Libtelex\Telex2\Telex2;

require '../vendor/autoload.php';

$telex2 = new Telex2();
print_r($telex2->match('+44 (0)785 056 6978'));

/*
=> Libtelex\Telex2\TelephoneNumber Object
(
    [source:Libtelex\Telex2\TelephoneNumber:private] => +44 (0)785 056 6978
    [countryCallingCode:Libtelex\Telex2\TelephoneNumber:private] => 44
    [isoAlpha2CountryCode:Libtelex\Telex2\TelephoneNumber:private] => GB
    [nationalNumber:Libtelex\Telex2\TelephoneNumber:private] => 07850566978
    [type:Libtelex\Telex2\TelephoneNumber:private] => 2
    [formatted:Libtelex\Telex2\TelephoneNumber:private] => +44 7850 566978
)
*/
