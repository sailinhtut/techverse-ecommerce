@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-3">
        <div class="mb-3">
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
                        <a href="{{ route('admin.dashboard.store.article.get') }}" class="btn btn-xs btn-ghost">
                            Articles
                        </a>
                    </li>

                    <li>
                        @isset($edit_article)
                            <a href="{{ route('admin.dashboard.store.article.edit.id.get', $edit_article['id']) }}"
                                class="btn btn-xs btn-ghost">
                                {{ $edit_article['title'] ?? 'Edit Article' }}
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard.store.article.create.get') }}" class="btn btn-xs btn-ghost">
                                Add Article
                            </a>
                        @endisset
                    </li>
                </ul>
            </div>
        </div>

        <p class="font-semibold lg:text-lg mb-3 mt-5"> {{ isset($edit_article) ? 'Edit Article' : 'Add Article' }}</p>

        @if ($errors->any())
            <div class="alert alert-error max-w-[300px] p-2 my-2">
                <ul class="mb-0 p-0 text-xs text-justify">
                    <li class="font-bold mb-1">Errors</li>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ isset($edit_article) ? route('admin.dashboard.store.article.id.post', ['id' => $edit_article['id']]) : route('admin.dashboard.store.article.post') }}"
            method="POST" class="w-full flex flex-col gap-3" enctype="multipart/form-data" x-data="{ submitting: false }"
            @submit="submitting=true">
            @csrf

            <div class="tabs tabs-box bg-base-100 shadow-none">
                <input type="radio" name="active_tab" class="tab" aria-label="General" checked="checked" />
                <div class="tab-content">
                    <div class="w-full flex flex-col items-start gap-3 pt-3">
                        <div class="w-full flex flex-col items-start gap-3 rounded-box border border-base-300 p-3">
                            <label for="image" class="text-sm">Header Image</label>
                            @if (!empty($edit_article['image']))
                                <div class="flex items-center gap-2">
                                    <img src="{{ $edit_article['image'] }}" class="size-12 object-contain border rounded">
                                    <label class="flex items-center gap-1 text-xs">
                                        <input type="checkbox" name="remove_image" value="1"
                                            class="checkbox checkbox-xs">
                                        Remove this image
                                    </label>
                                </div>
                            @endif
                            <input type="file" name="image" id="image" class="file-input file-input-sm ">
                        </div>

                        <div
                            class="w-full p-3 border border-base-300 rounded-box grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div class="w-full flex flex-col gap-2 md:col-span-2 lg:col-span-3">
                                <label for="title" class="text-sm">Article Title</label>
                                <input type="text" name="title" id="title" class="input w-full"
                                    value="{{ old('title', $edit_article['title'] ?? '') }}" required>
                            </div>

                            <div class="w-full flex flex-col gap-2  md:col-span-2 lg:col-span-3">
                                <label class="text-sm">Tags (Optional, Comma Separated)</label>
                                <input type="text" name="tags" class="input w-full"
                                    value="{{ old('tags', implode(',', $edit_article['tags'] ?? [])) }}" required>
                            </div>

                            <div class="w-full flex flex-col gap-2 md:col-span-2 lg:col-span-3">
                                <label for="description" class="text-sm">Short
                                    Description (Optional)</label>
                                <textarea name="description" id="description" class="textarea w-full" rows="4">{{ old('description', $edit_article['description'] ?? '') }}</textarea>
                            </div>

                            <div class="w-full flex flex-col gap-2 md:col-span-2 lg:col-span-3">
                                <label for="description" class="text-sm">Status</label>
                                <select name="status" class="select w-full">
                                    <option value="draft"
                                        {{ isset($edit_article) && $edit_article['status'] === 'draft' ? 'selected' : '' }}>
                                        Draft
                                    </option>
                                    <option value="published"
                                        {{ isset($edit_article) && $edit_article['status'] === 'published' ? 'selected' : '' }}
                                        selected>
                                        Published
                                    </option>
                                    <option value="archived"
                                        {{ isset($edit_article) && $edit_article['status'] === 'archived' ? 'selected' : '' }}>
                                        Archived
                                    </option>
                                </select>
                            </div>

                            <div class="w-full flex flex-col gap-2 md:col-span-2 lg:col-span-3">
                                <label for="description" class="text-sm">Is Featured</label>
                                <select name="is_featured" class="select w-full">
                                    <option value="0"
                                        {{ isset($edit_article) && $edit_article['is_featured'] ? 'selected' : '' }}>
                                        Normal
                                    </option>
                                    <option value="1"
                                        {{ isset($edit_article) && $edit_article['is_featured'] ? 'selected' : '' }}>
                                        Featured
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="p-3 rounded-box border border-base-300 w-full flex flex-col gap-2">
                            <label class="text-sm flex items-center gap-2">Article Content</label>
                            <div x-data="quillSetUpData()">
                                <div x-ref="editor" class=""></div>
                                <textarea class="hidden" name="content" x-ref="input">
                                    {{ old('content', $edit_article['content'] ?? '') }}
                                </textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="mt-10 btn btn-primary w-fit" :disabled="submitting">
                <span x-show="submitting" class="loading loading-spinner loading-sm mr-2"></span>
                <span x-show="submitting">{{ isset($edit_article) ? 'Saving Article' : 'Adding Article' }}</span>
                <span x-show="!submitting">
                    {{ isset($edit_article) ? 'Update Article' : 'Add Article' }}
                </span>
            </button>
        </form>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/gh/scrapooo/quill-resize-module@1.0.2/dist/quill-resize-module.js"></script>
    <script>
        const sitePrimaryColor = "{{ $settings['site_primary_color'] ?? config('app.primary_color') }}";

        const sitePrimaryContentColor =
            "{{ $settings['site_primary_content_color'] ?? config('app.site_primary_content_color') }}";

        const colorPalette = [
            // Site branding colors first
            sitePrimaryColor,
            sitePrimaryContentColor,

            // GREEN
            'HoneyDew', 'PaleGreen', 'LightGreen', 'Lime', 'LimeGreen', 'MediumSeaGreen', 'SeaGreen', 'ForestGreen',
            'Green', 'DarkGreen', 'Olive', 'OliveDrab', 'YellowGreen',

            // RED / PINK
            'MistyRose', 'LightCoral', 'Salmon', 'DarkSalmon', 'Red', 'FireBrick', 'Crimson', 'Pink', 'HotPink',
            'DeepPink', 'Fuchsia', 'Magenta',

            // BLUE / CYAN
            'LightCyan', 'PaleTurquoise', 'Cyan', 'Aqua', 'Aquamarine', 'Turquoise', 'MediumTurquoise', 'DarkTurquoise',
            'LightBlue', 'SkyBlue', 'DeepSkyBlue', 'DodgerBlue', 'CornflowerBlue', 'RoyalBlue', 'Blue', 'MediumBlue',
            'DarkBlue', 'Navy', 'MidnightBlue',

            // PURPLE / VIOLET
            'Lavender', 'Thistle', 'Plum', 'Orchid', 'Violet', 'MediumOrchid', 'DarkOrchid', 'Purple', 'RebeccaPurple',
            'DarkViolet', 'MediumPurple', 'SlateBlue', 'MediumSlateBlue',

            // YELLOW / ORANGE / BROWN
            'LightYellow', 'LemonChiffon', 'LightGoldenRodYellow', 'PapayaWhip', 'Moccasin', 'PeachPuff',
            'PaleGoldenRod', 'Khaki', 'DarkKhaki', 'Yellow', 'Gold', 'GoldenRod', 'Orange', 'DarkOrange', 'OrangeRed',
            'Peru', 'Chocolate', 'SandyBrown', 'BurlyWood', 'Tan', 'RosyBrown', 'Salmon', 'Sienna',

            // NEUTRALS / BLACK / WHITE / GRAY
            'Snow', 'White', 'WhiteSmoke', 'Gainsboro', 'LightGray', 'Silver', 'DarkGray', 'Gray', 'DimGray', 'Black'
        ];

        document.addEventListener("alpine:init", () => {
            // Register with Quill v2
            Quill.register("modules/resize", window.QuillResizeModule);
        });

        function quillSetUpData() {
            return {
                init() {
                    const toolbar = [
                        [{
                            'font': []
                        }],
                        [{
                            'size': ['small', false, 'large', 'huge']
                        }],
                        ['bold', 'italic', 'underline', 'strike', 'code'],
                        [{
                            'color': colorPalette
                        }, {
                            'background': colorPalette
                        }],
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }],
                        [{
                            'header': 1
                        }, {
                            'header': 2
                        }, 'blockquote', 'code-block'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }, {
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        ['direction', {
                            'align': []
                        }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ];

                    let initial = (this.$refs.input.value || '').trim();

                    let editor = new Quill(this.$refs.editor, {
                        theme: 'snow',
                        placeholder: 'Write something...',
                        modules: {
                            toolbar: {
                                container: toolbar,
                                handlers: {
                                    image: function() {
                                        const input = document.createElement('input');
                                        input.setAttribute('type', 'file');
                                        input.setAttribute('accept', 'image/*');
                                        input.click();

                                        input.onchange = () => {
                                            const file = input.files[0];
                                            if (file) {
                                                if (file.size > 200 * 1024) {
                                                    alert(
                                                        "Image is too large. Maximum allowed size is 200 KB."
                                                    );
                                                    return;
                                                }

                                                const reader = new FileReader();
                                                reader.onload = (e) => {
                                                    const range = editor.getSelection(true);
                                                    editor.insertEmbed(range.index, 'image', e.target
                                                        .result, 'user');
                                                    editor.setSelection(range.index + 1, 0);
                                                };
                                                reader.readAsDataURL(file);
                                            }
                                        };
                                    }

                                }
                            },
                            resize: {
                                parchment: Quill.import('parchment'),
                                displayStyles: {
                                    backgroundColor: 'black',
                                    border: 'none',
                                    color: 'white'
                                }
                            }
                        },
                        formats: [
                            'size', 'bold', 'italic', 'underline', 'color', 'list', 'link', 'image',
                            'video', 'background', 'script', 'header', 'blockquote', 'code-block', 'code',
                            'direction',
                            'align', 'font', 'strike', 'indent'
                        ]
                    });

                    // Set initial content
                    editor.root.innerHTML = initial;

                    if (initial.length === 0) {
                        editor.setSelection(0, 0);
                    }

                    editor.on('text-change', () => {
                        this.$refs.input.value = editor.root.innerHTML.trim();
                    });
                }
            }
        }
    </script>
@endpush
