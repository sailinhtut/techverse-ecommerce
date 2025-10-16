@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Payment List</p>

        <div class="card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="w-[150px]">Payment ID</th>
                            <th class="w-[150px]">Invoice Number</th>
                            <th class="w-[150px]">Amount</th>
                            <th class="w-[150px]">Status</th>
                            <th class="w-[150px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration + ($payments->currentPage() - 1) * $payments->perPage() }}.</td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $payment['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">
                                        Payment #{{ $payment['id'] }}</p>
                                </td>
                                <td>{{ $payment['invoice']['invoice_number'] ?? '-' }}</td>
                                <td>${{ number_format($payment['amount'], 2) }}</td>
                                <td>

                                    @php
                                        $color = match ($payment['invoice']['status'] ?? 'pending') {
                                            'unpaid' => 'badge-error',
                                            'paid' => 'badge-success',
                                            'refunded' => 'badge-warning',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div class="badge {{ $color }} badge-outline capitalize">
                                        {{ $payment['invoice']['status'] ?? 'pending' }}</div>
                                </td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button
                                                    onclick="document.getElementById('detail_modal_{{ $payment['id'] }}').showModal()">
                                                    View Details
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $payment['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            {{-- Payment Modal --}}
                            <dialog id="detail_modal_{{ $payment['id'] }}" class="modal">
                                <div class="modal-box max-h-[85vh] max-w-2xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">
                                        Payment #{{ $payment['id'] }}
                                    </h3>

                                    <div class="text-sm space-y-2">
                                        <p><strong>Payment ID:</strong> {{ $payment['id'] }}</p>
                                        <p><strong>Invoice:</strong> {{ $payment['invoice']['invoice_number'] ?? '-' }}</p>
                                        <p><strong>Amount:</strong> ${{ number_format($payment['amount'], 2) }}</p>
                                        <p><strong>Status:</strong> <span
                                                class="badge {{ $color }} badge-outline">{{ ucfirst($payment['invoice']['status'] ?? 'pending') }}</span>
                                        </p>
                                        <p><strong>Transaction ID:</strong> {{ $payment['transaction_id'] ?? '-' }}</p>
                                        <p><strong>Payment Method:</strong> {{ $payment['payment_method']['name'] ?? '-' }}
                                        </p>
                                        <p><strong>Details:</strong> {{ json_encode($payment['details']) ?? '-' }}</p>
                                        <p><strong>Created At:</strong> {{ $payment['created_at'] }}</p>
                                        <p><strong>Updated At:</strong> {{ $payment['updated_at'] }}</p>
                                    </div>

                                    <div class="modal-action mt-6">
                                        <form method="dialog">
                                            <button class="btn">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>

                            <dialog id="delete_modal_{{ $payment['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                    <p class="py-2 mb-0 text-sm">
                                        Are you sure you want to delete
                                        <span class="italic text-error">Payment
                                            #{{ $payment['id'] }}</span>
                                        ?
                                    </p>
                                    <div class="modal-action mt-0">
                                        <form method="dialog">
                                            <button class="btn">Cancel</button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.payment.payment.id.delete', ['id' => $payment['id']]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="flex justify-between items-center py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $payments->firstItem() }}</span> –
                        <span class="font-semibold">{{ $payments->lastItem() }}</span> of
                        <span class="font-semibold">{{ $payments->total() }}</span> results
                    </div>
                    <div class="join">
                        @if ($payments->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $payments->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif
                        @for ($i = 1; $i <= $payments->lastPage(); $i++)
                            <a href="{{ $payments->url($i) }}"
                                class="join-item btn btn-sm {{ $payments->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if ($payments->hasMorePages())
                            <a href="{{ $payments->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
