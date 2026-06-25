<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GammaController extends Controller
{
    public function fetchGammaData(Request $request)
    {
        // 1. Validate dữ liệu gửi lên từ Javascript cho chắc cú
        $request->validate([
            'symbol' => 'required|string',
            'date'   => 'required', // Bác có thể thêm rule 'date' nếu cần kiểm tra kỹ
        ]);

        $ticker = strtoupper($request->input('symbol'));
        $date = $request->input('date');

        // ==========================================================
        // 2. GỌI API THẬT HOẶC QUERY DATABASE (Bác sẽ code thật ở đây)
        // Ví dụ: $data = PolygonApi::getGamma($ticker, $date);
        // ==========================================================

        // DƯỚI ĐÂY LÀ DỮ LIỆU GIẢ (MOCK DATA) ĐỂ BÁC TEST TRƯỚC
        // Khi JS nhận được cục này, nó sẽ lấy các số này đắp lên giao diện
        $mockData = [
            'flow_call_wall'        => $ticker . ' 4500',
            'total_call_premium'    => '2,500,000',
            'flow_put_wall'         => $ticker . ' 4200',
            'total_put_premium'     => '1,800,000',
            'gamma_call_wall'       => $ticker . ' 4600',
            'gamma_put_wall'        => $ticker . ' 4100',
            'net_gamma_exposure'    => '350,000',
            'gamma_flip_estimate'   => $ticker . ' 4350',
            'net_premium_flow'      => '700,000'
        ];

        // 3. Trả về định dạng JSON chuẩn (Bắt buộc)
        return response()->json($mockData, 200);
    }
}
