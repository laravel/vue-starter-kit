<?php

namespace App\Data\Transformers;

use Illuminate\Support\Uri;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class UriTransformer implements Transformer
{
	public function transform(DataProperty $property, mixed $value, TransformationContext $context): mixed
	{
		if (!is_a($value, Uri::class)) {
			return $value;
		}

		return $value->value();
	}
}
