@extends('layouts.default_auth')
@section('admin_content')
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Cập nhật thông tin tài khoản
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form action="{{ route('save-information') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="exampleInputEmail1">Tên đăng nhập</label>
                            <input disabled type="text" class="input-control" value="{{Auth::user()->email}}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Mật khẩu mới</label>
                            <div class="row ">
                                <div class="col-lg-12">
                                    <input type="password" name="admin_password" class="input-control" id="password">
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('profile_firstname') ? 'has-error' : '' }}">
                            <label for="exampleInputEmail1">Họ và tên lót</label>
                            <input type="text" name="profile_firstname" class="input-control" value="{{Auth::user()->profile->profile_firstname}}">
                            {!! $errors->first('profile_firstname', '<div class="alert-error"><i class="fa fa-exclamation-circle"></i> :message</div>') !!}
                        </div>
                        <div class="form-group {{ $errors->has('profile_lastname') ? 'has-error' : '' }}">
                            <label for="exampleInputEmail1">Tên</label>
                            <input type="text" name="profile_lastname" class="input-control" value="{{Auth::user()->profile->profile_lastname}}">
                            {!! $errors->first('profile_lastname', '<div class="alert-error"><i class="fa fa-exclamation-circle"></i> :message</div>') !!}
                        </div>
                        <div class="form-group {{ $errors->has('profile_email') ? 'has-error' : '' }}">
                            <label for="exampleInputEmail1">Email cá nhân</label>
                            <input type="text" name="profile_email" class="input-control" value="{{Auth::user()->profile->profile_email}}">
                            {!! $errors->first('profile_email', '<div class="alert-error"><i class="fa fa-exclamation-circle"></i> :message</div>') !!}
                        </div>
                        <div class="form-group {{ $errors->has('profile_phone') ? 'has-error' : '' }}">
                            <label for="exampleInputEmail1">Số điện thoại</label>
                            <input type="text" name="profile_phone" class="input-control" value="{{Auth::user()->profile->profile_phone}}">
                            {!! $errors->first('profile_phone', '<div class="alert-error"><i class="fa fa-exclamation-circle"></i> :message</div>') !!}
                        </div>
                        <div class="form-group {{ $errors->has('profile_gender') ? 'has-error' : '' }}">
                            <label for="exampleInputEmail1">Giới tính</label><br>   
                            <label for="nam" class="accent-l">Nam</label>
                            <input type="radio" name="profile_gender" value="0" id="nam" {{Auth::user()->profile->profile_gender == 0 ? 'checked' : '' }} class="accent">
                            <label for="nu" class="accent-l">Nữ</label>
                            <input type="radio" name="profile_gender" value="1" id="nu" {{Auth::user()->profile->profile_gender == 1 ? 'checked' : '' }} class="accent">
                            {!! $errors->first('sex', '<div class="alert-error"><i class="fa fa-exclamation-circle"></i> :message</div>') !!}
                        </div>
                        <div class="form-group {{ $errors->has('day_of_birth') ? 'has-error' : '' }}">
                            <label for="exampleInputEmail1">Ngày sinh</label>
                            <input type="date" name="day_of_birth" value="{{ Auth::user()->profile->day_of_birth }}"
                                class="input-control">
                            {!! $errors->first(
                                'day_of_birth',
                                '<div class="alert-error"><i class="fa fa-exclamation-circle"></i> :message</div>',
                            ) !!}
                        </div>
                        <button type="submit" class="primary-btn-submit">Cập nhật thông tin</button>
                    </form>    
                </div>
            </div>
        </section>
    </div>
</div>
@endsection