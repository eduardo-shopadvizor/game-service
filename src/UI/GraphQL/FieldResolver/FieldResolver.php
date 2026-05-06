<?php

declare(strict_types=1);

namespace Saz\Game\UI\GraphQL\FieldResolver;

use BackedEnum;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Resolver\FieldResolver as BaseFieldResolver;

final class FieldResolver
{
    public function __invoke(
        mixed $parentValue,
        mixed $args,
        mixed $context,
        ResolveInfo $info,
    ): mixed {
        $fieldName = $info->fieldName;
        $value = BaseFieldResolver::valueFromObjectOrArray($parentValue, $fieldName);
        $value = $this->resolveValue($value);

        return $value instanceof Closure
            ? $value($parentValue, $args, $context, $info)
            : $value;
    }

    private function resolveValue(mixed $value): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return $value;
    }
}
