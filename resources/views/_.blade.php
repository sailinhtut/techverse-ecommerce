@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')

    <div class="p-5" x-data="productVariantForm()">
        <p class="mb-4 text-lg font-semibold">Testing Product Variant Form</p>

        <form action="/debug" method="POST" enctype="multipart/form-data" class="max-w-md space-y-4">
            @csrf
            <template x-for="(variant, index) in variants" :key="index">
                <div class="border border-base-300 p-4 rounded-lg flex flex-col gap-2 bg-base-100">
                    <div class="flex justify-between items-center">
                        <p class="font-medium" x-text="`Product Variant ${index + 1}`"></p>
                        <button type="button" class="btn btn-square btn-sm btn-outline btn-error"
                            @click="document.getElementById('delete_modal_' + variant.id).showModal()"><svg
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>


                    </div>

                    <dialog :id="`delete_modal_${variant.id}`" class="modal">
                        <div class="modal-box">
                            <form method="dialog">
                                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                            </form>
                            <p class="text-lg font-semibold py-0">Confirm Delete</p>

                            <p class="py-2 mb-0 text-sm">
                                Are you sure you want to delete
                                <span class="italic text-error" x-text="`Product Variant ${variant.sku}`"></span> ?
                            </p>
                            <div class="modal-action mt-0">
                                <form method="dialog">
                                    <button class="btn lg:btn-md">Close</button>
                                </form>
                                <form method="POST" :action="`/variant/delete/${variant.id}`">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn lg:btn-md btn-error">Delete</button>
                                </form>
                            </div>
                        </div>
                    </dialog>


                    <div x-show="variant.image" class="flex items-center gap-2 mb-2">
                        <img :src="variant.image" class="size-12 object-contain border border-base-300 rounded">
                        <label class="flex items-center gap-1 text-xs">
                            <input type="checkbox" :name="`product_variants[${index}][remove_image]`" value="1"
                                class="checkbox checkbox-xs">   
                            Remove this image
                        </label>
                    </div>

                    <!-- SKU -->
                    <label class="text-sm">SKU</label>
                    <input type="text"
                        :class="`input w-full ${variant.id !== null ? 'focus:outline-none focus:ring-0 focus:border-base-300 cursor-default bg-base-200' : ''}`"
                        placeholder="SKU" :name="`product_variants[${index}][sku]`" x-model="variant.sku" :readonly="variant.id !== null" required>

                    <!-- Regular Price -->
                    <label class="text-sm">Regular Price</label>
                    <input type="number" step="0.01" class="input input-bordered w-full" placeholder="Regular Price"
                        :name="`product_variants[${index}][regular_price]`" x-model="variant.regular_price" required>

                    <!-- Sale Price -->
                    <label class="text-sm">Sale Price</label>
                    <input type="number" step="0.01" class="input input-bordered w-full" placeholder="Sale Price"
                        :name="`product_variants[${index}][sale_price]`" x-model="variant.sale_price">

                    <!-- Stock -->

                    <label class="text-sm">Stock</label>
                    <input type="number" class="input input-bordered w-full" placeholder="Stock"
                        :name="`product_variants[${index}][stock]`" x-model="variant.stock" required>

                    <!-- Weight -->
                    <label class="text-sm">Weight</label>
                    <input type="number" step="0.01" class="input input-bordered w-full" placeholder="Weight"
                        :name="`product_variants[${index}][weight]`" x-model="variant.weight">

                    <!-- Image -->
                    <label class="text-sm">Image</label>
                    <input type="file" class="file-input file-input-bordered w-full"
                        :name="`product_variants[${index}][image]`">

                    <!-- Combination (nested adder) -->
                    <div class="pt-3">
                        <p class="font-semibold mb-2">Combinations</p>

                        <template x-for="(pair, cIndex) in variant.combination" :key="cIndex">
                            <div class="flex gap-2 items-center mb-2">
                                <input type="text" class="input input-bordered w-1/2" x-model="pair.key"
                                    placeholder="Key (e.g. color)">
                                <input type="text" class="input input-bordered w-1/2"
                                    :name="`product_variants[${index}][combination][${pair.key}]`" x-model="pair.value"
                                    placeholder="Value (e.g. Red)">
                                <button type="button" class="btn btn-square btn-sm btn-outline btn-error"
                                    @click="removeCombination(index, cIndex)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <button type="button" class="btn btn-xs btn-secondary" @click="addCombination(index)">+ Add
                            Combination</button>
                    </div>
                </div>
            </template>

            <!-- Add Variant Button -->
            <button class="btn btn-secondary" type="button" @click="addVariant()">+ Add Variant Group</button>

            <!-- Submit -->
            <button class="btn btn-primary" type="submit">Submit</button>
        </form>


    </div>
@endsection

@push('script')
    <script>
        function productVariantForm() {
            let existingVariants = @json($product['product_variants'] ?? []);

            existingVariants = existingVariants.map(variant => {
                return {
                    id: variant.id ?? null,
                    sku: variant.sku || '',
                    regular_price: variant.regular_price || 0,
                    sale_price: variant.sale_price || 0,
                    stock: variant.stock || 0,
                    weight: variant.weight || 0,
                    image: variant.image || null,
                    combination: variant.combination ? Object.entries(variant.combination).map(([key, value]) => ({
                        key,
                        value
                    })) : []
                };
            });

            return {
                variants: existingVariants,

                addVariant() {
                    this.variants.push({
                        id: null,
                        sku: '',
                        regular_price: '',
                        sale_price: '',
                        stock: '',
                        weight: '',
                        combination: []
                    });
                },

                removeVariant(index) {
                    this.variants.splice(index, 1);
                },

                addCombination(vIndex) {
                    this.variants[vIndex].combination.push({
                        key: '',
                        value: ''
                    });
                },

                removeCombination(vIndex, cIndex) {
                    this.variants[vIndex].combination.splice(cIndex, 1);
                },
            }
        }
    </script>
@endpush
