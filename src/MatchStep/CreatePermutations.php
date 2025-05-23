<?php declare(strict_types=1);

namespace Libtelex\Telex2\MatchStep;

use Libtelex\Telex2\MatchStepInterface;
use Libtelex\Telex2\RuleSetInterface;
use Libtelex\Telex2\StringObject;
use Libtelex\Telex2\TelephoneNumber;

use function array_filter;
use function array_map;
use function array_unique;
use function implode;
use function in_array;
use function preg_match;
use function str_starts_with;
use function strlen;

use const false;
use const null;
use const SORT_REGULAR;
use const true;

/**
 * @phpstan-type CandidateArray array{string,string}
 * @internal
 */
final class CreatePermutations implements MatchStepInterface
{
    /**
     * See:
     * - https://support.microsoft.com/en-us/skype/what-are-exit-codes-and-why-do-i-need-them-ac46f7da-8692-43cc-a96e-f86881a67023
     * - https://en.wikipedia.org/wiki/List_of_international_call_prefixes
     * - https://www.truphone.com/support/calling-abroad-country-codes-and-international-dialling/
     *
     * @var string[]
     * @todo Expand this list?  Currently short, but will cover most cases.
     */
    private const array EXIT_CODES = [
        '0011',  // Australia
        '011',   // Any country following the North American numbering plan
        '00',    // Recommended ITU prefix
    ];

    /**
     * @param TelephoneNumber $input
     * @phpstan-return TelephoneNumber[]|null
     */
    public function __invoke(
        RuleSetInterface $ruleSet,
        mixed $input,
    ): array|null {
        $trunkCode = $ruleSet->getTrunkCode();
        /** @var int[] */
        $nationalNumberLengths = (array) $ruleSet->getNationalNumberLength();

        // (We can take a shortcut if we've already been able to split the number into its main components)
        if ($input->hasCountryCallingCode()) {
            $candidate = $this->filterCandidate(
                [$input->getCountryCallingCode(), $input->getNationalNumber()],
                $trunkCode,
                $nationalNumberLengths,
            );

            if (false === $candidate) {
                return null;
            }

            $input->setMainNumbers(...$candidate);

            return [$input];
        }

        // (Things get a bit murkier from now on because we don't know if we're looking at a weirdly-formatted
        // international number, or a national number)

        $countryCallingCode = $ruleSet->getCountryCallingCode();

        $sourceDigits = (new StringObject($input->getSource()))
            ->onlyDigits()
            ->getValue()
        ;

        $candidates = [];

        // 1. 'Expanded' international number (i.e. containing an exit code), with/out superfluous digits (e.g.
        //    "0044 (0)1234 567890") or "0044 1234 567890":

        $expandedIntlNumberRegExp = '~^(?:' . implode('|', self::EXIT_CODES) . "){$countryCallingCode}(.*)$~";
        $matches = [];
        $treatNumberAsExpandedIntl = (bool) preg_match($expandedIntlNumberRegExp, $sourceDigits, $matches);

        if ($treatNumberAsExpandedIntl) {
            $candidates[] = $this->filterCandidate(
                [$countryCallingCode, $matches[1]],
                $trunkCode,
                $nationalNumberLengths,
            );
        }

        // 2. A *non-standard* portable international number, with/out superfluous digits (e.g. "44 (0)1234 567890" or
        //    "44 1234 567890").  (Remember: we've already dealt with numbers starting something like "+44 ".)

        $portableIntlNumberRegExp = "~^{$countryCallingCode}(.*)~";
        $matches = [];
        $treatNumberAsPortableIntl = (bool) preg_match($portableIntlNumberRegExp, $sourceDigits, $matches);

        if ($treatNumberAsPortableIntl) {
            $candidates[] = $this->filterCandidate(
                [$countryCallingCode, $matches[1]],
                $trunkCode,
                $nationalNumberLengths,
            );
        }

        // 3. A complete 'national number' (e.g. "01234 567890").  Basically: just try the whole number.

        $candidates[] = $this->filterCandidate(
            ['', $sourceDigits],
            $trunkCode,
            $nationalNumberLengths,
        );

        $filteredCandidates = array_filter($candidates);

        if (!$filteredCandidates) {
            return null;
        }

        return array_map(
            fn (array $candidate): TelephoneNumber => (clone $input)->setMainNumbers(...$candidate),
            array_unique($filteredCandidates, SORT_REGULAR),
        );
    }

    /**
     * @phpstan-param CandidateArray $candidate
     * @param int[] $nationalNumberLengths
     * @phpstan-return CandidateArray|false ...Like `filter_var()`
     */
    private function filterCandidate(
        array $candidate,
        string $trunkCode,
        array $nationalNumberLengths,
    ): array|false {
        // Remember, for the national number, we're expecting something *like*:
        // - "1234 567890" (from a correctly-formatted international number for a plan with a trunk code)
        // - "01234 567890" (from a national-only number, or an incorrectly-formatted international number for a plan
        //   with a trunk code)

        // (If the candidate contains no country dialling code, we must treat it as a national number.  In that case, we
        // mustn't touch the national component: only in international numbers can -- and should -- the trunk code be
        // omitted.)
        if ('' !== $candidate[0]) {
            // Make sure the national number is complete before we take any further steps: if the country's plan
            // specifies a trunk code then make sure the number includes it:

            $nationalNumber = $candidate[1];

            $candidate[1] = '' === $trunkCode || str_starts_with($nationalNumber, $trunkCode)
                ? $nationalNumber
                : $trunkCode . $nationalNumber
            ;
        }

        return in_array(strlen($candidate[1]), $nationalNumberLengths, true)
            ? $candidate
            : false
        ;
    }
}
