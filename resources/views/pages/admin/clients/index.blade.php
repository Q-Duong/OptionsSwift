@extends('layouts.default_auth')
@section('title', 'All Clients CRM')

@push('styles')
<style>
    /* NHÃN TRẠNG THÁI TRỰC QUAN */
    .badge { padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
    .badge-approved { background: rgba(89, 234, 30, 0.1); color: var(--primary-color, #59ea1e); border: 1px solid var(--primary-color, #59ea1e); }
    .badge-pending { background: rgba(255, 193, 7, 0.1); color: #ffc107; border: 1px solid #ffc107; }
    .badge-denied { background: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d; }
    .badge-expired { background: rgba(160, 170, 178, 0.1); color: #a0aab2; border: 1px solid #a0aab2; }

    /* CSS FORM CẬP NHẬT NHANH (GIỐNG TAB PENDING) */
    .quick-action-form { display: flex; gap: 8px; align-items: center; justify-content: flex-end; margin: 0; }
    .status-select, .duration-select { background: #05080a; border: 1px solid var(--border-color); color: #fff; padding: 6px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: 0.2s; }
    .status-select:focus, .duration-select:focus { outline: none; border-color: var(--primary-color, #59ea1e); }
    
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
                    <th>Status</th>
                    <th>Expiration Date</th>
                    <th style="text-align: right;">Quick Action (Cấp phát / Gia hạn)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    @php
                        // Xử lý logic hiển thị màu sắc tối ưu cho cả Pending và Expired
                        $isExpired = $client->expires_at && \Carbon\Carbon::now()->greaterThan($client->expires_at);
                        
                        if ($client->status === 'approved') {
                            $badgeClass = $isExpired ? 'badge-expired' : 'badge-approved';
                            $statusText = $isExpired ? 'Expired' : 'Active VIP';
                        } elseif ($client->status === 'denied') {
                            $badgeClass = 'badge-denied';
                            $statusText = 'Denied';
                        } else {
                            // Trạng thái Pending: Phân biệt Khách Mới và Khách Hết Hạn
                            if ($isExpired) {
                                $badgeClass = 'badge-expired';
                                $statusText = 'Expired';
                            } else {
                                $badgeClass = 'badge-pending';
                                $statusText = 'Pending';
                            }
                        }
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $client->name }}</strong><br>
                            <span style="color: var(--text-muted); font-size: 12px;">{{ $client->email }}</span>
                        </td>
                        
                        <td>
                            <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                        
                        <td style="font-size: 13px;">
                            @if($client->expires_at)
                                <span style="color: {{ $isExpired ? '#ff4d4d' : '#59ea1e' }}; font-weight: bold;">
                                    {{ \Carbon\Carbon::parse($client->expires_at)->format('d/m/Y H:i') }}
                                </span>
                            @elseif($client->status === 'approved')
                                <span style="color: #59ea1e; font-style: italic;">Lifetime Access</span>
                            @else
                                <span style="color: var(--text-muted);">---</span>
                            @endif
                        </td>
                        
                        <td style="text-align: right;">
                            <form action="{{ route('admin.clients.update_status', $client->id) }}" method="POST" class="quick-action-form">
                                @csrf
                                @method('PUT')
                                
                                <select name="status" class="status-select" onchange="toggleDuration(this)">
                                    <option value="pending" {{ $client->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $client->status == 'approved' ? 'selected' : '' }} style="color: #59ea1e;">Approve / Renew</option>
                                    <option value="denied" {{ $client->status == 'denied' ? 'selected' : '' }} style="color: #ff4d4d;">Deny Access</option>
                                </select>

                                <select name="duration" class="duration-select" style="{{ $client->status == 'approved' ? 'display: inline-block;' : 'display: none;' }}">
                                    <option value="7_days">7 Days Trial</option>
                                    <option value="1_month">1 Month</option>
                                    <option value="3_months">3 Months</option>
                                    <option value="lifetime" {{ is_null($client->expires_at) && $client->status == 'approved' ? 'selected' : '' }}>Lifetime</option>
                                </select>

                                <button type="submit" class="btn-execute" onclick="return confirm('Cập nhật trạng thái cho {{ $client->name }}?');">
                                    Update
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 40px;">Chưa có khách hàng nào trong hệ thống CRM.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleDuration(selectObj) {
        var form = selectObj.closest('form');
        var durationSelect = form.querySelector('.duration-select');
        
        if (selectObj.value === 'approved') {
            durationSelect.style.display = 'inline-block';
            selectObj.style.borderColor = '#59ea1e';
        } else if (selectObj.value === 'denied') {
            durationSelect.style.display = 'none';
            selectObj.style.borderColor = '#ff4d4d';
        } else {
            durationSelect.style.display = 'none';
            selectObj.style.borderColor = '#1a242c';
        }
    }

    // Tự động tô màu viền (Border) khi load trang
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.status-select').forEach(function(selectObj) {
            if(selectObj.value === 'approved') {
                selectObj.style.borderColor = '#59ea1e';
            } else if(selectObj.value === 'denied') {
                selectObj.style.borderColor = '#ff4d4d';
            }
        });
    });
</script>
@endsection