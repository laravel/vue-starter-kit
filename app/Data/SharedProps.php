<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SharedProps extends Data
{
	public function __construct(
		public object          $errors,
		public string          $name,
		public SharedAuthProps $auth,
		public bool            $sidebarOpen,
	)
	{
	}
}
