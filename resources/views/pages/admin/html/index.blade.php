@extends('layouts.default_auth')
@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/css/support/pagination.css') }}" type="text/css" as="style" />
    <link rel="stylesheet" href="{{ versionResource('assets/css/support/accountant.css') }}" type="text/css" as="style" />
@endpush
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel-heading">
            Quản lý Giao diện Client
            <span class="tools pull-right">
                <a href="{{ route('admin.html.create') }}"  class="btn-primary" style="width: auto; padding: 10px 20px;">+ Thêm Khối HTML</a>
            </span>
        </div>
        <div class="table-responsive table-content">
            <div id="table-scroll" class="table-scroll">
                <table class="table">
                    <thead>
                        <tr class="section-title">
                            <th>ID</th>
                            <th>Key (Tên định danh)</th>
                            <th>Cập nhật lần cuối</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($settings as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td style="color: var(--primary-color); font-family: monospace;">{{ $item->key }}</td>
                                <td>{{ $item->updated_at->format('d/m/Y H:i') }}</td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.html.edit', $item->id) }}" class="btn-sm btn-edit">Sửa</a>

                                    <form action="{{ route('admin.html.delete', $item->id) }}" method="POST"
                                        style="display:inline-block; margin-left: 10px;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-sm btn-delete"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa khối này?');">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">Chưa
                                    có dữ liệu. Hãy thêm khối HTML mới.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ versionResource('assets/js/support/essential.js') }}"></script>
@endpush
