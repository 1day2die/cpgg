<?php

namespace App\Http\Controllers\Api;

use App\Classes\PterodactylClient;
use App\Events\UserUpdateCreditsEvent;
use App\Helpers\CurrencyHelper;
use App\Http\Resources\UserResource;
use App\Models\DiscordUser;
use App\Models\User;
use App\Notifications\ReferralNotification;
use App\Settings\PterodactylSettings;
use App\Settings\ReferralSettings;
use App\Settings\UserSettings;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    private $pterodactyl;
    private $currencyHelper;

    public function __construct(PterodactylSettings $ptero_settings, CurrencyHelper $currencyHelper)
    {
        $this->pterodactyl = new PterodactylClient($ptero_settings);
        $this->currencyHelper = $currencyHelper;
    }

    const ALLOWED_INCLUDES = ['servers.product', 'notifications', 'payments', 'vouchers.users', 'roles', 'discordUser'];
    const ALLOWED_FILTERS = ['name', 'server_limit', 'email', 'pterodactyl_id', 'suspended'];

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $users = QueryBuilder::for(User::class)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->allowedFilters(self::ALLOWED_FILTERS)
            ->paginate($request->input('per_page') ?? 50);

        return UserResource::collection($users);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return UserResource|\Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(int $id)
    {
        $discordUser = DiscordUser::find($id);
        $userQuery = $discordUser
            ? $discordUser->user()->getQuery()
            : User::query();

        $user = QueryBuilder::for($userQuery)
            ->with('discordUser')
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->where('users.id', '=', $id)
            ->orWhereHas('discordUser', function (Builder $builder) use ($id) {
                $builder->where('id', '=', $id);
            })
            ->firstOrFail();

        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return UserResource|Illuminate\Database\Eloquent\ModelNotFoundExpcetion
     * 
     * @throws ValidationException
     */
    public function update(Request $request, int $id)
    {
        $discordUser = DiscordUser::find($id);
        $user = $discordUser ? $discordUser->user : User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|min:4|max:30',
            'email' => 'required|string|email',
            'credits' => 'sometimes|numeric|min:0.01|max:50000',
            'server_limit' => 'sometimes|numeric|min:0|max:1000000',
        ]);

        event(new UserUpdateCreditsEvent($user));

        //Update Users Password on Pterodactyl
        //Username,Mail,First and Lastname are required aswell
        $response = $this->pterodactyl->application->patch('/application/users/'.$user->pterodactyl_id, [
            'username' => $request->name,
            'first_name' => $request->name,
            'last_name' => $request->name,
            'email' => $request->email,
        ]);

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'pterodactyl_error_message' => $response->toException()->getMessage(),
                'pterodactyl_error_status' => $response->toException()->getCode(),
            ]);
        }
        if($request->has("role")){
            $collectedRoles = collect($request->role)->map(fn($val)=>(int)$val);
            $user->syncRoles($collectedRoles);
        }

        $user->update($request->except('role'));

        return UserResource::make($user);
    }

    /**
     * increments the users credits or/and server_limit
     *
     * @param  Request  $request
     * @param  int  $id
     * @return UserResource|\Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @throws ValidationException
     */
    public function increment(Request $request, int $id)
    {
        $discordUser = DiscordUser::find($id);
        $user = $discordUser ? $discordUser->user : User::findOrFail($id);

        $request->validate([
            'credits' => 'sometimes|numeric|min:0.01|max:50000',
            'server_limit' => 'sometimes|numeric|min:0|max:1000000',
        ]);

        if ($request->credits) {
            if ($user->credits + $request->credits >= $this->currencyHelper->prepareForDatabase(50000)) {
                throw ValidationException::withMessages([
                    'credits' => __("You can't add this amount of credits because you would exceed the credit limit"),
                ]);
            }
            event(new UserUpdateCreditsEvent($user));
            $user->increment('credits', $this->currencyHelper->prepareForDatabase($request->credits));
        }

        if ($request->server_limit) {
            if ($user->server_limit + $request->server_limit >= 2147483647) {
                throw ValidationException::withMessages([
                    'server_limit' => __("You cannot add this amount of servers because it would exceed the server limit."),
                ]);
            }
            $user->increment('server_limit', $request->server_limit);
        }

        return UserResource::make($user->fresh());
    }

    /**
     * decrements the users credits or/and server_limit
     *
     * @param  Request  $request
     * @param  int  $id
     * @return UserResource|\Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @throws ValidationException
     */
    public function decrement(Request $request, int $id)
    {
        $discordUser = DiscordUser::find($id);
        $user = $discordUser ? $discordUser->user : User::findOrFail($id);

        $request->validate([
            'credits' => 'sometimes|numeric|min:0.01|max:50000',
            'server_limit' => 'sometimes|numeric|min:0|max:1000000',
        ]);

        if ($request->credits) {
            if ($user->credits - $this->currencyHelper->prepareForDatabase($request->credits) < 0) {
                throw ValidationException::withMessages([
                    'credits' => __("You can't remove this amount of credits because you would exceed the minimum credit limit"),
                ]);
            }
            $user->decrement('credits', $this->currencyHelper->prepareForDatabase($request->credits));
        }

        if ($request->server_limit) {
            if ($user->server_limit - $request->server_limit < 0) {
                throw ValidationException::withMessages([
                    'server_limit' => __("You cannot remove this amount of servers because it would exceed the minimum server."),
                ]);
            }
            $user->decrement('server_limit', $request->server_limit);
        }

        return UserResource::make($user->fresh());
    }

    /**
     * Suspends the user
     *
     * @param  Request  $request
     * @param  int  $id
     * @return UserResource|\Illuminate\Http\JsonResponse|\Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function suspend(Request $request, int $id)
    {
        $discordUser = DiscordUser::find($id);
        $user = $discordUser ? $discordUser->user : User::findOrFail($id);

        if ($user->isSuspended()) {
            return response()->json([
                'error' => __('The user is already suspended'),
            ], 400);
        }
        
        $user->suspend();

        return UserResource::make($user);
    }

    /**
     * Unsuspend the user
     *
     * @param  Request  $request
     * @param  int  $id
     * @return UserResource|\Illuminate\Http\JsonResponse|\Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function unsuspend(Request $request, int $id)
    {
        $discordUser = DiscordUser::find($id);
        $user = $discordUser ? $discordUser->user : User::findOrFail($id);

        if (! $user->isSuspended()) {
            return response()->json([
                'error' => __('The user is not suspended'),
            ], 400);
        }

        $user->unSuspend();

        return UserResource::make($user);
    }

    /**
     * Create a unique Referral Code for User
     *
     * @return string
     */
    protected function createReferralCode()
    {
        $referralcode = STR::random(8);
        if (User::where('referral_code', '=', $referralcode)->exists()) {
            $this->createReferralCode();
        }

        return $referralcode;
    }

    /**
     * @param Request  $request
     * @param UserSettings $userSettings
     * @return UserResource
     * 
     * @throws ValidationException
     */
    public function store(Request $request, UserSettings $userSettings, ReferralSettings $referralSettings)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:30', 'min:4', 'alpha_num', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:64', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:191'],
        ]);

        // Prevent the creation of new users via API if this is enabled.
        if (! $userSettings->creation_enabled) {
            throw ValidationException::withMessages([
                'error' => 'The creation of new users has been blocked by the system administrator.',
            ]);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'credits' => $userSettings->initial_credits,
            'server_limit' => $userSettings->initial_server_limit,
            'password' => Hash::make($request->input('password')),
            'referral_code' => $this->createReferralCode(),
        ]);

        $response = $this->pterodactyl->application->post('/application/users', [
            'external_id' => App::environment('local') ? Str::random(16) : (string) $user->id,
            'username' => $user->name,
            'email' => $user->email,
            'first_name' => $user->name,
            'last_name' => $user->name,
            'password' => $request->input('password'),
            'root_admin' => false,
            'language' => 'en',
        ]);

        if ($response->failed()) {
            $user->delete();
            throw ValidationException::withMessages([
                'pterodactyl_error_message' => $response->toException()->getMessage(),
                'pterodactyl_error_status' => $response->toException()->getCode(),
            ]);
        }

        $user->update([
            'pterodactyl_id' => $response->json()['attributes']['id'],
        ]);
        //INCREMENT REFERRAL-USER CREDITS
        if (! empty($request->input('referral_code'))) {
            $ref_code = $request->input('referral_code');
            $new_user = $user->id;
            if ($ref_user = User::query()->where('referral_code', '=', $ref_code)->first()) {
                if ($referralSettings->mode == 'register' || $referralSettings->mode == 'both') {
                    $ref_user->increment('credits', $referralSettings->reward);
                    $ref_user->notify(new ReferralNotification($ref_user->id, $new_user));
                }
                //INSERT INTO USER_REFERRALS TABLE
                DB::table('user_referrals')->insert([
                    'referral_id' => $ref_user->id,
                    'registered_user_id' => $user->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
        $user->sendEmailVerificationNotification();

        return UserResource::make($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return UserResource|\Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function destroy(int $id)
    {
        $discordUser = DiscordUser::find($id);
        $user = $discordUser ? $discordUser->user : User::findOrFail($id);

        $user->delete();

        return UserResource::make($user);
    }
}
