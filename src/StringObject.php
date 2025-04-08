<?php declare(strict_types=1);

namespace Libtelex\Telex2;

use Stringable;

use function mb_strlen;
use function preg_quote;
use function preg_replace;

/**
 * Value object
 */
class StringObject implements Stringable
{
    public function __construct(
        private string $value,
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * For convenience
     */
    public function getLength(): int
    {
        return mb_strlen($this->getValue());
    }

    public function onlyDigits(): self
    {
        /** @var string For now, if an error occurs, just let subsequent code fail */
        $digits = preg_replace('~\D~', '', $this->getValue());

        return new self($digits);
    }

    public function deleteLeft(string $substring): self
    {
        $quotedSubstring = preg_quote($substring, '~');
        /** @var string For now, if an error occurs, just let subsequent code fail */
        $newValue = preg_replace("~^{$quotedSubstring}~", '', $this->getValue());

        return new self($newValue);
    }
}
