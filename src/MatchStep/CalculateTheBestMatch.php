<?php declare(strict_types=1);

namespace Libtelex\Telex2\MatchStep;

use Libtelex\Telex2\MatchStepInterface;
use Libtelex\Telex2\RuleSetInterface;
use Libtelex\Telex2\TelephoneNumber;

use const null;

final class CalculateTheBestMatch implements MatchStepInterface
{
    /**
     * @param TelephoneNumber[] $input
     */
    public function __invoke(
        mixed $input,
        RuleSetInterface $ruleSet,
    ): TelephoneNumber|null {
        // (Assumes that the telephone numbers are in priority order)
        foreach ($input as $telephoneNumber) {
            if ($ruleSet->formatIntl($telephoneNumber)) {
                return $telephoneNumber;
            }
        }

        return null;
    }
}
