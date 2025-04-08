<?php declare(strict_types=1);

namespace Libtelex\Telex2\MatchStep;

use Libtelex\Telex2\MatchStepInterface;
use Libtelex\Telex2\RuleSetInterface;
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
        mixed $input,
        RuleSetInterface $ruleSet,
    ): array|null {
        $countryCallingCode = $ruleSet->getCountryCallingCode();
        $sourceDigits = $input->getSourceDigits();
        $trunkCode = $ruleSet->getTrunkCode();
        /** @var int[] */
        $nationalNumberLengths = (array) $ruleSet->getNationalNumberLength();

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

        // 2. A portable international number, with/out superfluous digits (e.g. "+44 (0)1234 567890" or
        //    "+44 1234 567890")

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

    private function applyTrunkCode(
        string $nationalNumber,
        string $trunkCode,
    ): string {
        return '' === $trunkCode || str_starts_with($nationalNumber, $trunkCode)
            ? $nationalNumber
            : $trunkCode . $nationalNumber
        ;
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

        // Make sure the national number is complete before we take any further steps: if the country's plan specifies a
        // trunk code then make sure the number includes it
        $candidate[1] = $this->applyTrunkCode($candidate[1], $trunkCode);

        return in_array(strlen($candidate[1]), $nationalNumberLengths, true)
            ? $candidate
            : false
        ;
    }
}
