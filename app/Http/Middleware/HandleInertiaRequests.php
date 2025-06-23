<?php

namespace App\Http\Middleware;

use App\Data\SharedAuthProps;
use App\Data\SharedProps;
use App\Data\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;
use Override;

class HandleInertiaRequests extends Middleware
{
	/**
	 * The root template that's loaded on the first page visit.
	 *
	 * @see https://inertiajs.com/server-side-setup#root-template
	 *
	 * @var string
	 */
	protected $rootView = 'app';

	/**
	 * Define the props that are shared by default.
	 *
	 * @see https://inertiajs.com/shared-data
	 *
	 * @return array<string, mixed>
	 */
	#[Override]
	public function share(Request $request): array
	{
		return new SharedProps(
			errors: Inertia::always($this->resolveValidationErrors($request)),
			name: config('app.name'),
			auth: new SharedAuthProps(
				user: User::fromUser($request->user()),
			),
			sidebarOpen: !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
		)->toArray();
	}
}
