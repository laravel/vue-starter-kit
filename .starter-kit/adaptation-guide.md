# Upstream Sync — Adaptation Guide

This guide maps upstream `laravel/vue-starter-kit` patterns to this fork's conventions. The AI agent MUST read this before adapting any upstream change.

## Architecture Overview

This fork replaces Laravel's `FormRequest` + ad-hoc array props pattern with **Spatie Laravel Data** (`spatie/laravel-data`) combined with **Spatie TypeScript Transformer** (`spatie/laravel-typescript-transformer`). Every Data class is automatically mirrored as a TypeScript type in `resources/js/types/generated.d.ts`. There are **zero** FormRequest classes in this fork — the `app/Http/Requests/` directory does not exist.

---

## Pattern 1: FormRequest → Data Class

### Upstream

```php
// app/Http/Requests/SomeRequest.php
class SomeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
        ];
    }
}
```

### Fork

```php
// app/Data/SomeRequest.php
namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SomeRequest extends Data
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
        ];
    }
}
```

### Conversion Rules

1. Class extends `Spatie\LaravelData\Data` instead of `Illuminate\Foundation\Http\FormRequest`
2. Add `#[TypeScript]` attribute (always)
3. Convert form fields to **constructor-promoted public properties** with PHP types
4. If only simple type constraints: no `rules()` method needed (constructor types provide implicit validation)
5. If complex rules (unique, confirmed, Password defaults, etc.): add `public static function rules(ValidationContext $context): array`
6. Replace `$this->user()` with `Auth::user()` or `Auth::id()` — Data objects are not HTTP requests
7. Replace `$this->ip()` with `Request::ip()` (static facade)
8. Remove `authorize()` method — handle authorization in controller or middleware
9. `password_confirmation` must be a constructor property (even without a validation rule) for the `confirmed` rule and TypeScript generation

### Existing Request Data Classes

| Class                               | Has `rules()` | Has Business Logic                                                  |
| ----------------------------------- | ------------- | ------------------------------------------------------------------- |
| `LoginRequest`                      | No            | Yes (`authenticate()`, `ensureIsNotRateLimited()`, `throttleKey()`) |
| `RegisterRequest`                   | Yes           | No                                                                  |
| `ProfileUpdateRequest`              | Yes           | No                                                                  |
| `DeleteUserRequest`                 | Yes           | No                                                                  |
| `PasswordUpdateRequest`             | Yes           | No                                                                  |
| `PasswordResetLinkRequest`          | Yes           | No                                                                  |
| `NewPasswordRequest`                | Yes           | No                                                                  |
| `ConfirmPasswordRequest`            | No            | No                                                                  |
| `StorePasskeyRequest`               | Yes           | No                                                                  |
| `PasskeyRegistrationOptionsRequest` | No            | No                                                                  |

---

## Pattern 2: Inertia Array Props → Props Data Class

### Upstream

```php
return Inertia::render('page/Name', [
    'canResetPassword' => Route::has('password.request'),
    'status' => session('status'),
]);
```

### Fork

```php
return Inertia::render('page/Name', new NameProps(
    canResetPassword: Route::has('password.request'),
    status: $request->session()->get('status'),
));
```

### Conversion Steps

1. Create a `*Props` class in `app/Data/` with matching constructor properties
2. Add `#[TypeScript]` attribute
3. Pass `new XProps(...)` instead of an array to `Inertia::render()`

### Existing Props Data Classes

| Class                 | Used For                |
| --------------------- | ----------------------- |
| `LoginProps`          | Login page              |
| `ForgotPasswordProps` | Forgot password page    |
| `ResetPasswordProps`  | Reset password page     |
| `ProfileProps`        | Profile settings page   |
| `VerifyEmailPrompts`  | Email verification page |
| `PasskeyProps`        | Passkey settings page   |

---

## Pattern 3: Shared Props (Inertia Middleware)

### Upstream

```php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => ['user' => $request->user()],
        'name' => config('app.name'),
    ];
}
```

### Fork (HandleInertiaRequests.php)

```php
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
```

Key details:

- Constructs `SharedProps` then calls `->toArray()` to satisfy the parent return type
- `errors` is wrapped with `Inertia::always(...)` so validation errors persist across partial reloads
- The user goes through `User::fromUser()` factory (not raw Eloquent model)
- `sidebarOpen` is a fork addition (not in upstream)

---

## Pattern 4: User DTO with Factory Method

### Upstream

Passes `$request->user()` (raw Eloquent model) to frontend.

### Fork

Uses `App\Data\User` with a `fromUser()` factory:

```php
public static function fromUser(?UserModel $user): ?self
{
    if (!$user instanceof UserModel) {
        return null;
    }
    return new self(
        id: $user->id,
        name: $user->name,
        email: $user->email,
        avatar: $user->avatar,
        email_verified_at: $user->email_verified_at,
        created_at: $user->created_at,
    );
}
```

**Name collision**: `App\Data\User` and `App\Models\User` both exist. In Data classes, the model is aliased as `use App\Models\User as UserModel`.

Similar pattern exists for `PasskeyProp::fromPasskey()`.

---

## Pattern 5: TypeScript Integration

### Every Data class MUST have `#[TypeScript]`

Without it, no TypeScript type is generated. No error is thrown — Vue components will silently fail with type errors.

### Type generation pipeline

1. PHP Data class with `#[TypeScript]` → `php artisan typescript:transform` → `resources/js/types/generated.d.ts`
2. During `vite dev`, the custom plugin in `resources/js/lib/vite-plugin-transformer.ts` watches `app/Data/**/*.php` and auto-regenerates types
3. Composer script: `composer types` runs the artisan command

### Type mapping

| PHP Type                           | TypeScript Type   |
| ---------------------------------- | ----------------- |
| `string`                           | `string`          |
| `int`                              | `number`          |
| `bool`                             | `boolean`         |
| `?string`                          | `string \| null`  |
| `CarbonInterface` / `Carbon`       | `string`          |
| `?CarbonInterface`                 | `string \| null`  |
| `?Uri` (with `UriTransformer`)     | `any \| null`     |
| `object`                           | `object`          |
| Nested Data class                  | Reference by name |
| `array` with `@param array<T>` doc | `Array<T>`        |

### Vue component consumption

```vue
<script setup lang="ts">
import { LoginProps, LoginRequest } from '@/types/generated';

// Page props
defineProps<LoginProps>();

// Form type
const form = useForm<LoginRequest>({
	email: '',
	password: '',
	remember: false,
});
</script>
```

The same Data class can serve as BOTH page props and form type (dual-purpose).

---

## Pattern 6: Custom Transformer (UriTransformer)

For non-serializable types, create a transformer in `app/Data/Transformers/`:

```php
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
```

Applied via attribute: `#[WithTransformer(UriTransformer::class)]`

---

## Gotchas Checklist

When adapting an upstream change, verify each of these:

- [ ] New FormRequest? → Convert to Data class in `app/Data/`
- [ ] `#[TypeScript]` attribute added to all new Data classes?
- [ ] Constructor properties match form fields (including `password_confirmation`)?
- [ ] Complex validation uses `ValidationContext` (not Laravel's `$request`)?
- [ ] No `$this->user()` or `$this->ip()` — use facades instead?
- [ ] Inertia array props replaced with Props Data class?
- [ ] Factory methods used for Eloquent → DTO conversion?
- [ ] `@param array<T>` PHPDoc added for array properties?
- [ ] Run `php artisan typescript:transform` after changes?
- [ ] Vue components import types from `@/types/generated`?
- [ ] No manual TypeScript interfaces duplicating generated types?
- [ ] Auth-related changes checked for passkey compatibility?
