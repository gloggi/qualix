<?php

declare(strict_types=1);

namespace Doctrine\Inflector\Rules\English;

use Doctrine\Inflector\GenericLanguageInflectorFactory;
use Doctrine\Inflector\Rules\Pattern;
use Doctrine\Inflector\Rules\Patterns;
use Doctrine\Inflector\Rules\Ruleset;
use Doctrine\Inflector\Rules\Substitutions;
use Doctrine\Inflector\Rules\Transformations;

/**
 * Override the Doctrine English\InflectorFactory, in order to get Laravel to pluralize "feedback" to "feedbacks".
 * We need this because by default, Laravel would assume that the plural of feedback is feedback, which is not
 * the case in qualix: The relation FeedbackData::feedbacks is named with an "s" as the suffix.
 */
final class InflectorFactory extends GenericLanguageInflectorFactory
{
    protected function getSingularRuleset(): Ruleset
    {
        return Rules::getSingularRuleset();
    }

    protected function getPluralRuleset(): Ruleset
    {
        return new Ruleset(
            new Transformations(...Inflectible::getPlural()),
            new Patterns(...array_filter(iterator_to_array(Uninflected::getPlural()), function (Pattern $pattern) {
                return !$pattern->matches('feedback');
            })),
            new Substitutions(...Inflectible::getIrregular())
        );
    }
}
