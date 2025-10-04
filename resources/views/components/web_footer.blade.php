<footer class="bg-base-200 border-t border-base-300">
    <div class="pt-10 pb-10 px-6 lg:px-20 md:flex md:justify-between md:items-start gap-10">

        <div class="mb-6 md:mb-0 md:w-1/3">
            <p class="text-xl font-semibold mb-3">{{ config('app.name') }}</p>
            <p class="text-sm">
                Your one-stop shop for computers, peripherals, and tech accessories.
                We provide high-quality products with excellent customer support.
            </p>
        </div>

        <!-- Quick Links -->
        <div class="mb-6 md:mb-0 md:w-1/3">
            <p class=" text-lg font-semibold mb-3">Quick Links</p>
            <ul class="space-y-2 p-0">
                <li><a href="{{ url('/') }}"
                        class="hover:text-primary hover:underline hover:underline-offset-5 transition-colors">Home</a>
                </li>
                <li><a href="{{ route('shop.get') }}"
                        class="hover:text-primary hover:underline hover:underline-offset-5 transition-colors">Shop</a>
                </li>
                <li><a href="/about"
                        class="hover:text-primary hover:underline hover:underline-offset-5 transition-colors">About
                        Us</a></li>
                <li><a href="/contact"
                        class="hover:text-primary hover:underline hover:underline-offset-5 transition-colors">Contact</a>
                </li>
                <li><a href="/privacy"
                        class="hover:text-primary hover:underline hover:underline-offset-5 transition-colors">Privacy
                        Policy</a></li>
                <li><a href="/terms"
                        class="hover:text-primary hover:underline hover:underline-offset-5 transition-colors">Terms &
                        Conditions</a></li>
            </ul>
        </div>

        <!-- Contact Info -->
        <div class="md:w-1/3">
            <p class="text-lg font-semibold mb-3">Contact Us</p>
            <ul class="space-y-2  text-sm p-0">
                <li>
                    <div class="tooltip" data-tip="View On Map">
                        <a target="_blank" href="https://www.google.com/maps" class="hover:text-primary"><i
                                class="bi bi-geo-alt-fill"></i> 123
                            Tech Street,
                            Yangon, Myanmar</a>
                    </div>
                </li>
                <li>
                    <div class="tooltip" data-tip="Call Now">
                        <a target="_blank" href="tel:+959252203838" class="hover:text-primary">
                            <i class="bi bi-telephone-fill"></i>
                            +95 (252) 203-838
                        </a>
                    </div>
                </li>
                <li>
                    <div class="tooltip" data-tip="Send Mail Now">
                        <a target="_blank" href="mailto:support@techverse.com" class="hover:text-primary"><i
                                class="bi bi-envelope-at-fill"></i>
                            support@techverse.com</a>
                    </div>
                </li>
            </ul>

            <div class="flex gap-3 mt-4">
                <a href="#" class=" hover:text-[#1877F2] transition-colors">
                    <i class="bi bi-facebook text-lg"></i>
                </a>
                <a href="#" class=" hover:text-[#1DA1F2] transition-colors">
                    <i class="bi bi-twitter text-lg"></i>
                </a>
                <a href="#" class=" hover:text-[#E4405F] transition-colors">
                    <i class="bi bi-instagram text-lg"></i>
                </a>
                <a href="#" class=" hover:text-[#FF0000] transition-colors">
                    <i class="bi bi-youtube text-lg"></i>
                </a>
            </div>

        </div>
    </div>

    <div class="border-t border-base-300 "></div>

    <div class="py-2 text-center text-sm px-2">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</footer>

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
                tooltipTriggerEl))
        })
    </script>
@endpush
