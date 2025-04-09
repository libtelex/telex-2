# TELEX-2

Telephone numbers matched, validated, formatted, using [PHP](https://www.php.net/).

> [!WARNING]
> TELEX-2 is a work in progress and does not yet offer the same functionality as its earlier incarnation, [Telex](https://github.com/libtelex/telex)

> [!WARNING]
> Only a small selection of European countries is supported at present&mdash;find out which ones by looking in `src/RuleSet`&mdash;and the rule-sets that *have* been implemented may not correctly recognise all of the country's numbers.

TELEX-2 is used by [SeeTheWorld](https://www.seetheworld.com/).

## Usage

Quickly format a telephone number:

```php
echo (new Telex2())->formatIntl('+44 (0)785 056 6978');  // => `"+44 7850 566978"`
```

Or get a complete rundown:

```php
$telex2 = new Telex2();
print_r($telex2->match('+44 (0)785 056 6978'));

/*
=> Libtelex\Telex2\TelephoneNumber Object
(
    [source:Libtelex\Telex2\TelephoneNumber:private] => +44 (0)785 056 6978
    [countryCallingCode:Libtelex\Telex2\TelephoneNumber:private] => 44
    [nationalNumber:Libtelex\Telex2\TelephoneNumber:private] => 07850566978
    [type:Libtelex\Telex2\TelephoneNumber:private] => 2
    [formatted:Libtelex\Telex2\TelephoneNumber:private] => +44 7850 566978
)
*/
```

## Installation

Install using [Composer](https://getcomposer.org/):

```sh
composer require libtelex/telex-2
```

## Background

TELEX-2 is yet another attempt at recognising, and formatting, telephone numbers using PHP; it is an evolution of [Telex](https://github.com/libtelex/telex), which does a slightly better job than some much older, messier code.

TELEX-2 more reliably matches telephone numbers because it looks for more country-specific, numeric 'signatures'.  These signatures, patterns in a country's [telephone numbering plan](https://en.wikipedia.org/wiki/Telephone_numbering_plan), are encoded in separate rule-set classes&mdash;one for each country.

It's all still pretty rudimentary, really, but this latest incarnation is more robust and reliable, and its approach is much more satisfying than Telex'.
