@extends('layouts.default_auth')
@section('title', 'HTML Widgets Manager')

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
                    <th>Identifier Key</th>
                    <th>Last Updated</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($settings as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td style="color: var(--primary-neon); font-family: monospace; font-weight: bold;">{{ $item->key }}</td>
                        <td>{{ $item->updated_at->format('M d, Y') }}</td>
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