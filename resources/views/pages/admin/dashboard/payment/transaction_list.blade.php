@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Transaction List</p>

        <div class="card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="w-[150px]">Transaction ID</th>
                            <th class="w-[150px]">Payment ID</th>
                            <th class="w-[150px]">User</th>
                            <th class="w-[100px]">Type</th>
                            <th class="w-[100px]">Status</th>
                            <th class="w-[150px]">Amount</th>
                            <th class="w-[150px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}.
                                </td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $transaction['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">
                                        Transaction #{{ $transaction['id'] }}</p>
                                </td>
                                <td>{{ $transaction['payment_id'] ?? '-' }}</td>
                                <td>{{ $transaction['user']['name'] ?? '-' }}</td>
                                <td>{{ ucfirst($transaction['type']) }}</td>
                                <td>
                                    @php
                                        $color = match ($transaction['status']) {
                                            'pending' => 'badge-warning',
                                            'completed' => 'badge-success',
                                            'failed' => 'badge-error',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div class="badge {{ $color }} badge-outline capitalize">
                                        {{ $transaction['status'] }}</div>
                                </td>
                                <td>${{ number_format($transaction['amount'], 2) }}</td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button
                                                    onclick="document.getElementById('detail_modal_{{ $transaction['id'] }}').showModal()">
                                                    View Details
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $transaction['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            {{-- Transaction Modal --}}
                            <dialog id="detail_modal_{{ $transaction['id'] }}" class="modal">
                                <div class="modal-box max-h-[85vh] max-w-2xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">
                                        Transaction #{{ $transaction['id'] }}
                                    </h3>

                                    <div class="text-sm space-y-2">
                                        <p><strong>Transaction ID:</strong> {{ $transaction['id'] }}</p>
                                        <p><strong>Payment ID:</strong> {{ $transaction['payment_id'] ?? '-' }}</p>
                                        <p><strong>User:</strong> {{ $transaction['user']['name'] ?? '-' }}</p>
                                        <p><strong>Reference:</strong> {{ $transaction['reference'] ?? '-' }}</p>
                                        <p><strong>Type:</strong> {{ ucfirst($transaction['type']) }}</p>
                                        <p><strong>Status:</strong> <span
                                                class="badge {{ $color }} badge-outline">{{ ucfirst($transaction['status']) }}</span>
                                        </p>
                                        <p><strong>Amount:</strong> ${{ number_format($transaction['amount'], 2) }}</p>
                                        <p><strong>Created At:</strong> {{ $transaction['created_at'] }}</p>
                                        <p><strong>Updated At:</strong> {{ $transaction['updated_at'] }}</p>
                                    </div>

                                    <div class="modal-action mt-6">
                                        <form method="dialog">
                                            <button class="btn">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
                            <dialog id="delete_modal_{{ $transaction['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                    <p class="py-2 mb-0 text-sm">
                                        Are you sure you want to delete
                                        <span class="italic text-error">Transaction
                                            #{{ $transaction['id'] }}</span>
                                        ?
                                    </p>
                                    <div class="modal-action mt-0">
                                        <form method="dialog">
                                            <button class="btn">Cancel</button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.payment.transaction.id.delete', ['id' => $transaction['id']]) }}">
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
                        <span class="font-semibold">{{ $transactions->firstItem() }}</span> –
                        <span class="font-semibold">{{ $transactions->lastItem() }}</span> of
                        <span class="font-semibold">{{ $transactions->total() }}</span> results
                    </div>
                    <div class="join">
                        @if ($transactions->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $transactions->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif
                        @for ($i = 1; $i <= $transactions->lastPage(); $i++)
                            <a href="{{ $transactions->url($i) }}"
                                class="join-item btn btn-sm {{ $transactions->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if ($transactions->hasMorePages())
                            <a href="{{ $transactions->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
