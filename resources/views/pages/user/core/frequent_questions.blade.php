@extends('layouts.app')

@section('app_content')
    @include('components.landing_navbar')

    <div class="mt-[60px] px-4 lg:px-8 max-w-4xl mx-auto">
        <div x-data="faqState()" class="py-8 space-y-6">

            <!-- Header -->
            <div class="text-center space-y-2">
                <h1 class="text-xl lg:text-xl font-semibold">Frequently Asked Questions</h1>
                <p class="text-sm text-gray-500">
                    Find quick answers to common questions
                </p>
            </div>

            <!-- Search Bar -->
            <div class="relative">
                <input type="text" x-model="search" placeholder="Search questions..."
                    class="input input-bordered w-full pl-10 focus:outline-none focus:ring-0" />
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35m1.85-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- FAQ Accordion -->
            <div class="space-y-3">
                <template x-for="faq in filteredFaqs" :key="faq.id">
                    <div class="collapse collapse-arrow bg-base-100 border border-base-300">
                        <input type="checkbox" />
                        <div class="collapse-title text-sm lg:text-base font-medium">
                            <span x-text="faq.question"></span>
                        </div>
                        <div class="collapse-content text-sm text-gray-600 leading-relaxed">
                            <p x-html="faq.answer"></p>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div x-show="filteredFaqs.length === 0" x-cloak class="text-center text-sm text-gray-500 py-8">
                    No matching questions found.
                </div>
            </div>

        </div>
    </div>

    @include('components.web_footer')
@endsection

@push('script')
    <script>
        function faqState() {
            return {
                search: '',
                faqs: @json($faqs->map(fn($f) => $f->jsonResponse())),

                get filteredFaqs() {
                    if (!this.search) return this.faqs;

                    const q = this.search.toLowerCase();

                    return this.faqs.filter(faq =>
                        faq.question.toLowerCase().includes(q) ||
                        faq.answer.toLowerCase().includes(q)
                    );
                }
            }
        }
    </script>
@endpush
