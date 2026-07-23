<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MarketDataController extends Controller
{
    public function fetchChain(Request $request)
    {
        $sym = $request->query('symbol');
        $expDate = $request->query('expDate');
        $apiKey = config('services.polygon.api_key');

        $optionTicker = $sym === 'SPX' ? 'I:SPX' : $sym;
        $base = "https://api.polygon.io/v3/snapshot/options/{$optionTicker}";

        $calls = $this->fetchContractType($base, $expDate, 'call', $apiKey);
        $puts = $this->fetchContractType($base, $expDate, 'put', $apiKey);

        return response()->json([
            'calls' => $calls,
            'puts'  => $puts
        ]);
    }

    private function fetchContractType($base, $expDate, $type, $apiKey)
    {
        $results = [];
        $url = "{$base}?expiration_date={$expDate}&limit=250&contract_type={$type}&apiKey={$apiKey}";
        $pg = 0;

        // Vòng lặp lấy data 6 pages y hệt logic JS cũ
        while ($url && $pg < 6) {
            // Thêm timeout(10) để server PHP không bị treo nếu Polygon phản hồi quá lâu
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) break;

            $data = $response->json();
            if (!empty($data['results'])) {
                $results = array_merge($results, $data['results']);
            }

            $url = !empty($data['next_url']) ? $data['next_url'] . "&apiKey={$apiKey}" : null;
            $pg++;
        }

        return $results;
    }

    /**
     * Proxy gọi API VWAP từ Polygon
     */
    public function fetchVwap(Request $request)
    {
        $sym = $request->query('symbol');
        $date = $request->query('date');
        $apiKey = config('services.polygon.api_key');

        $ticker = $sym === 'SPX' ? 'I:SPX' : $sym;
        $url = "https://api.polygon.io/v2/aggs/ticker/{$ticker}/range/1/minute/{$date}/{$date}?adjusted=true&sort=asc&limit=1000&apiKey={$apiKey}";

        // Thêm timeout(10) bảo vệ server
        $response = Http::timeout(10)->get($url);

        if (!$response->successful()) {
            return response()->json(['error' => "VWAP $sym failed"], $response->status());
        }

        return response()->json($response->json());
    }
}