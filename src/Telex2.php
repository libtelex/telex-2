<?php declare(strict_types=1);

namespace Libtelex\Telex2;

use Libtelex\Telex2\MatchStep\CalculateTheBestMatch;
use Libtelex\Telex2\MatchStep\CreatePermutations;
use Libtelex\Telex2\RuleSet\Denmark;
use Libtelex\Telex2\RuleSet\France;
use Libtelex\Telex2\RuleSet\Italy;
use Libtelex\Telex2\RuleSet\Monaco;
use Libtelex\Telex2\RuleSet\Spain;
use Libtelex\Telex2\RuleSet\Switzerland;
use Libtelex\Telex2\RuleSet\UnitedKingdom;
use LogicException;

use const null;

/**
 * Facade
 *
 * @todo Add more tests!
 * @todo Test against all rule-sets and use some kind of score to determine the best match?
 */
class Telex2
{
    /**
     * Lazy-loaded
     *
     * @var RuleSetInterface[]
     */
    private array $ruleSets;

    /**
     * Lazy-loaded
     *
     * @var MatchStepInterface[]
     */
    private array $steps;

    /**
     * @return RuleSetInterface[]
     */
    private function getRuleSets(): array
    {
        if (!isset($this->ruleSets)) {
            $ruleSets = [
                new France(),
                new Switzerland(),
                new Spain(),
                new Monaco(),
                new Denmark(),
                // Variable length:
                new UnitedKingdom(),
                new Italy(),
            ];

            // @todo Sort by length, longest first?
            $this->ruleSets = $ruleSets;
        }

        return $this->ruleSets;
    }

    /**
     * @return MatchStepInterface[]
     */
    private function getSteps(): array
    {
        if (!isset($this->steps)) {
            $this->steps = [
                new CreatePermutations(),
                new CalculateTheBestMatch(),
            ];
        }

        return $this->steps;
    }

    /**
     * @throws LogicException If the last match-step failed to return a telephone-number object
     */
    public function match(string $string): TelephoneNumber|null
    {
        foreach ($this->getRuleSets() as $ruleSet) {
            $outputFromPrev = new TelephoneNumber($string);

            foreach ($this->getSteps() as $step) {
                $outputFromCurr = $step($outputFromPrev, $ruleSet);

                if (null === $outputFromCurr) {
                    // (No output; try a different rule-set)
                    continue 2;
                }

                $outputFromPrev = $outputFromCurr;
            }

            if (!($outputFromPrev instanceof TelephoneNumber)) {
                throw new LogicException('The last match-step failed to return a telephone-number object');
            }

            // (Every step was successful)
            return $outputFromPrev;
        }

        // (Read "not formatted" / "unable to format")
        return null;
    }

    public function formatIntl(string $string): string|null
    {
        return $this
            ->match($string)
            ?->getFormatted()
        ;
    }
}
