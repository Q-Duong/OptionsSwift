@extends('layouts.default_auth')
@section('title', 'Client Registration Queue')

@push('styles')
<style>
    /* CSS Tối ưu cho Form Duyệt Nhanh trên 1 dòng */
    .quick-action-form {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: flex-end;
        margin: 0;
    }
    .status-select, .duration-select {
        background: #05080a;
        border: 1px solid var(--border-color);
        color: #fff;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
    }
    .status-select:focus, .duration-select:focus {
        outline: none;
        border-color: var(--primary-color, #59ea1e);
    }
    .btn-execute {
        background: transparent;
        border: 1px solid var(--primary-color, #59ea1e);
        color: var(--primary-color, #59ea1e);
        padding: 6px 15px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
        text-transform: uppercase;
    }
    .btn-execute:hover {
        background: rgba(89, 234, 30, 0.1);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <h2>Awaiting Gate Approvals</h2>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Email Target</th>
                    <th>Signed Up</th>
                    <th style="text-align: right;">Action Layer (Duyệt / Từ chối)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingClients as $client)
                    <tr>
                        <td><strong>{{ $client->name }}</strong></td>
                        <td style="color: var(--text-muted); font-size: 13px;">{{ $client->email }}</td>
                        <td>{{ $client->created_at->format('M d, H:i') }}</td>
                        <td style="text-align: right;">
                            
                            <form action="{{ route('admin.clients.update_status', $client->id) }}" method="POST" class="quick-action-form">
                                @csrf
                                @method('PUT')
                                
                                <select name="status" class="status-select" onchange="toggleDuration(this)">
                                    <option value="pending" selected>Pending</option>
                                    <option value="approved" style="color: #59ea1e;">Approve</option>
                                    <option value="denied" style="color: #ff4d4d;">Deny</option>
                                </select>

                                <select name="duration" class="duration-select" style="display: none;">
                                    <option value="7_days">7 Days Trial</option>
                                    <option value="1_month">1 Month</option>
                                    <option value="3_months">3 Months</option>
                                    <option value="lifetime">Lifetime Access</option>
                                </select>

                                <button type="submit" class="btn-execute" onclick="return confirm('Xác nhận thao tác đối với tài khoản {{ $client->name }}?');">
                                    Execute
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 40px;">No user records are currently stacked inside the approval queue.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleDuration(selectObj) {
        // Tìm ra ô chọn duration nằm cùng trong 1 form với ô status vừa thay đổi
        var form = selectObj.closest('form');
        var durationSelect = form.querySelector('.duration-select');
        
        if (selectObj.value === 'approved') {
            durationSelect.style.display = 'inline-block';
            selectObj.style.borderColor = '#59ea1e'; // Đổi viền xanh
        } else if (selectObj.value === 'denied') {
            durationSelect.style.display = 'none';
            selectObj.style.borderColor = '#ff4d4d'; // Đổi viền đỏ
        } else {
            durationSelect.style.display = 'none';
            selectObj.style.borderColor = '#1a242c'; // Trả về viền xám mặc định
        }
    }
</script>
@endsection