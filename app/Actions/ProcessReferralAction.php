<?php

namespace App\Actions;

use App\Helpers\CurrencyHelper;
use App\Models\User;
use App\Notifications\ReferralNotification;
use App\Settings\GeneralSettings;
use App\Settings\ReferralSettings;
use Illuminate\Support\Facades\DB;

class ProcessReferralAction
{
    protected ReferralSettings $referralSettings;
    protected GeneralSettings $generalSettings;
    protected CurrencyHelper $currencyHelper;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->referralSettings = app(ReferralSettings::class);
        $this->generalSettings = app(GeneralSettings::class);
        $this->currencyHelper = app(CurrencyHelper::class);
    }

    public function execute(User $user, string $referral_code, bool $log_activity = false)
    {
        $ref_user = User::query()->where('referral_code', $referral_code)->first();

        if ($ref_user) {
            if ($this->referralSettings->mode == 'sign-up' || $this->referralSettings->mode == 'both') {
                $ref_user->increment('credits', $this->referralSettings->reward);
                $ref_user->notify(new ReferralNotification($user));

                if ($log_activity) {
                    $log = sprintf(
                        'gained %s %s for sign-up-referral of %s (ID:%s)',
                        $this->currencyHelper->formatForDisplay($this->referralSettings->reward),
                        $this->generalSettings->credits_display_name,
                        $user->name,
                        $user->id
                    );

                    activity()
                        ->performedOn($user)
                        ->causedBy($ref_user)
                        ->log($log);
                }
            }

            DB::table('user_referrals')->insert([
                'referral_id' => $ref_user->id,
                'registered_user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}