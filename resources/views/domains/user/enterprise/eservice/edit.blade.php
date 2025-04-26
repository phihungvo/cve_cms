@extends('layouts.in')

@section('body')
<div class="px-20 py-6">
    <div class="box flex items-center px-5 py-6 bg-white rounded-lg shadow-md">
        <div class="nav nav-tabs flex overflow-auto whitespace-nowrap" role="tablist">
            <a href="{{ route('user.permission.edit', ['role_id' => $row->role_id ?? 0]) }}"
                class="p-4 {{ ($ROUTE === 'user.permission.edit') ? 'active' : '' }}" role="tab">
                {{ 'Edit Permission ' . ($row->role->name ?? 'Permission for Role #' . ($row->role_id ?? 'Unknown')) }}
            </a>
        </div>
    </div>
</div>

<div class="tab-content px-20 ">
    <div class="tab-pane active" role="tabpanel">
        @yield('content')

        <!-- Success message -->
        @if (session('success'))
            <div class="alert alert-success p-5">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error messages -->
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger p-5">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main form container wrapped in a box -->
        <div class="box p-5 bg-white rounded-lg shadow-md">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-blue-600 mb-5 border-b border-blue-600 pb-1">Role</h3>
                <p class="p-2 border rounded bg-gray-100 text-gray-800">
                    {{ $row->role_name ?? ($row->role->name ?? 'Unknown Role') }}
                </p>
            </div>

            <!-- Permission edit form -->
            <form method="POST" action="{{ route('user.permission.update', ['role_id' => $row->role_id ?? 0]) }}"
                class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Action switches -->
                <div class="form-group">
                    <label class="block font-semibold text-gray-700 mb-1">Actions</label>
                    <div class="grid grid-cols-1 gap-3 p-1">
                        @foreach ($actions as $action)
                            <div class="form-check p-2">
                                <input type="checkbox" name="actions[]" value="{{ $action['id'] }}"
                                    class="form-check-switch" id="action-{{ $action['id'] }}"
                                    {{ in_array($action['id'], $selected_actions) ? 'checked' : '' }}>
                                <label class="form-check-label text-gray-800" for="action-{{ $action['id'] }}">
                                    {{ ucfirst($action['name']) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit and Cancel buttons -->
                <div class="flex justify-end space-x-6 mt-5">
                    <button type="submit"
                        class="btn btn-primary bg-blue-600 text-white px-3 py-2 rounded-md text-sm hover:bg-blue-700 transition">
                        Submit
                    </button>
                    <span class="w-2 invisible">.</span> <!-- Tạo khoảng trống -->
                    <a href="{{ route('user.permission.index') }}"
                        class="btn btn-secondary bg-gray-300 text-gray-800 px-3 py-2 rounded-md text-sm hover:bg-gray-400 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@push('styles')
    <style>
        .form-group {
            margin-bottom: 1.5rem;
        }

        /* Toggle switch styles */
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-check-switch {
            display: none;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        /* Custom toggle switch styling */
        .form-check-switch+.form-check-label::before {
            content: '';
            display: inline-block;
            width: 2rem;
            height: 1rem;
            background-color: #d1d5db;
            border-radius: 9999px;
            position: relative;
            transition: background-color 0.3s;
        }

        .form-check-switch+.form-check-label::after {
            content: '';
            display: inline-block;
            width: 0.8rem;
            height: 0.8rem;
            background-color: white;
            border-radius: 9999px;
            position: absolute;
            top: 0.1rem;
            left: 0.1rem;
            transition: transform 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-check-switch:checked+.form-check-label::before {
            background-color: #2563eb;
        }

        .form-check-switch:checked+.form-check-label::after {
            transform: translateX(1rem);
        }

        /* Button and alert styles */
        .btn-primary,
        .btn-secondary {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .alert {
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }
    </style>
@endpush