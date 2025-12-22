@extends('layouts.admin.admin_dashboard')


@section('admin_dashboard_content')
    <div class="p-3 lg:p-5" x-data="storageManager()" x-init="load()">
        <p class="text-lg font-semibold">File Manager</p>

        <div class="mt-5 flex flex-col lg:flex-row gap-3 justify-between items-start lg:items-center">
            <div class="w-fit max-w-full text-sm bg-base-300 rounded px-2 overflow-x-auto">
                <div class="breadcrumbs text-sm my-0 py-1">
                    <ul>
                        <li>
                            <span x-show="loading" class="loading loading-spinner loading-xs text-primary mr-1"></span>
                            <button class="btn btn-xs btn-ghost" @click="path=''; page=1; load()">
                                Root
                            </button>
                        </li>
                        <li></li>
                        <template x-for="(segment, index) in path.split('/').filter(Boolean)" :key="index">
                            <li>
                                <template x-if="index !== path.split('/').filter(Boolean).length - 1">
                                    <button class="btn btn-xs btn-ghost"
                                        @click="path = path.split('/').slice(0, index + 1).join('/'); page=1; load()"
                                        x-text="segment">
                                    </button>
                                </template>
                                <template x-if="index === path.split('/').filter(Boolean).length - 1">
                                    <button class="btn btn-xs btn-ghost text-primary"
                                        @click="path = path.split('/').slice(0, index + 1).join('/'); page=1; load()"
                                        x-text="segment">
                                    </button>
                                </template>
                            </li>
                        </template>
                    </ul>

                </div>
            </div>

            <div class="flex flex-row items-center gap-1">
                <button type="button" class="btn btn-sm" @click="upload_modal.showModal()">
                    Upload
                </button>
                <button type="button" class="btn btn-sm" @click="create_folder_modal.showModal()">
                    Create Folder
                </button>
            </div>

        </div>

        <div class="w-full max-w-sm alert alert-error bg-red-700 text-white text-xs my-3 flex flex-col items-start gap-1">
            <div class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <span class="text-sm font-semibold">Warning</span>
            </div>
            <p class="text-justify">
                All files shown here are part of your application system and media information.
                Changing or deleting them may remove images, products, or other important information.
                Please be careful before making any changes. They are basically just for display propose.
            </p>

            <button class="mt-2 btn btn-sm" @click="$el.parentElement.remove()">Close</button>
        </div>

        <div class="card shadow-sm border border-base-300 mt-5">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Preview</th>
                            <th class="">Size</th>
                            <th class="">Modified</th>
                            <th class="">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        <template x-for="dir in directories" :key="dir.path">
                            <tr class="">

                                <td>
                                    <button class="btn btn-ghost px-0 pr-5 btn-sm h-auto py-1 btn-sm"
                                        @click="openDir(dir.path)">
                                        <i class="">📁</i><span x-text="dir.basename"></span>
                                    </button>
                                </td>
                                <td class="flex items-center gap-2">
                                </td>
                                <td class=""></td>
                                <td class=""></td>
                                <td class="">
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                            </svg>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button type="button" class="text-error"
                                                    @click="delete_modal.showModal();delete_candidate=dir">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-for="file in files" :key="file.path">
                            <tr class="">
                                <td>
                                    <a target="_blank" :href="file.download_url"
                                        class="btn btn-ghost px-0 pr-5 btn-sm h-auto py-1">
                                        <i class="">📄</i> <span x-text="file.basename"></span>
                                    </a>
                                </td>
                                <td class="flex items-center gap-2">

                                    <template x-if="file.mime_type.startsWith('image/')">
                                        <img :src="file.download_url" alt=""
                                            class="w-10 h-10 rounded object-cover border border-slate-300"
                                            @click="document.getElementById(`image_modal_`+file.path).showModal()" />


                                    </template>
                                    <template x-if="file.mime_type.startsWith('image/')">
                                        <dialog :id="`image_modal_` + file.path" class="modal">
                                            <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                                <form method="dialog">
                                                    <button
                                                        class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                </form>
                                                <img :src="file.download_url"
                                                    class="w-full h-auto rounded-lg object-contain">
                                            </div>
                                        </dialog>
                                    </template>

                                    <template x-if="!file.mime_type">
                                        <div class="text-center text-lg">
                                            📄
                                        </div>
                                    </template>
                                </td>
                                <td class="" x-text="file.human_size"></td>
                                <td class="" x-text="file.modified"></td>
                                <td class="">
                                    <div tabindex="0" role="button" class="dropdown dropdown-left !z-[9999]">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                            </svg>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <a target="_blank" :href="file.download_url" class="">
                                                    View
                                                </a>
                                            </li>
                                            <li>
                                                <button type="button" @click="downloadFile(file)">
                                                    Download
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button"
                                                    @click="rename_modal.showModal();rename_candidate=file">
                                                    Rename
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    @click="delete_modal.showModal();delete_candidate=file">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>


                                </td>
                            </tr>
                        </template>

                        <tr x-show="files.length === 0 && directories.length === 0">
                            <td colspan="4" class=" text-gray-500 py-4">
                                No files or folders found
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div
                    class="overflow-x-auto text-sm flex flex-col gap-3 lg:flex-row items-start lg:items-center justify-between py-3 px-5">
                    <div>
                        Total size of contained files: <strong x-text="total_human"></strong>
                    </div>

                    <div class="flex justify-center" x-show="last_page > 1">
                        <div class="join">

                            <button class="join-item btn btn-sm" :class="page === 1 ? 'btn-disabled' : ''"
                                @click="page > 1 && (page = page - 1, load())">«</button>

                            <button class="join-item btn btn-sm" :class="page === 1 ? 'btn-active' : ''"
                                @click="page = 1; load()">1</button>

                            {{-- <span class="join-item btn btn-sm disabled" x-show="page > 4">…</span> --}}

                            <template x-for="p in pagesInWindow()" :key="p">
                                <button class="join-item btn btn-sm" :class="page === p ? 'btn-active' : ''"
                                    @click="page = p; load()" x-text="p"></button>
                            </template>

                            {{-- <span class="join-item btn btn-sm disabled" x-show="page < last_page - 3">…</span> --}}

                            <button class="join-item btn btn-sm" :class="page === last_page ? 'btn-active' : ''"
                                @click="page = last_page; load()" x-text="last_page"></button>

                            <button class="join-item btn btn-sm" :class="page === last_page ? 'btn-disabled' : ''"
                                @click="page < last_page && (page = page + 1, load())">»</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <dialog id="delete_modal" class="modal" @close="delete_candidate=null">
            <div class="modal-box">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <p class="text-lg font-semibold py-0">Confirm Delete</p>

                <p class="py-2 mb-0 text-sm">
                    Are you sure you want to delete
                    <span class="italic text-error" x-text="delete_candidate.basename"></span> ?
                </p>
                <div class="modal-action mt-0">
                    <form method="dialog" x-show="!deleting">
                        <button class="btn lg:btn-md">Close</button>
                    </form>
                    <div x-show="deleting" class="loading loading-spinner loading-lg text-error">
                    </div>
                    <button type="button" x-show="!deleting" class="btn lg:btn-md btn-error"
                        @click="deleteFile()">Delete</button>
                </div>
            </div>
        </dialog>

        <dialog id="rename_modal" class="modal" @close="rename_candidate=null">
            <div class="modal-box">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <p class="text-lg font-semibold py-0">Rename File</p>

                <p class="py-2 mb-0 text-sm">
                    <input type="text" x-model="newFileNameInput"
                        :placeholder="rename_candidate ? rename_candidate.basename : ''" class="input w-full">
                </p>
                <div class="modal-action mt-0">
                    <form method="dialog" x-show="!renaming">
                        <button class="btn lg:btn-md">Close</button>
                    </form>
                    <div x-show="renaming" class="loading loading-spinner loading-lg text-primary">
                    </div>
                    <button type="button" x-show="!renaming" class="btn lg:btn-md btn-primary"
                        @click="renameFile()">Rename</button>
                </div>
            </div>
        </dialog>

        <dialog id="upload_modal" class="modal">
            <div class="modal-box">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <p class="text-lg font-semibold py-0">Upload File</p>

                <div class="py-2 mb-0 text-sm">

                    <input type="file" x-ref="fileInput" class="file-input file-input-bordered w-full" />

                    <div class="mt-3 w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700" x-show="progress > 0">
                        <div class="bg-blue-600 h-2.5 rounded-full" :style="`width: ${progress}%`"></div>
                    </div>

                    <p class="mt-2 text-sm" x-text="status"></p>
                </div>
                <div class="modal-action mt-0 ">
                    <form method="dialog" x-show="!uploading">
                        <button class="btn lg:btn-md">Close</button>
                    </form>
                    <div x-show="uploading" class="loading loading-spinner loading-lg text-primary">
                    </div>
                    <button type="button" class="btn btn-sm"
                        @click="cancelTokenSource && cancelTokenSource.cancel('User canceled upload')" x-show="uploading">
                        Stop Upload
                    </button>
                    <button x-show="!uploading" class="btn btn-primary" @click="uploadFile()">
                        Upload
                    </button>

                </div>
            </div>
        </dialog>

        <dialog id="create_folder_modal" class="modal">
            <div class="modal-box">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <p class="text-lg font-semibold py-0">Creat folder</p>

                <p class="py-2 mb-0 text-sm">
                    <input type="text" x-model="createFolderNameInput" placeholder="Enter Folder Name"
                        class="input w-full">
                </p>
                <div class="modal-action mt-0">
                    <form method="dialog" x-show="!creatingFolder">
                        <button class="btn lg:btn-md">Close</button>
                    </form>
                    <div x-show="creatingFolder" class="loading loading-spinner loading-lg text-primary">
                    </div>
                    <button type="button" x-show="!creatingFolder" class="btn lg:btn-md btn-primary"
                        @click="createFolder()">Create</button>
                </div>
            </div>
        </dialog>
    </div>
@endsection


@push('script')
    <script>
        function downloadFile(file) {
            const link = document.createElement('a');
            link.href = file.download_url;
            link.download = file.basename; // suggest file name
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function storageManager() {
            return {
                path: '/',
                page: 1,
                perPage: 100,
                last_page: 1,
                directories: [],
                files: [],
                total_human: '0 B',

                deleting: false,
                delete_candidate: null,
                loading: false,

                newFileNameInput: '',
                renaming: false,
                rename_candidate: null,

                progress: 0,
                status: '',
                uploading: false,
                cancelTokenSource: null,

                createFolderNameInput: '',
                creatingFolder: false,


                async load() {
                    const url = '/storage-manager/list';

                    try {
                        this.loading = true;
                        const response = await axios.get(url, {
                            params: {
                                path: this.path,
                                page: this.page,
                                per_page: this.perPage
                            },
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        this.loading = false;

                        this.path = response.data.path;
                        this.directories = response.data.directories;
                        this.files = response.data.files;
                        this.total_human = response.data.total_human;

                        this.page = response.data.current_page;
                        this.last_page = response.data.last_page;

                    } catch (error) {
                        console.error('Error fetching files:', error);
                    }
                },

                openDir(dir) {
                    this.path = dir;
                    this.page = 1;
                    this.load();
                },

                goUp() {
                    if (!this.path) return;
                    const parts = this.path.split('/');
                    parts.pop();
                    this.path = parts.join('/');
                    this.page = 1;
                    this.load();
                },

                pagesInWindow() {
                    const windowSize = 1; // 99
                    let start = Math.max(this.page - windowSize, 2); // 98
                    let end = Math.min(this.page + windowSize, this.last_page - 1); // 99

                    const pages = [];
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    return pages;
                },

                async deleteFile() {
                    if (!this.delete_candidate) return;


                    try {
                        this.deleting = true;

                        const response = await axios.delete('/storage-manager/delete', {
                            data: {
                                path: this.delete_candidate.path
                            },
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (response.data.success) {
                            await this.load();
                        } else {
                            alert(response.data.message || "Delete failed");
                        }
                    } catch (error) {
                        console.error('Delete error:', error);
                        alert(error.response?.data?.message || "Error deleting file");
                    } finally {
                        this.deleting = false;
                        this.delete_candidate = null;
                        document.getElementById('delete_modal').close();
                    }
                },

                async createFolder() {
                    if (!this.createFolderNameInput.trim()) {
                        alert("Please enter a folder name");
                        return;
                    }

                    this.creatingFolder = true;
                    try {
                        const response = await axios.post('/storage-manager/create-folder', {
                            path: this.path,
                            name: this.createFolderNameInput.trim()
                        }, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (response.data.success) {
                            this.load(); // reload files
                            this.createFolderNameInput = '';
                            document.getElementById('create_folder_modal').close();
                        } else {
                            alert(response.data.message || "Error creating folder");
                        }
                    } catch (error) {
                        console.error("Create folder error:", error);
                        alert(error.response?.data?.message || "Error creating folder");
                    } finally {
                        this.creatingFolder = false;
                    }
                },

                async renameFile() {
                    if (!this.rename_candidate || !this.newFileNameInput) return;

                    this.renaming = true;

                    try {
                        const response = await axios.post('/storage-manager/rename', {
                            old_path: this.rename_candidate.path,
                            new_name: this.newFileNameInput
                        }, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (response.data.success) {
                            await this.load();
                        } else {
                            alert(response.data.message || "Rename failed");
                        }
                    } catch (error) {
                        console.error('Rename error:', error);
                        alert(error.response?.data?.message || "Error renaming file");
                    } finally {
                        this.renaming = false;
                        this.rename_candidate = null;
                        this.newFileNameInput = '';
                        document.getElementById('rename_modal').close();
                    }
                },


                async uploadFile() {
                    const fileInput = this.$refs.fileInput;
                    if (!fileInput.files.length) {
                        alert("Please select a file first");
                        return;
                    }

                    this.cancelTokenSource = axios.CancelToken.source();

                    const formData = new FormData();
                    formData.append('file', fileInput.files[0]);
                    formData.append('path', this.path);

                    this.progress = 0;
                    this.status = "Uploading...";
                    this.uploading = true;

                    try {
                        const response = await axios.post('/storage-manager/upload', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                'Accept': 'application/json'
                            },
                            onUploadProgress: (progressEvent) => {
                                if (progressEvent.total) {
                                    this.progress = Math.round((progressEvent.loaded * 100) / progressEvent
                                        .total);
                                }
                            },
                            cancelToken: this.cancelTokenSource.token,
                        });

                        if (response.data.success) {
                            this.status = "Upload Completed ✅";
                            this.progress = 0;
                            await this.load();

                        } else {
                            this.status = response.data.message || "Upload failed ❌";
                        }

                    } catch (error) {
                        console.error("Upload error:", error);
                        this.status = error.response?.data?.message || "Error uploading file ❌";
                    } finally {
                        document.getElementById('upload_modal').close();
                        this.uploading = false;
                        this.cancelTokenSource = null;
                        this.progress = 0;
                        fileInput.value = '';
                        setTimeout(() => {
                            this.status = '';
                        }, 2000);
                    }
                }
            }
        }
    </script>
@endpush
