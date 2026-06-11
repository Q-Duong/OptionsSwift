@extends('layouts.default_auth')
@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Thông tin tài khoản
                </header>

                <div class="panel-body">
                    <div class="position-center">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Họ và tên lót</label>
                            <input disabled type="text" class="input-control"
                                value="{{ Auth::user()->profile->profile_firstname }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Tên </label>
                            <input disabled type="text" class="input-control"
                                value="{{ Auth::user()->profile->profile_lastname }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Tên đăng nhập</label>
                            <input disabled type="text" class="input-control" value="{{ Auth::user()->email }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Số điện thoại</label>
                            <input disabled type="text" class="input-control"
                                value="{{ Auth::user()->profile->profile_phone }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Giới tính</label>
                            <input disabled type="text" class="input-control"
                                value="{{ Auth::user()->profile->profile_gender == 0 ? 'Nam' : 'Nữ' }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Ngày sinh</label>
                            <input disabled type="text" class="input-control"
                                value="{{ Carbon\Carbon::parse(Auth::user()->profile->day_of_birth)->format('d/m/Y') }}">
                        </div>
                        <a href="{{ route('settings') }}" class="primary-btn-submit"><i class="fa fa-cog"></i> Cài
                            đặt tài khoản</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
