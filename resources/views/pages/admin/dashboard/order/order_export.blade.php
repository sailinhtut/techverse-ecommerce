@php
    $site_currency = getParsedTemplate('site_currency');
@endphp
@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-3 lg:p-5 min-h-screen">

        <div class="mb-4">
            <button onclick="history.back()" class="btn btn-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </button>
        </div>

        <div class="w-fit max-w-full text-sm bg-base-300 rounded px-2 overflow-x-auto ">
            <div class="breadcrumbs text-sm my-0 py-1">
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard.order.get') }}" class="btn btn-xs btn-ghost">
                            Orders
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.dashboard.order.export-order.get') }}" class="btn btn-xs btn-ghost">
                            Export Orders
                        </a>
                    </li>
                </ul>

            </div>
        </div>


        <p class="text-lg font-semibold mt-5 mb-3">Export Orders</p>


        <div class="tabs tabs-box bg-base-100 shadow-none">
            <input type="radio" name="active_tab" class="tab" aria-label="General" checked="checked" />
            <div class="tab-content">
                <div class="w-full flex flex-col items-start gap-3 mt-3">
                    <form method="POST" action="{{ route('admin.dashboard.order.export-order.post') }}"
                        class="w-full border border-base-300 rounded-box p-5" x-data="{ submitting: false }"
                        @submit="submitting=true">
                        @csrf
                        @method('POST')
                        <div class="grid grid-cols-1 gap-3">
                            <div class="flex flex-col gap-1">
                                <label class="text-sm">Export Name</label>
                                <input type="text" name="export_name" class="input" value="{{ old('export_name') }}">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm">Export Start Date</label>
                                <input type="datetime-local" name="export_start_date" value="{{ old('export_start_date') }}"
                                    class="input" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm">Export End Date</label>
                                <input type="datetime-local" name="export_end_date" value="{{ old('export_end_date') }}"
                                    class="input" />
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-5" :disabled="submitting">
                            <span x-show="submitting" class="loading loading-spinner loading-sm mr-2"></span>
                            <span x-show="submitting">Exporting..</span>
                            <span x-show="!submitting">
                                Export Orders
                            </span>
                        </button>
                    </form>

                </div>
            </div>

        </div>

    </div>
@endsection
