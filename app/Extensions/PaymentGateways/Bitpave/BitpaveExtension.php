<?php

namespace App\Extensions\PaymentGateways\Bitpave;

use App\Classes\AbstractExtension;
use App\Events\PaymentEvent;
use App\Events\UserUpdateCreditsEvent;
use App\Models\PartnerDiscount;
use App\Models\Payment;
use App\Models\ShopProduct;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;


/**
 * Summary of PayPalExtension
 */
class BitpaveExtension extends AbstractExtension
{
    public static function getConfig(): array
    {
        return [
            "name" => "Bitpave",
            "RoutesIgnoreCsrf" => [],
        ];
    }
    public static function getRedirectUrl(Payment $payment, ShopProduct $shopProduct, string $totalPriceString): string
    {
        $url = 'https://bitpave.com/api/checkout/create';
        $settings = new BitpaveSettings();
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'client' => $settings->client_id,
                'client_secret' => $settings->client_secret,
                'name' => "Order #{$payment->id} - " . $shopProduct->description,
                'wallet' => $settings->wallet,
                //'icon' => "https://ctrlpanel.gg/img/controlpanel.png",
                'price' => number_format($shopProduct->getTotalPrice(), 2, '.', ''),
                'custom_data' => json_encode(['user_id' => Auth::user()->id, 'email' => Auth::user()->email, 'payment_id' => $payment->id]),
                'success_url' => route('payment.BitpaveSuccess'),
                'cancel_url' => route('payment.Cancel'),
                'callback_url' => route('payment.BitpaveCallback'),
            ]);

            Log::error($response->json());
            return $response->json()['checkout_url'];


        } catch (Exception $ex) {
            Log::error('Bitpave Payment: ' . $ex->getMessage());
            throw new Exception('Payment failed');
        }
    }


    static function success(Request $request): void
    {
        $payment = Payment::findOrFail($request->input('payment'));
        $payment->status = 'pending';

        Redirect::route('home')->with('success', 'Your payment is being processed')->send();
        return;
    }

    public static function callback(Request $request)
    {
        try {


            $settings = new BitpaveSettings();
            // Verify that incoming source is legit & verify the transaction status
            if ($request->input('signature') == $settings->client_secret and $request->input('status') == 'completed') {

                $data = json_decode($request->input('custom_data'));

                $payment = Payment::findOrFail($data->payment_id);
                $payment->status->update([
                    'status' => "paid",
                ]);

                $shopProduct = ShopProduct::findOrFail($payment->shop_item_product_id);
                event(new PaymentEvent($payment, $payment, $shopProduct));

                $user = User::findOrFail($payment->user_id);
                event(new UserUpdateCreditsEvent($user));

            }
        } catch (Exception $ex) {
            Log::error('Bitpave Payment Webhook: ' . $ex->getMessage());
            }
    }



}
