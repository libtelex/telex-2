<?php declare(strict_types=1);

namespace Libtelex\Telex2;

use function array_fill;
use function array_sum;
use function array_values;
use function ctype_digit;
use function implode;
use function in_array;
use function is_array;
use function is_int;
use function floor;
use function preg_match;
use function str_split;
use function strlen;

use const null;
use const true;

/**
 * @todo Test this!
 */
class Utils
{
    /**
     * Returns `true` if the subject starts with one of the patterns, or `false` otherwise
     *
     * @param string|string[] $patterns
     */
    public static function startsWith(
        string|array $patterns,
        string $subject,
        string &$prefix = '',
    ): bool {
        $pattern = is_array($patterns)
            ? implode('|', $patterns)
            : $patterns
        ;

        $matches = [];
        $matched = (bool) preg_match("~^({$pattern})~", $subject, $matches);

        if ($matched) {
            $prefix = $matches[0];
        }

        return $matched;
    }

    /**
     * @param int|int[] $patternOrLength
     * @return string[]
     * @todo Validate inputs
     */
    public static function chunk(
        string $string,
        int|array $patternOrLength,
        // array $synFreeChars,
    ): array {
        $stringLength = strlen($string);

        // An integer means "chunks of equal length".  (And it means we need to calculate the complete pattern.)
        if (is_int($patternOrLength)) {
            // (New name for the sake of clarity)
            /** @var int */
            $usualChunkLength = $patternOrLength;

            if ($usualChunkLength < $stringLength) {
                $numChunksOfUsualLength = (int) floor($stringLength / $usualChunkLength);
                $patternOrLength = array_fill(0, $numChunksOfUsualLength, $usualChunkLength);
            } else {
                $patternOrLength = [$usualChunkLength];
            }
        }

        /** @var int */
        $maxLengthByPattern = array_sum($patternOrLength);

        if ($maxLengthByPattern < $stringLength) {
            // Augment the pattern if necessary:

            $leftoverChunkLength = $stringLength - $maxLengthByPattern;
            $patternOrLength[] = $leftoverChunkLength;
        }

        $patternOrLength = array_values($patternOrLength);

        $currChunk = '';
        $numDigitsInCurrChunk = 0;
        $chunks = [];
        $patternIdx = 0;

        foreach (str_split($string) as $char) {
            // if (in_array($char, $synFreeChars, true)) {
            //     // Parentheses can be eaten greedily -- they don't count towards the length of the current chunk
            //     $currChunk .= $char;

            //     continue;
            // }

            if ($patternOrLength[$patternIdx] === $numDigitsInCurrChunk) {
                $chunks[] = $currChunk;
                $currChunk = '';
                $numDigitsInCurrChunk = 0;

                $patternIdx++;
            }

            if (ctype_digit($char)) {
                $currChunk .= $char;
                $numDigitsInCurrChunk++;
            }
        }

        // (Leftovers)
        $chunks[] = $currChunk;

        return $chunks;
    }
}
