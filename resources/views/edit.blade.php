@extends('layouts.app')

@section('content')
    <h1>Edit Item</h1>

    <form action="{{ route('update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $item->name }}" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="Allowed" {{ $item->status === 'Allowed' ? 'selected' : '' }}>Allowed</option>
                <option value="Prohibited" {{ $item->status === 'Prohibited' ? 'selected' : '' }}>Prohibited</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection