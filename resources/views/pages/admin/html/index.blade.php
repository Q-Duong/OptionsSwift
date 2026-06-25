@extends('layouts.default_auth')
@section('title', 'HTML Widgets Manager')

@push('styles')
<style>
    .badge {
        padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-left: 10px;
    }
    .badge-scanner { background: rgba(89, 234, 30, 0.1); color: var(--primary-neon); border: 1px solid rgba(89, 234, 30, 0.3); }
    .badge-flow { background: rgba(160, 170, 178, 0.1); color: var(--text-muted); border: 1px solid rgba(160, 170, 178, 0.3); }
</style>
@endpush

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <h2>System Widgets</h2>
        <a href="{{ route('admin.html.create') }}" class="action-btn btn-add">+ New Widget</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Identifier Key / Type</th>
                    <th>Last Updated</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($settings as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>
                            <strong style="color: var(--primary-neon); font-family: monospace;">{{ $item->key }}</strong>
                            @if(strpos(strtolower($item->key), 'scanner') !== false)
                                <span class="badge badge-scanner">Scanner Hub</span>
                            @else
                                <span class="badge badge-flow">Data Flow</span>
                            @endif
                        </td>
                        <td>{{ $item->updated_at->format('M d, Y - H:i') }}</td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.html.edit', $item->id) }}" class="action-btn btn-edit">Edit</a>
                                <form action="{{ route('admin.html.delete', $item->id) }}" method="POST" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete" onclick="return confirm('Delete this widget permanently?');">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">No widget database fragments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection