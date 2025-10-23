@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Payment Methods</p>

        <button class="btn btn-primary" onclick="create_payment_method_modal.showModal()">Create Payment Method</button>

        <div class="card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="w-[150px]">Name</th>
                            <th class="w-[150px]">Code</th>
                            <th class="w-[120px]">Type</th>
                            <th class="w-[100px]">Enabled</th>
                            <th class="w-[100px]">Priority</th>
                            <th class="w-[200px]">Description</th>
                            <th class="w-[120px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paymentmethods as $method)
                            <tr>
                                <td>{{ $loop->iteration + ($paymentmethods->currentPage() - 1) * $paymentmethods->perPage() }}.
                                </td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $method['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">
                                        {{ $method['name'] }}
                                    </p>
                                </td>
                                <td>{{ $method['code'] }}</td>
                                <td class="capitalize">{{ $method['type'] }}</td>
                                <td>
                                    @if ($method['enabled'])
                                        <span class="badge badge-success badge-outline">Enabled</span>
                                    @else
                                        <span class="badge badge-error badge-outline">Disabled</span>
                                    @endif
                                </td>
                                  <td class="capitalize">{{ $method['priority'] }}</td>
                                <td>{{ $method['description'] ?? '-' }}</td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button
                                                    onclick="document.getElementById('detail_modal_{{ $method['id'] }}').showModal()">
                                                    View Details
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button"
                                                    onclick="document.getElementById('edit_modal_{{ $method['id'] }}').showModal()">
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $method['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>


                            <dialog id="detail_modal_{{ $method['id'] }}" class="modal">
                                <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">Payment Method Details</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm">ID</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $method['id'] }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Name</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $method['name'] }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Code</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $method['code'] }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Type</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none capitalize"
                                                value="{{ $method['type'] }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Enabled</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $method['enabled'] ? 'Yes' : 'No' }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Priority</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ ucfirst($method['priority']) }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Created At</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ \Carbon\Carbon::parse($method['created_at'])->format('Y-m-d H:i') }}"
                                                readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Description</label>
                                            <textarea class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>{{ $method['description'] ?? '-' }}</textarea>
                                        </div>
                                    </div>

                                    {{-- Conditional Information Section --}}
                                    <div class="mt-5 border-t border-base-300 pt-4 space-y-3">
                                        @if ($method['code'] === 'cod')
                                            <div>
                                                <p class="font-semibold text-sm mb-2">COD Instruction</p>
                                                <div class="bg-base-200 rounded-box p-3 text-sm">
                                                    {{ $method['payment_attributes']['instruction'] ?? 'No instruction provided.' }}
                                                </div>
                                            </div>
                                        @elseif ($method['code'] === 'direct_bank_transfer')
                                            <div>
                                                <p class="font-semibold text-sm mb-2">Bank Accounts</p>
                                                <div class="space-y-3">
                                                    @forelse ($method['payment_attributes']['bank_accounts'] ?? [] as $bank)
                                                        <div class="bg-base-200 rounded-box p-3 text-sm space-y-1">
                                                            <div><strong>Account ID:</strong>
                                                                {{ $bank['account_id'] ?? '-' }}</div>
                                                            <div><strong>Account Name:</strong>
                                                                {{ $bank['account_name'] ?? '-' }}</div>
                                                            <div><strong>Bank Name:</strong>
                                                                {{ $bank['bank_name'] ?? '-' }}</div>
                                                            <div><strong>Branch Name:</strong>
                                                                {{ $bank['branch_name'] ?? '-' }}</div>
                                                        </div>
                                                    @empty
                                                        <p class="text-sm italic text-gray-500">No bank account information
                                                            available.</p>
                                                    @endforelse
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="modal-action mt-6">
                                        <form method="dialog">
                                            <button class="btn btn-primary w-full">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>

                            <dialog id="edit_modal_{{ $method['id'] }}" class="modal">
                                <div x-data='{
                                    name: @json($method['name']),
                                    paymentType: @json($method['code']),
                                    enabled: @json($method['enabled'] ? '1' : '0'),
                                    description: @json($method['description']),
                                    priority: @json($method['priority'] ?? 'high'),
                                    bank_accounts: @json($method['payment_attributes']['bank_accounts'] ?? []), init() { console.log("Tested"); },
                                    addBankRow() { this.bank_accounts.push({ account_id: "" , account_name: "" ,
                                    bank_name: "" , branch_name: "" }); }, removeBankRow(index) {
                                    this.bank_accounts.splice(index, 1); }, }'
                                    class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-4">Edit Payment Method</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm">Name</label>
                                            <input x-model="name" type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                                placeholder="Enter method name" required>
                                        </div>
                                        <div>
                                            <label class="text-sm">Type</label>
                                            <select x-model="paymentType" name="type"
                                                class="select w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                                disabled>
                                                <option value="">Select Type</option>
                                                <option value="cod">Cash on Delivery</option>
                                                <option value="direct_bank_transfer">Direct Bank Transfer</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-sm">Enabled</label>
                                            <select name="enabled" x-model="enabled"
                                                class="select w-full focus:outline-none focus:ring-0 focus:border-base-300">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-sm">Priority</label>
                                            <select x-model="priority" name="priority"
                                                class="select w-full focus:outline-none focus:ring-0 focus:border-base-300">
                                                <option value="">Select Priority</option>
                                                <option value="high">High</option>
                                                <option value="low">Low</option>
                                            </select>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Description</label>
                                            <textarea name="description" x-model="description"
                                                class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300" rows="2"
                                                placeholder="Optional description"></textarea>
                                        </div>
                                    </div>

                                    <template x-if="paymentType === 'cod'">
                                        <form
                                            action="{{ route('admin.dashboard.payment.payment-method.update-cod-method.id.post', ['id' => $method['id']]) }}"
                                            method="POST" class="mt-5 space-y-3 border-t border-base-300 pt-4"
                                            x-data="{ saving: false }" @submit.stop="saving=true">
                                            @csrf
                                            <p class="font-semibold text-sm text-center">COD Method Configuration</p>

                                            <input type="hidden" name="name" :value="name">
                                            <input type="hidden" name="enabled" :value="enabled">
                                            <input type="hidden" name="description" :value="description">
                                            <input type="hidden" name="priority" :value="priority">
                                            <div>
                                                <label class="text-sm">Delivery Instruction</label>
                                                <textarea name="cod[instruction]" class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                                    placeholder="e.g., Collect payment on delivery">{{ $method['payment_attributes']['instruction'] ?? '' }}</textarea>
                                            </div>

                                            <div class="modal-action mt-4">
                                                <button type="submit" class="btn btn-primary w-full"
                                                    :disabled="saving"> <span x-show="saving"
                                                        class="loading loading-spinner loading-sm mr-2"></span>
                                                    <span x-show="saving">Saving COD Payment</span>
                                                    <span x-show="!saving">Save COD Payment</span></button>
                                            </div>
                                        </form>
                                    </template>

                                    <template x-if="paymentType === 'direct_bank_transfer'">
                                        <form
                                            action="{{ route('admin.dashboard.payment.payment-method.update-direct-bank-method.id.post', ['id' => $method['id']]) }}"
                                            method="POST" class="mt-5 border-t border-base-300 pt-4 space-y-3"
                                            x-data="{ saving: false }" @submit.stop="saving=true">
                                            @csrf
                                            <p class="font-semibold text-sm text-center">Direct Bank Transfer Configuration
                                            </p>

                                            <input type="hidden" name="name" :value="name">
                                            <input type="hidden" name="enabled" :value="enabled">
                                            <input type="hidden" name="description" :value="description">
                                            <input type="hidden" name="priority" :value="priority">

                                            <template x-for="(bank, index) in bank_accounts" :key="index">
                                                <div class="border border-slate-200 rounded-box p-3 space-y-2 relative">
                                                    <button type="button"
                                                        class="btn btn-xs btn-circle btn-ghost absolute right-1 top-1"
                                                        @click="removeBankRow(index)">
                                                        ✕
                                                    </button>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="text-sm">Account ID</label>
                                                            <input type="text" x-model="bank.account_id"
                                                                :name="'bank_accounts[' + index + '][account_id]'"
                                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                                                placeholder="Enter account ID" required>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm">Account Name</label>
                                                            <input type="text" x-model="bank.account_name"
                                                                :name="'bank_accounts[' + index + '][account_name]'"
                                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                                                placeholder="Enter account name" required>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm">Bank Name</label>
                                                            <input type="text" x-model="bank.bank_name"
                                                                :name="'bank_accounts[' + index + '][bank_name]'"
                                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                                                placeholder="Enter bank name" required>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm">Branch Name</label>
                                                            <input type="text" x-model="bank.branch_name"
                                                                :name="'bank_accounts[' + index + '][branch_name]'"
                                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                                                placeholder="Enter branch name">
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            <div class="flex justify-end">
                                                <button type="button" class="btn btn-outline btn-sm"
                                                    @click="addBankRow()">+ Add
                                                    Bank</button>
                                            </div>

                                            <div class="modal-action mt-4">
                                                <button type="submit" class="btn btn-primary w-full"
                                                    :disabled="saving"> <span x-show="saving"
                                                        class="loading loading-spinner loading-sm mr-2"></span>
                                                    <span x-show="saving">Saving Bank Transfer Method</span>
                                                    <span x-show="!saving">Save Bank Transfer Method</span></button>
                                            </div>
                                        </form>
                                    </template>
                                </div>
                            </dialog>

                            <dialog id="delete_modal_{{ $method['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold py-0">Confirm Delete</p>
                                    <p class="py-2 mb-0 text-sm">
                                        Are you sure you want to delete
                                        <span class="italic text-error">{{ $method['name'] }}</span>?
                                    </p>
                                    <div class="modal-action mt-0">
                                        <form method="dialog">
                                            <button class="btn">Cancel</button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.payment.payment-method.id.delete', ['id' => $method['id']]) }}">
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

                <div class="flex justify-between items-center py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $paymentmethods->firstItem() }}</span> –
                        <span class="font-semibold">{{ $paymentmethods->lastItem() }}</span> of
                        <span class="font-semibold">{{ $paymentmethods->total() }}</span> results
                    </div>
                    <div class="join">
                        @if ($paymentmethods->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $paymentmethods->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif
                        @for ($i = 1; $i <= $paymentmethods->lastPage(); $i++)
                            <a href="{{ $paymentmethods->url($i) }}"
                                class="join-item btn btn-sm {{ $paymentmethods->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if ($paymentmethods->hasMorePages())
                            <a href="{{ $paymentmethods->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <dialog id="create_payment_method_modal" class="modal">
            <div x-data="{
                name: '',
                paymentType: '',
                enabled: '1',
                description: '',
                priority: 'high',
                addBankRow() {
                    this.bank_accounts.push({ account_id: '', account_name: '', bank_name: '', branch_name: '' });
                },
                removeBankRow(index) {
                    this.bank_accounts.splice(index, 1);
                },
                bank_accounts: [{ account_id: '', account_name: '', bank_name: '', branch_name: '' }]
            }" class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>

                <h3 class="text-lg font-semibold text-center mb-4">Create Payment Method</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Name</label>
                        <input x-model="name" type="text"
                            class="input w-full focus:outline-none focus:ring-0 focus:border-base-300"
                            placeholder="Enter method name" required>
                    </div>
                    <div>
                        <label class="text-sm">Type</label>
                        <select x-model="paymentType" name="type"
                            class="select w-full focus:outline-none focus:ring-0 focus:border-base-300" required>
                            <option value="">Select Type</option>
                            <option value="cod">Cash on Delivery</option>
                            <option value="direct_bank_transfer">Direct Bank Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Enabled</label>
                        <select name="enabled" x-model="enabled"
                            class="select w-full focus:outline-none focus:ring-0 focus:border-base-300">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Priority</label>
                        <select x-model="priority" name="priority"
                            class="select w-full focus:outline-none focus:ring-0 focus:border-base-300">
                            <option value="">Select Priority</option>
                            <option value="high">High</option>
                            <option value="low">Low</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm">Description</label>
                        <textarea name="description" x-model="description"
                            class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300" rows="2"
                            placeholder="Optional description"></textarea>
                    </div>
                </div>

                <template x-if="paymentType === 'cod'">
                    <form action="{{ route('admin.dashboard.payment.payment-method.create-cod-method.post') }}"
                        method="POST" class="mt-5 space-y-3 border-t border-base-300 pt-4" x-data="{ creating: false }"
                        @submit.stop="creating=true">
                        @csrf
                        <p class="font-semibold text-sm text-center">COD Method Configuration</p>

                        <input type="hidden" name="name" :value="name">
                        <input type="hidden" name="enabled" :value="enabled">
                        <input type="hidden" name="description" :value="description">
                        <input type="hidden" name="priority" :value="priority">

                        <div>
                            <label class="text-sm">Delivery Instruction</label>
                            <textarea name="cod[instruction]" class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                placeholder="e.g., Collect payment on delivery"></textarea>
                        </div>

                        <div class="modal-action mt-4">
                            <button type="submit" class="btn btn-primary w-full" :disabled="creating"> <span
                                    x-show="creating" class="loading loading-spinner loading-sm mr-2"></span>
                                <span x-show="creating">Creating COD Payment</span>
                                <span x-show="!creating">Create COD Payment</span></button>
                        </div>
                    </form>
                </template>

                <template x-if="paymentType === 'direct_bank_transfer'">
                    <form action="{{ route('admin.dashboard.payment.payment-method.create-direct-bank-method.post') }}"
                        method="POST" class="mt-5 border-t border-base-300 pt-4 space-y-3" x-data="{ creating: false }"
                        @submit.stop="creating=true">
                        @csrf
                        <p class="font-semibold text-sm text-center">Direct Bank Transfer Configuration</p>


                        <input type="hidden" name="name" :value="name">
                        <input type="hidden" name="enabled" :value="enabled">
                        <input type="hidden" name="description" :value="description">
                        <input type="hidden" name="priority" :value="priority">

                        <template x-for="(bank, index) in bank_accounts" :key="index">
                            <div class="border border-base-200 rounded-box p-3 space-y-2 relative">
                                <button type="button" class="btn btn-xs btn-circle btn-ghost absolute right-1 top-1"
                                    @click="removeBankRow(index)">
                                    ✕
                                </button>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-sm">Account ID</label>
                                        <input type="text" x-model="bank.account_id"
                                            :name="'bank_accounts[' + index + '][account_id]'"
                                            class="input w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                            placeholder="Enter account ID" required>
                                    </div>
                                    <div>
                                        <label class="text-sm">Account Name</label>
                                        <input type="text" x-model="bank.account_name"
                                            :name="'bank_accounts[' + index + '][account_name]'"
                                            class="input w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                            placeholder="Enter account name" required>
                                    </div>
                                    <div>
                                        <label class="text-sm">Bank Name</label>
                                        <input type="text" x-model="bank.bank_name"
                                            :name="'bank_accounts[' + index + '][bank_name]'"
                                            class="input w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                            placeholder="Enter bank name" required>
                                    </div>
                                    <div>
                                        <label class="text-sm">Branch Name</label>
                                        <input type="text" x-model="bank.branch_name"
                                            :name="'bank_accounts[' + index + '][branch_name]'"
                                            class="input w-full focus:outline-none focus:ring-0 focus:border-base-300"
                                            placeholder="Enter branch name">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="flex justify-end">
                            <button type="button" class="btn btn-outline btn-sm" @click="addBankRow()">+ Add
                                Bank</button>
                        </div>

                        <div class="modal-action mt-4">
                            <button type="submit" class="btn btn-primary w-full" :disabled="creating"> <span
                                    x-show="creating" class="loading loading-spinner loading-sm mr-2"></span>
                                <span x-show="creating">Creating Bank Transfer Method</span>
                                <span x-show="!creating">Create Bank Transfer Method</span></button>
                        </div>
                    </form>
                </template>
            </div>
        </dialog>

    </div>
@endsection
