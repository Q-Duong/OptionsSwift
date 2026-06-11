@extends('layouts.default_auth')
@section('title', isset($setting) ? 'Edit Code Block' : 'Create New Widget')

@style
<style>
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 6px; color: var(--text-muted); font-size: 12px; text-transform: uppercase; }
    .form-group input, .form-group textarea { width: 100%; padding: 12px; background: #05080a; border: 1px solid var(--border-color); color: #fff; border-radius: 4px; font-size: 14px; }
    .form-group textarea { height: 320px; font-family: monospace; color: var(--primary-neon); resize: vertical; }
    .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--admin-accent); }
    .error-text { color: var(--admin-accent); font-size: 12px; margin-top: 5px; display: block; }
</style>
@endstyle

@section('content')
<div class="panel-card" style="max-width: 850px; margin: 0 auto;">
    <div class="panel-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 15px; margin-bottom: 25px;">
        <h2>{{ isset($setting) ? 'Modify System Widget' : 'Build Custom Data Block' }}</h2>
        <a href="{{ route('admin.html.index') }}" style="color: var(--text-muted); font-size: 13px; text-decoration: none;">&larr; Back to List</a>
    </div>

    <form action="{{ isset($setting) ? route('admin.html.update', $setting->id) : route('admin.html.store') }}" method="POST">
        @csrf
        @if(isset($setting))
            @method('PUT')
        @endif
        
        <div class="form-group">
            <label>Identifier Key (System deployment variable)</label>
            <input type="text" name="key" value="{{ old('key', $setting->key ?? '') }}" placeholder="e.g., client_dashboard_html" required {{ isset($setting) ? 'readonly' : '' }}>
            @error('key') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Raw HTML Script Injection</label>
            <textarea name="value" placeholder="<div class='custom-widget'>...</div>" required>{{ old('value', $setting->value ?? '') }}</textarea>
            @error('value') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="action-btn btn-add" style="width: 100%; padding: 14px; font-size: 14px;">
            {{ isset($setting) ? 'Commit Realtime Updates' : 'Deploy To Storage File' }}
        </button>
    </form>
</div>
@endsection