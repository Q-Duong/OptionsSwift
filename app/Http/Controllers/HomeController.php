<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $plans = [
            [
                'name' => 'Quarterly Flow',
                'price' => '$120',
                'breakdown' => 'Just $40 per month',
                'id' => env('STRIPE_PRICE_QUARTERLY'),
            ],
            [
                'name' => 'Semi-Annual Flow',
                'price' => '$210',
                'breakdown' => 'Save 12% - Just $35 per month',
                'id' => env('STRIPE_PRICE_SEMI_ANNUAL'),
                'popular' => true,
            ],
            [
                'name' => 'Annual Flow',
                'price' => '$360',
                'breakdown' => 'Save 25% - Just $30 per month',
                'id' => env('STRIPE_PRICE_ANNUAL'),
            ],
        ];

        $features = [
            'Live Option Flow Scanner',
            'Gamma Levels & Block Alerts',
            'Institutional Data Feed',
            'Option Chain Imbalance, Pressure & GEX analysis',
            'Estimated Hedge Shares',
        ];
        $currentPlanId = null;
        if (Auth::guard('client')->check()) {
            $client = Auth::guard('client')->user();
            $currentPlanId = $client->subscription('default') ? $client->subscription('default')->stripe_price : null;
        }

        // $currentPlanId = null;
        // if (Auth::guard('client')->check()) {
        //     $user = Auth::guard('client')->user();
        //     $subscription = $user->subscription('default');

        //     if ($subscription && $subscription->valid()) {
        //         $currentPlanId = $subscription->stripe_price;
        //     }
        // }

        return view('pages.client.home', compact('plans', 'features', 'currentPlanId'));
    }
}
