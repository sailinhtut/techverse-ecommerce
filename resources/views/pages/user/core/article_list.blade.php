@extends('layouts.app')

@section('app_content')
    @php
        $site_name = getParsedTemplate('site_name');
        $site_logo = getSiteLogoURL();
    @endphp
    @include('components.landing_navbar')
    <div class="p-3 lg:p-7 mt-[60px] mx-auto min-h-screen">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-3 lg:mt-6">
                <h1 class="text-xl md:text-2xl font-semibold text-gray-800 mb-4">Articles</h1>
            </div>

            <form action="{{ route('articles.search.get') }}" class="max-w-[300px] flex join-horizontal mb-5">
                <input type="text" name="q" id="q" class="input input-sm join-item"
                    placeholder="Search Something...">
                <button class="btn btn-sm join-item">
                    Search <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </button>
            </form>


            @if (!empty($articles))
                <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($articles as $article)
                        <a href="{{ route('articles.slug.get', ['slug' => $article['slug']]) }}"
                            class="card bg-base-100 border border-base-300 rounded-none! shadow-sm p-4 flex flex-col cursor-pointer group transition-all! duration-100 hover:-translate-y-1.5">

                            <img src="{{ $article['image'] ?? $site_logo }}"
                                class="w-full h-48 object-cover rounded mb-4 group-hover:scale-105 group-hover:rounded-none transition-all duration-300"
                                alt="{{ $article['title'] }}">

                            <h2 class="font-semibold text-normal">{{ $article['title'] }}</h2>
                            <p class="text-sm text-base-content/70 mb-2 wrap-break-word">
                                {{ substr($article['description'], 0, 100) }}
                                ...
                            </p>
                        </a>
                    @endforeach
                </div>



                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $articles->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $articles->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $articles->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($articles->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $articles->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $articles->url(1) }}"
                            class="join-item btn btn-sm {{ $articles->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $articles->currentPage() - 1);
                            $end = min($articles->lastPage() - 1, $articles->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $articles->url($i) }}"
                                class="join-item btn btn-sm {{ $articles->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($articles->lastPage() > 1)
                            <a href="{{ $articles->url($articles->lastPage()) }}"
                                class="join-item btn btn-sm {{ $articles->currentPage() === $articles->lastPage() ? 'btn-active' : '' }}">
                                {{ $articles->lastPage() }}
                            </a>
                        @endif

                        @if ($articles->hasMorePages())
                            <a href="{{ $articles->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-center text-base-content/70 mt-6">No article found. </p>
            @endif

        </div>
    </div>
    @include('components.web_footer')
@endsection
