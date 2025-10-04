{{-- resources/views/cart/index.blade.php --}}
@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')
    <div class="max-w-screen p-6 overflow-x-hidden" x-data>
        <p class="lg:text-lg font-semibold mb-3">Your Cart</p>
        <div class="card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead class="">
                        <tr class="">
                            <th class="w-[50px]">No.</th>
                            <th class="w-[50px]">Image</th>
                            <th class="w-[200px]">Title</th>
                            <th class="">Price</th>
                            <th class="">Quantity</th>
                            <th class="">Total</th>
                            <th class=""></th>
                        </tr>
                    </thead>

                    <tbody>
                        <template x-for="(item, index) in Object.values($store.cart.items)" :key="item.id">
                            <tr>
                                <td x-text="index+1"></td>
                                <td class="">
                                    <img :src="item.image ? item.image :
                                        '{{ asset('assets/images/techverse_green_logo.png') }}'"
                                        :alt="item.title" class="w-[20px] h-auto object-contain">
                                </td>


                                <td class="w-[200px] h-[30px] line-clamp-1">
                                    <a :href="`/shop/${item.slug}`" class="cursor-default hover:underline"
                                        x-text="item.title"></a>
                                </td>
                                <td x-text="item.price.toFixed(2)"></td>

                                <td class="flex items-center justify-start gap-3">
                                    <button class="btn btn-sm btn-square" @click="$store.cart.addItem(item)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                    <div x-text="item.quantity"></div>
                                    <button class="btn btn-sm btn-square" @click="$store.cart.removeItem(item.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                        </svg>

                                    </button>
                                </td>
                                <td x-text="(item.price * item.quantity).toFixed(2)"></td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">

                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                            </svg>
                                            </i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button class=""
                                                    @click="$store.cart.deleteItem(item.id)">Remove</button>
                                            </li>
                                        </ul>
                                    </div>

                                </td>
                            </tr>
                        </template>
                        <tr x-show="Object.keys($store.cart.items).length === 0">
                            <td colspan="7">Your cart is empty</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        {{-- <div class="w-full md:w-[500px] mt-3 ml-auto">
            <p class="font-semibold mb-0">Cart Summary</p>

            <div class="table w-full">
                <div class="table-row">
                    <div class="table-cell py-2">Total Items</div>
                    <div class="table-cell text-right font-medium">{{ getCartTotalItems() }}</div>
                </div>
                <div class="table-row">
                    <div class="table-cell py-2">Total Cost</div>
                    <div class="table-cell text-right font-medium">${{ getCartTotalCost() }}</div>
                </div>
            </div>

        </div> --}}

        <div class="w-full md:w-[500px] mt-3 ml-auto">
            <p class="font-semibold mb-0">Cart Summary</p>

            <div class="table w-full">
                <div class="table-row">
                    <div class="table-cell py-2">Total Items</div>
                    <div class="table-cell text-right font-medium" x-text="$store.cart.totalItems()"></div>
                </div>
                <div class="table-row">
                    <div class="table-cell py-2">Total Cost</div>
                    <div class="table-cell text-right font-medium">
                        $<span x-text="$store.cart.totalCost().toFixed(2)"></span>
                    </div>
                </div>
            </div>
        </div>


        <div class="mt-3 flex gap-2 justify-between">
            <div class="flex gap-2 items-center">
                <button class="btn  lg:btn-md" @click.prevent="$store.cart.clearCart()">
                    Clear
                </button>

                <a href="{{ route('shop.get') }}" class="btn  lg:btn-md">Back</a>
            </div>

            <form action="{{ route('shop.get') }}" @submit.prevent="handleCheckout()">
                <button class="btn  lg:btn-md btn-primary" :disabled="$store.cart.totalItems() === 0">
                    Proceed Checkout
                </button>
            </form>
        </div>

    </div>
@endsection

@push('script')
    <script>
        function handleCheckout(e) {
            const cartEmpty = {{ session('cart_items', []) ? 'false' : 'true' }};
            if (cartEmpty) {
                e.preventDefault();
                Toast.show("Your cart is empty!");
                return false;
            }
            return true;
        }
    </script>
@endpush
