@extends('layouts.app')

@section('content')
    <h1>Google Sheets Data </h1>

    <!-- Google Sheet URL Form -->
    <form action="{{ route('set-google-sheet-url') }}" method="POST" class="mb-4">
        @csrf
        <div class="input-group">
            <input type="text" name="google_sheet_url" class="form-control" placeholder="Enter Google Sheet URL" required>
            <button type="submit" class="btn btn-primary">Set URL</button>
        </div>
    </form>

    <!-- Generate and Clear Buttons -->
    <div class="mb-4">
        <form action="{{ route('generate-rows') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success btn-custom">Generate 1000 records</button>
        </form>
        <form action="{{ route('clear-table') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger btn-custom">Clear Table</button>
        </form>
    </div>

    <!-- Data Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <a href="{{ route('edit', $item->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('delete', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Create New Item Form -->
    <h2>Create New Item</h2>
    <form action="{{ route('store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="Allowed">Allowed</option>
                <option value="Prohibited">Prohibited</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
@endsection