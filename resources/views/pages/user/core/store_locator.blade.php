@extends('layouts.app')

@section('app_content')
    @include('components.landing_navbar')

    <div class="p-4 lg:p-8 mt-[60px] max-w-7xl mx-auto">
        <div x-data="storeLocatorState()" class="flex flex-col lg:flex-row gap-6">
            <div class="lg:w-2/3 space-y-3 h-fit lg:sticky lg:top-[100px]">
                <p class="text-2xl font-semibold lg:mb-6">Store Locator</p>

                <div class="w-full h-80 bg-gray-100 rounded-lg overflow-hidden z-0 border border-base-300">
                    <div id="map" class="w-full h-full rounded-lg z-0"></div>
                </div>

                <div x-show="selectedBranch" x-cloak
                    class="sticky top-[100px] p-4 bg-white border border-base-300 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        <!-- Branch Name -->
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-semibold">Branch Name</label>
                            <input type="text" x-model="selectedBranch.name" readonly
                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none">
                        </div>

                        <!-- City, State, Country -->
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-semibold">Location</label>
                            <input type="text"
                                :value="`${selectedBranch.city}, ${selectedBranch.state}, ${selectedBranch.country} ${selectedBranch.postal_code}`"
                                readonly
                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none">
                        </div>

                        <!-- Phone -->
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-semibold">Phone</label>
                            <input type="text" x-model="selectedBranch.phone" readonly
                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none">
                        </div>

                        <!-- Email -->
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-semibold">Email</label>
                            <input type="text" x-model="selectedBranch.email" readonly
                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none">
                        </div>

                        <!-- Open / Close Time -->
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-semibold flex items-center gap-2">
                                Hours
                                <span class="relative flex h-2 w-2">
                                    <!-- Green ping if open -->
                                    <template x-if="isOpenNow(selectedBranch.open_time, selectedBranch.close_time)">
                                        <div class="flex flex-col items-center justify-center">
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                        </div>
                                    </template>

                                    <!-- Red ping if closed -->
                                    <template x-if="!isOpenNow(selectedBranch.open_time, selectedBranch.close_time)">
                                        <div class="flex flex-col items-center justify-center">
                                            <span
                                                class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span
                                                class="relative inline-flex rounded-full h-2 w-2 animate-pulse bg-red-500"></span>
                                        </div>
                                    </template>
                                </span>
                            </label>

                            <input type="text"
                                :value="`${selectedBranch.open_time} - ${selectedBranch.close_time} ${isOpenNow(selectedBranch.open_time, selectedBranch.close_time) ? '(Open Now)' : '(Closed)'}`"
                                readonly
                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none">
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2 flex flex-col gap-1">
                            <label class="text-sm font-semibold">Address</label>
                            <textarea x-model="selectedBranch.address" readonly
                                class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"></textarea>
                        </div>

                        <!-- Description (full width) -->
                        <div class="md:col-span-2 flex flex-col gap-1">
                            <label class="text-sm font-semibold">Description</label>
                            <textarea x-model="selectedBranch.description" readonly
                                class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"></textarea>
                        </div>

                    </div>
                </div>
            </div>

            <div class="lg:w-1/3 space-y-3 lg:pt-[65px] lg:pb-[200px]">
                <template x-for="branch in branches" :key="branch.id">
                    <div @click="selectBranch(branch)"
                        :class="selectedBranch && selectedBranch.id === branch.id ? 'bg-primary text-white' :
                            'border border-base-300 bg-slate-100 hover:border-primary'"
                        class="cursor-pointer rounded-lg p-4 transition flex flex-col gap-1">
                        <p class="font-semibold text-sm" x-text="branch.name"></p>
                        <p class="text-xs" x-text="`${branch.city}, ${branch.state}`"></p>
                        <p class="text-xs" x-text="branch.phone"></p>
                        <p class="text-xs" x-text="branch.email"></p>
                    </div>
                </template>

                <div x-show="loading" class="flex justify-center py-4">
                    <span class="loading loading-spinner loading-md text-primary"></span>
                </div>

                <div x-show="pagination && pagination.next_page_url && !loading" class="flex justify-center mt-2" x-cloak>
                    <button class="btn btn-sm btn-ghost" @click="loadMore()">Load More</button>
                </div>
            </div>
        </div>
    </div>

    @include('components.web_footer')
@endsection


@push('script')
    <script>
        function storeLocatorState() {
            return {
                branches: [],
                selectedBranch: null,
                pagination: null,
                loading: false,
                markers: [], // store marker references

                init() {
                    this.loadBranches();
                },

                isOpenNow(openTime, closeTime) {
                    if (!openTime || !closeTime) return false;

                    const now = new Date();
                    const [openH, openM] = openTime.split(':').map(Number);
                    const [closeH, closeM] = closeTime.split(':').map(Number);

                    const openDate = new Date();
                    openDate.setHours(openH, openM, 0);

                    const closeDate = new Date();
                    closeDate.setHours(closeH, closeM, 0);

                    if (closeDate <= openDate) closeDate.setDate(closeDate.getDate() + 1);

                    return now >= openDate && now <= closeDate;
                },

                async loadBranches(page = 1, append = false) {
                    if (this.loading) return;
                    this.loading = true;

                    try {
                        const res = await axios.get('/store-locator', {
                            params: {
                                page
                            }
                        });

                        if (append) {
                            this.branches.push(...res.data.data);
                        } else {
                            this.branches = res.data.data;
                        }

                        this.pagination = res.data;

                        // Update map markers
                        this.updateMarkers();

                    } catch (err) {
                        console.error(err);
                    } finally {
                        this.loading = false;
                    }
                },

                loadMore() {
                    if (this.pagination && this.pagination.next_page_url) {
                        this.loadBranches(this.pagination.current_page + 1, true);
                    }
                },

                selectBranch(branch) {
                    this.selectedBranch = branch;
                    this.focusMap(branch.latitude, branch.longitude);

                    // Open the popup for the clicked branch
                    const marker = this.markers.find(m => {
                        const latLng = m.getLatLng();
                        const tolerance = 0.00001; // small number to handle float precision
                        return Math.abs(latLng.lat - branch.latitude) < tolerance &&
                            Math.abs(latLng.lng - branch.longitude) < tolerance;
                    });

                    if (marker) {
                        marker.openPopup();
                    }

                    this.$nextTick(() => {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth' // optional for smooth scroll
                        });
                    });
                },

                focusMap(lat, lng) {
                    if (window.mapInstance) {
                        window.mapInstance.setView([lat, lng], 15);
                    }
                },

                updateMarkers() {
                    if (!window.mapInstance) return;

                    // Remove existing markers
                    this.markers.forEach(marker => window.mapInstance.removeLayer(marker));
                    this.markers = [];

                    // Add new markers for all branches
                    this.branches.forEach(branch => {
                        if (branch.latitude && branch.longitude) {
                            const marker = L.marker([branch.latitude, branch.longitude])
                                .addTo(window.mapInstance)
                                .bindPopup(
                                    `<b>${branch.name}</b><br>${branch.city}, ${branch.state},<br><a href='tel:${branch.phone}'>${branch.phone}</a>`
                                );
                            this.markers.push(marker);
                        }
                    });

                    // Optional: fit map bounds to all markers
                    const group = new L.featureGroup(this.markers);
                    if (this.markers.length) {
                        window.mapInstance.fitBounds(group.getBounds().pad(0.2));
                    }
                }
            }
        }


        // Initialize Leaflet map
        document.addEventListener('alpine:init', () => {
            document.addEventListener('DOMContentLoaded', () => {
                window.mapInstance = L.map('map', {
                    dragging: false, // disable drag
                    scrollWheelZoom: false, // optional, keep zoom enabled
                    doubleClickZoom: false, // optional
                    boxZoom: false, // optional
                    keyboard: false,
                }).setView([0, 0], 2);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(window.mapInstance);
            });
        });
    </script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endpush
