@extends('layouts.default_auth')
@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Thêm danh mục bài viết
                    <span class="tools pull-right">
                        <a href="{{ route('post_category.index') }}" class="primary-btn-submit">Quản lý</a>
                        <a class="fa fa-chevron-down" href="javascript:;"></a>
                    </span>
                </header>
                <div class="panel-body">
                    <div class="position-center">
                        <form action="{{ route('post_category.store') }}" method="post">
                            @csrf
                            <div class="form-group @error('post_category_name') has-error @enderror">
                                <label for="exampleInputEmail1">Tên danh mục bài viết</label>
                                <input type="text" name="post_category_name" class="input-control" id="slug"
                                    placeholder="Điền tên danh mục bài viết" onkeyup="ChangeToSlug();">
                                @error('post_category_name')
                                    <div class="alert-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group hidden">
                                <label for="exampleInputEmail1">Slug danh mục bài viết</label>
                                <input type="text" name="post_category_slug" class="input-control" id="convert_slug"
                                    placeholder="Điền Slug danh mục bài viết" readonly>
                            </div>
                            <button type="submit" class="primary-btn-submit">Thêm danh mục bài
                                viết</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
