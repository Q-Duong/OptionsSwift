@extends('layouts.default_auth')
@section('title', 'All Clients CRM')

@push('styles')
<style>
    /* NHÃN TRẠNG THÁI TRỰC QUAN */
    .badge { padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
    .badge-active { background: rgba(89, 234, 30, 0.1); color: var(--primary-color, #59ea1e); border: 1px solid var(--primary-color, #59ea1e); }
    .badge-denied { background: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d; }

    /* CSS FORM CẬP NHẬT NHANH */
    .quick-action-form { display: flex; gap: 8px; align-items: center; justify-content: flex-end; margin: 0; }
    .status-select { background: #05080a; border: 1px solid var(--border-color); color: #fff; padding: 6px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: 0.2s; outline: none; }
    .status-select:focus { border-color: var(--primary-color, #59ea1e); }
    
    .btn-execute { background: transparent; border: 1px solid var(--primary-color, #59ea1e); color: var(--primary-color, #59ea1e); padding: 6px 15px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; transition: 0.2s; text-transform: uppercase; }
    .btn-execute:hover { background: rgba(89, 234, 30, 0.1); transform: translateY(-1px); }
</style>
@endpush

@section('content')
<div class="panel-card">
    <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Master Client Directory</h2>
        <span style="color: var(--text-muted); font-size: 13px;">Total Records: <strong>{{ $clients->count() }}</strong></span>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Client Info</th>
                    <th>Account Status</th>
                    <th style="text-align: right;">Admin Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    @php
                        // Logic giờ chỉ còn đúng 2 trạng thái cốt lõi
                        $isActive = $client->status === 'active';
                        $badgeClass = $isActive ? 'badge-active' : 'badge-denied';
                        $statusText = $isActive ? 'Active' : 'Denied / Banned';
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $client->name }}</strong><br>
                            <span style="color: var(--text-muted); font-size: 12px;">{{ $client->email }}</span>
                        </td>
                        
                        <td>
                            <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                        
                        <td style="text-align: right;">
                            <form action="{{ route('admin.clients.update_status', $client->id) }}" method="POST" class="quick-action-form">
                                @csrf
                                @method('PUT')
                                
                                <select name="status" class="status-select" onchange="updateBorderColor(this)">
                                    <option value="active" {{ $client->status == 'active' ? 'selected' : '' }} style="color: #59ea1e;">Active Access</option>
                                    <option value="denied" {{ $client->status == 'denied' ? 'selected' : '' }} style="color: #ff4d4d;">Deny Access</option>
                                </select>

                                <select name="is_vip" class="status-select" style="margin-right: 5px;">
                                    <option value="0" {{ !$client->is_vip ? 'selected' : '' }}>Regular User</option>
                                    <option value="1" {{ $client->is_vip ? 'selected' : '' }} style="color: #ffc107;">VIP / Lifetime</option>
                                </select>

                                <button type="submit" class="btn-execute" onclick="return confirm('Change access status for {{ $client->name }}?');">
                                    Update
                                </button>
                            </form>
                        </td>
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 40px;">No clients found in the CRM.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Hàm xử lý đổi màu viền mượt mà khi Admin chọn trạng thái
    function updateBorderColor(selectObj) {
        if (selectObj.value === 'active') {
            selectObj.style.borderColor = '#59ea1e';
        } else if (selectObj.value === 'denied') {
            selectObj.style.borderColor = '#ff4d4d';
        }
    }

    // Tự động tô màu viền (Border) chuẩn xác lúc trang vừa load xong
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.status-select').forEach(function(selectObj) {
            updateBorderColor(selectObj); // Gọi lại hàm trên cho gọn code
        });
    });
</script>
@endsection