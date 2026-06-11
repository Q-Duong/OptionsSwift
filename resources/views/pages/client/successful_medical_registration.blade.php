@extends('layouts.default')
@section('content')
@section('title', 'Đăng ký khám thành công - ')
<div class="container text-center">
    <div class="cart-empty">
        <img src="{{asset('frontend/img/sucess.png')}}"
        alt="" />
        <p>Đăng ký khám thành công !</p>
        <h6>Cảm ơn Quý khách đã gửi thông tin đến Công Ty TNHH Đầu Tư Trang Thiết Bị Y Tế Nam Khánh Linh. Bộ phận chăm sóc khách hàng của chúng tôi sẽ liên hệ cho Quý khách trong thời gian sớm nhất. Chân thành cảm ơn Quý khách đã sử dụng dịch vụ của chúng tôi!</h6>
        <h4><a href="{{ route('home.index')}}"><i class="fas fa-arrow-circle-left"></i> VỀ TRANG CHỦ</a></h4>
        <h5></h5>
    </div>
</div>
@endsection

