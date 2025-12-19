@extends('layouts.admin.admin_dashboard')


@section('admin_dashboard_content')
    <div class="p-3 lg:p-5">
        <p class="lg:text-lg font-semibold">Setting</p>
        <div class="mt-2 tabs tabs-lift bg-base-100 shadow-none">
            <input type="radio" name="active_tab" class="tab pl-0" aria-label="Profile Setting" checked="checked" />
            <div class="tab-content border-base-300 bg-base-100">
                <div class="w-full flex flex-col max-w-sm p-3 gap-1.5">
                    {{-- <p class="font-semibold">Profile</p> --}}
                    <img src="{{ auth()->user()->jsonResponse()['profile'] ?? asset('assets/images/blank_profile.png') }}"
                        class="size-28 rounded-full border border-slate-300 object-cover object-center" />
                    <div class="flex items-center justify-between text-sm">
                        <p>Name</p>
                        <p>{{ auth()->user()->name ?? 'Unknown' }}</p>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <p>Email</p>
                        <p>{{ auth()->user()->email ?? 'Unknown' }}</p>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <p>Email Verification</p>
                        <p>{{ auth()->user()->email_verified_at ? \Carbon\Carbon::parse(auth()->user()->email_verified_at)->format('Y-m-d h:i A') : 'Not Verified' }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <p>Birth</p>
                        <p>{{ auth()->user()->date_of_birth ? \Carbon\Carbon::parse(auth()->user()->date_of_birth)->format('d.m.Y') : '-' }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <p>Created At</p>
                        <p>{{ auth()->user()->created_at ? \Carbon\Carbon::parse(auth()->user()->created_at)->format('d.m.Y') : '-' }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <p>Role</p>
                        <p>{{ auth()->user()->role?->display_name ?? 'Unknown' }}</p>
                    </div>

                    <a class="mt-5 btn btn-sm w-fit" href="{{ route('profile.get') }}">Update Profile</a>
                    <a href="{{ route('forgot-password.get') }}" class="btn btn-sm w-fit">Reset Password</a>
                    <button class="btn btn-sm w-fit text-error" onclick="logout_modal.showModal()">Log Out</button>
                </div>
            </div>

            @if (auth()->user()->hasPermissions(['manage_site_setting']))
                <input type="radio" name="active_tab" class="tab" aria-label="Site Setting" />
                <div class="tab-content border-base-300 bg-base-100">
                    <form action="{{ route('admin.dashboard.setting.api.post') }}" method="POST"
                        class="w-full flex flex-col p-3 gap-3 resize" enctype="multipart/form-data">
                        @csrf

                        <div x-data="logoPickerState('{{ $site_logo }}')" class="flex flex-col gap-2 text-sm">
                            <label>Site Logo</label>

                            <img :src="preview"
                                class="size-28 rounded-full border border-slate-300 object-cover object-center" />

                            <div class="flex items-center gap-2">
                                <label class="btn btn-xs w-fit cursor-pointer">
                                    Choose Image
                                    <input type="file" name="site_logo" class="hidden" accept="image/*" x-ref="file"
                                        @change="handleFileChange" />
                                </label>

                                <button type="button" class="btn btn-xs w-fit text-error relative" @click="deleteLogo">
                                    <span x-show="!loading">Delete</span>
                                    <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 text-sm">
                            <label>Site Name</label>
                            <input type="hidden" name="settings[0][key]" value="site_name" />
                            <input type="text" class="input input-bordered w-full max-w-sm" name="settings[0][value]"
                                value="{{ $settings['site_name'] ?? config('app.name') }}" />
                        </div>

                        <div class="flex flex-col gap-2 text-sm">
                            <label class="flex items-center gap-2">Site Description <span class="tooltip font-normal"
                                    data-tip="{{ config('app.template_usage_tooltip') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </span></label>
                            <input type="hidden" name="settings[1][key]" value="site_description" />

                            <textarea class="textarea textarea-bordered w-full max-w-sm" name="settings[1][value]" placeholder="">{{ $settings['site_description'] ?? '' }}
                            </textarea>
                        </div>

                        <div class="flex flex-col gap-2 text-sm">
                            <label>Site Phone 1</label>
                            <input type="hidden" name="settings[2][key]" value="site_phone_1" />
                            <input type="text" class="input input-bordered w-full max-w-sm" name="settings[2][value]"
                                value="{{ $settings['site_phone_1'] ?? config('app.phone_1') }}" />
                        </div>

                        <div class="flex flex-col gap-2 text-sm">
                            <label>Site Phone 2</label>
                            <input type="hidden" name="settings[3][key]" value="site_phone_2" />
                            <input type="text" class="input input-bordered w-full max-w-sm" name="settings[3][value]"
                                value="{{ $settings['site_phone_2'] ?? config('app.phone_2') }}" />
                        </div>

                        <div class="flex flex-col gap-2 text-sm">
                            <label>Site Contact Email</label>
                            <input type="hidden" name="settings[4][key]" value="site_contact_email" />
                            <input type="text" class="input input-bordered w-full max-w-sm" name="settings[4][value]"
                                value="{{ $settings['site_contact_email'] ?? config('app.contact_email') }}" />
                        </div>

                        <div class="flex flex-col gap-2 text-sm">
                            <label>Site Support Email</label>
                            <input type="hidden" name="settings[5][key]" value="site_support_email" />
                            <input type="text" class="input input-bordered w-full max-w-sm" name="settings[5][value]"
                                value="{{ $settings['site_support_email'] ?? config('app.support_email') }}" />
                        </div>

                        <div class="flex flex-col gap-2 text-sm">
                            <label>Site Address</label>
                            <input type="hidden" name="settings[6][key]" value="site_address" />
                            <textarea class="textarea textarea-bordered w-full max-w-sm" name="settings[6][value]">{{ $settings['site_address'] ?? config('app.address') }}</textarea>
                        </div>

                        <div class="flex flex-col gap-2 text-sm">
                            <label>Site Map Location Link</label>
                            <input type="hidden" name="settings[7][key]" value="site_map_location_link" />
                            <input type="text" class="input input-bordered w-full max-w-sm" name="settings[7][value]"
                                value="{{ $settings['site_map_location_link'] ?? config('app.map_location_link') }}" />
                        </div>



                        <button class="mt-5 btn btn-sm btn-primary w-fit">Save Settings</button>
                    </form>
                </div>
            @endif

            @if (auth()->user()->hasPermissions(['manage_theme_setting']))
                <input type="radio" name="active_tab" class="tab" aria-label="Theme Setting" />
                <div class="tab-content border-base-300 bg-base-100">
                    <form action="{{ route('admin.dashboard.setting.api.post') }}" method="POST"
                        class="w-full flex flex-col p-3 gap-1.5 resize">
                        @csrf

                        <div class="flex flex-col gap-1 text-sm" x-data="{ color: '{{ $settings['site_primary_color'] }}' }">
                            <label>Site Primary Color</label>
                            <input type="hidden" name="settings[0][key]" value="site_primary_color" />
                            <input type="hidden" name="settings[0][value]" x-model="color" />
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="color"
                                    class="w-10 h-8 rounded border border-gray-300 cursor-pointer" />
                                <input type="text" x-model="color" class="input input-bordered w-24 text-sm font-mono"
                                    placeholder="#FFFFFF" maxlength="7" />
                                <div class="w-6 h-6 rounded border border-gray-300" :style="{ backgroundColor: color }">
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1 text-sm" x-data="{ color: '{{ $settings['site_primary_content_color'] }}' }">
                            <label>Site Primary Content Color</label>
                            <input type="hidden" name="settings[1][key]" value="site_primary_content_color" />
                            <input type="hidden" name="settings[1][value]" x-model="color" />
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="color"
                                    class="w-10 h-8 rounded border border-gray-300 cursor-pointer" />
                                <input type="text" x-model="color" class="input input-bordered w-24 text-sm font-mono"
                                    placeholder="#FFFFFF" maxlength="7" />
                                <div class="w-6 h-6 rounded border border-gray-300" :style="{ backgroundColor: color }">
                                </div>
                            </div>
                        </div>



                        <button class="mt-5 btn btn-sm btn-primary w-fit">Save Settings</button>
                    </form>
                </div>
            @endif

            @if (auth()->user()->hasPermissions(['manage_legal_setting']))
                <input type="radio" name="active_tab" class="tab" aria-label="Legal Setting" />
                <div class="tab-content border-base-300 bg-base-100">
                    <form action="{{ route('admin.dashboard.setting.api.post') }}" method="POST"
                        class="w-full flex flex-col p-3 gap-3 resize">
                        @csrf

                        <div class="flex flex-col gap-2 text-sm">
                            <label class="flex items-center gap-2">Site Privacy Policy <span class="tooltip font-normal"
                                    data-tip="{{ config('app.template_usage_tooltip') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </span></label>
                            <input type="hidden" name="settings[0][key]" value="site_privacy_policy" />

                            <div x-data="quillSetUpData()">
                                <div x-ref="editor" class=""></div>

                                <textarea class="hidden" name="settings[0][value]" x-ref="input">
                                    {{ $settings['site_privacy_policy'] ?? '' }}
                                </textarea>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 text-sm">
                            <label class="flex items-center gap-2">Site Terms & Conditions <span
                                    class="tooltip font-normal" data-tip="{{ config('app.template_usage_tooltip') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </span></label>
                            <input type="hidden" name="settings[1][key]" value="site_terms_conditions" />

                            <div x-data="quillSetUpData()">
                                <div x-ref="editor" class=""></div>

                                <textarea class="hidden" name="settings[1][value]" x-ref="input">
                                    {{ $settings['site_terms_conditions'] ?? '' }}
                                </textarea>
                            </div>
                        </div>
                        <button class="mt-5 btn btn-sm btn-primary w-fit">Save Legal Setting</button>
                    </form>
                </div>
            @endif


        </div>

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
            Quill.register("modules/resize", window.QuillResizeModule);
        });

        function logoPickerState(initialLogo) {
            return {
                preview: initialLogo,
                loading: false,

                handleFileChange(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.preview = URL.createObjectURL(file);
                    }
                },

                deleteLogo() {
                    this.loading = true;

                    axios.delete(
                            "{{ route('admin.dashboard.setting.api.key.delete', 'site_logo') }}"
                        )
                        .then(() => {
                            this.preview = @json($site_logo);
                            this.$refs.file.value = '';
                        })
                        .catch(() => {
                            Toast.show('Failed to delete', {
                                type: 'error'
                            });
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                }
            };
        }

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
