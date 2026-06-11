@extends('layouts.default_auth')
@section('title', 'Client Registration Queue')

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
                    <th style="text-align: right;">Action Layer</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingClients as $client)
                    <tr>
                        <td><strong>{{ $client->name }}</strong></td>
                        <td style="color: var(--text-muted); font-size: 13px;">{{ $client->email }}</td>
                        <td>{{ $client->created_at->format('M d, H:i') }}</td>
                        <td style="text-align: right;">
                            <form action="{{ route('admin.clients.approve', $client->id) }}" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="action-btn btn-approve" onclick="return confirm('Grant access credentials for {{ $client->name }}?');">Authorize Client</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 40px;">No US records are currently stacked inside the approval queue.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection