<?php declare(strict_types=1);

namespace Libtelex\Telex2\MatchStep;

use Libtelex\Telex2\MatchStepInterface;
use Libtelex\Telex2\RuleSetInterface;
use Libtelex\Telex2\StringObject;
use Libtelex\Telex2\TelephoneNumber;

use function preg_match;
use function preg_replace;

use const null;

/**
 * Aims to quickly reject any input that is 'obviously' not a valid telephone number for the current country
 *
 * @internal
 */
final class TriageRawInput implements MatchStepInterface
{
    /**
     * @param TelephoneNumber $input
     */
    public function __invoke(
        RuleSetInterface $ruleSet,
        mixed $input,
    ): TelephoneNumber|null {
        // Remove noise to make subsequent work easier.
        //
        // Most non-digit characters are used to create groupings: essentially, to create space, but retain a connection
        // between, the digits.  We need to preserve that meaning, so we replace each unwanted character with a space.
        /** @var string For now, if an error occurs, just let subsequent code fail */
        $significantChars = preg_replace('~[^+0-9\s]~', ' ', $input->getSource());

        $matches = [];

        $telNumAppearsToContainACountryDiallingCode = (bool) preg_match(
            '~^\s*\+\s*(\d+)\s+(.*)~',
            $significantChars,
            $matches,
        );

        if ($telNumAppearsToContainACountryDiallingCode) {
            if ($ruleSet->getCountryCallingCode() !== $matches[1]) {
                // (Reject the input)
                return null;
            }

            $nationalNumber = (new StringObject($matches[2]))
                ->onlyDigits()
                ->getValue()
            ;

            $input->setMainNumbers($ruleSet->getCountryCallingCode(), $nationalNumber);

            return $input;
        }

        return $input;
    }
}
