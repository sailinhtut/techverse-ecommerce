@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5">
        <p class="lg:text-lg font-semibold mb-3">
            {{ isset($edit_category) ? 'Edit Category' : 'Add Category' }}
        </p>

        @if ($errors->any())
            <div class="alert alert-error max-w-[300px] p-2 my-2">
                <ul class="mb-0 p-0 text-xs text-justify">
                    <li class="font-bold mb-1">Errors</li>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ isset($edit_category) ? route('admin.dashboard.category.id.post', ['id' => $edit_category->id]) : route('admin.dashboard.category.post') }}"
            method="POST" class="lg:w-[300px] flex flex-col gap-3">
            @csrf

            <label for="name" class="text-sm">Category Name</label>
            <input type="text" name="name" id="name" class="input input-sm"
                value="{{ old('name', $edit_category->name ?? '') }}" required>

            <label for="description" class="text-sm">Description (Optional)</label>
            <textarea name="description" id="description" class="textarea textarea-sm" rows="4">{{ old('description', $edit_category->description ?? '') }}</textarea>

            <button type="submit" class="btn btn-primary w-fit">
                {{ isset($edit_category) ? 'Update Category' : 'Add Category' }}
            </button>

        </form>
    </div>
@endsection
