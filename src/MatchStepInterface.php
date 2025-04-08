<?php declare(strict_types=1);

namespace Libtelex\Telex2;

interface MatchStepInterface
{
    /**
     * Strictly `null` if unsuccessful
     */
    public function __invoke(
        mixed $input,
        RuleSetInterface $ruleSet,
    ): mixed;
}
