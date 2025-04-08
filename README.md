# TELEX-2

> [!WARNING]
> TELEX-2 is a work in progress and does not yet offer the same functionality as its earlier incarnation, [Telex](https://github.com/libtelex/telex)

TELEX-2 recognises, and formats, telephone numbers using [PHP](https://www.php.net/).

TELEX-2 is used by [SeeTheWorld](https://www.seetheworld.com/).

## Background

TELEX-2 is yet another attempt at recognising, and formatting, telephone numbers using PHP; it is an evolution of [Telex](https://github.com/libtelex/telex), which does a slightly better job than some much older code.

TELEX-2 more reliably matches telephone numbers because it looks for more country-specific, numeric 'signatures'.  These signatures, patterns in a country's [telephone numbering plan](https://en.wikipedia.org/wiki/Telephone_numbering_plan), are encoded in separate rule-set classes&mdash;one for each country.

It's all still pretty rudimentary, really, but its approach is more satisfying, and more reliable, than Telex'.
