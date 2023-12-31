@php
    use Apsonex\FilamentSimpleFile\FilamentSimpleFileServiceProvider;
    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath(true);
    $acceptedFileTypes = $getAcceptedFileTypes();
    $maxSize = $getMaxSize() ?: 'null';
    $minSize = $getMinSize() ?: 'null';
    $uploadedFile = $getUploadedFile($statePath) ?: [];
    $imageHeight = $getImageHeight() ?: null;
    $aspectVideoView = $getAspectVideoView() ?: null;
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        wire:ignore
        x-data="{
            css: '{{ \Filament\Support\Facades\FilamentAsset::getStyleHref('filament-simple-file-css-plugin', 'apsonex/filament-simple-file') }}{{ app()->environment('local') ? '&cache=' . now()->getTimestamp() : '' }}',
            randomName: () => ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, (c) => (c ^ (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))).toString(16), ),
            id: '{{ $id }}',
            statePath: '{{ $statePath }}',
            {{-- getFormUploadedFiles: async () => await $wire.getFormUploadedFiles(@js($statePath)), --}}
            uploadUsing: (fileKey, file, success, error, progress) => {
                $wire.upload(
                    `{{ $statePath }}.${fileKey}`,
                    file,
                    () => success(fileKey),
                    error,
                    (progressEvent) => progress(true, progressEvent.detail.progress, 100),
                )
            },
            deleteUploadedFileUsing: async (fileKey) => await $wire.deleteUploadedFile(@js($statePath), fileKey),
            removeUploadedFileUsing: async (fileKey) => await $wire.removeFormUploadedFile(@js($statePath), fileKey),
            state: $wire.$entangle('{{ $statePath }}'),
            maxSize: {{ $maxSize }},
            minSize: {{ $minSize }},
            state: null,
            wire: null,
            progress: 0,
            processing: false,
            img: { src: null, key: null, blob: null },
            uploadedFiles: {},
            disabled: {{ $isDisabled ? 'true' : 'false' }},
            imageHeight: {{ $imageHeight ? $imageHeight : 'null' }},
            addCss() {
                if (document.querySelector(`link[href='${this.css}']`)) {
                    return;
                }
                const link = document.createElement('link')
                link.type = 'text/css';
                link.rel = 'stylesheet';
                link.href = this.css;
                document.head.prepend(link);
            },
            init() {
                this.addCss();
                this.getFiles();
            },
            get hasImage() {
                if (this.img.blob || this.img.src) return true;
                return false;
            },
            get imageSrc() {
                if (this.img.blob) return this.img.blob;
                return this.img.src
            },
            async getFiles() {
                this.processing = true;
                // https://github.com/filamentphp/filament/blob/3.x/packages/forms/resources/views/components/file-upload.blade.php
                // https://github.com/filamentphp/filament/blob/3.x/packages/forms/resources/js/components/file-upload.js#L118
                this.uploadedFiles = @js($uploadedFile);
                this.img.key = Object.keys(this.uploadedFiles)[0];
                if (this.img.key) {
                    this.img.src = this.uploadedFiles[this.img.key].url;
                }
                this.processing = false;
            },
            async deleteFile() {
                this.img.blob = null;

                if (!this.img.key) {
                    return;
                }
                await this.deleteUploadedFileUsing(this.img.key);
                this.img.src = null;
                this.img.key = null;
            },
            readFile(file, onSuccess) {
                var reader = new FileReader();
                reader.onload = () => onSuccess(file, reader.result);
                reader.readAsDataURL(file);
            },
            uploadFile($event) {
                this.processing = true;
                this.progress = 0;

                this.readFile(
                    $event.target.files[0],
                    (async (file, blob) => {
                        let fileKey = this.randomName();
                        this.processing = true;

                        this.img.blob = blob;

                        await this.uploadUsing(
                            fileKey,
                            file,
                            (fileKey) => {
                                this.processing = false;
                                this.img.key = fileKey;
                            },
                            (err) => {
                                console.log('error: ' + err);
                            },
                            ($progressEvent) => {
                                console.log($progressEvent);
                            }
                        )
                    }).bind(this)
                )
            },
            imageUpdated($event) {
                if (!$event.detail.data[this.statePath]) return;

                let imageHeight = $event.detail.data[this.statePath].imageHeight;

                if (imageHeight) {
                    this.imageHeight = parseInt(imageHeight);
                }
            }
        }"
        x-on:simpl-image-file-updated.window="imageUpdated"
    >
        <div
            class="relative flex flex-col flex-wrap"
            x-cloak
        >
            <div
                class="relative flex flex-col items-start w-full"
                x-show="hasImage"
            >
                <div
                    class="relative flex w-full overflow-hidden border rounded-lg shadow"
                    x-bind:class="{
                        '{{ $aspectVideoView ? 'aspect-video' : 'aspect-square' }} min-h-[100px]': !imageHeight,
                    }"
                    x-bind:style="{
                        maxWidth: '400px',
                    }"
                >
                    <img
                        x-bind:src="imageSrc"
                        class="object-contain w-full h-auto max-w-full"
                        x-bind:style="{
                            height: imageHeight ? imageHeight + 'px' : 'auto',
                        }"
                    />
                </div>

                <button
                    @click.prevent="deleteFile(img.key)"
                    type="button"
                    {{-- class="absolute top-0 right-0 z-10 w-8 h-8 p-1 mt-2 mr-2 text-xs font-semibold text-red-500 bg-white border border-gray-200 rounded-full shadow hover:text-primary-500 hover:bg-gray-200" --}}
                    class="z-10 flex items-center mt-1 text-xs font-medium text-danger-500"
                >
                    <x-heroicon-o-trash class="w-4 h-4 mr-0.5" />
                    <span class="">Delete</span>
                </button>
            </div>

            <div
                class=""
                x-show="!hasImage"
            >
                <button
                    type="button"
                    x-bind:disabled="disabled"
                    x-on:click.prevent="
                            $refs.fileUploadInput.value = null
                            $refs.fileUploadInput.click();
                        "
                    class="px-4 py-2 text-sm border rounded-lg text-primary-500"
                >Upload</button>
                <input
                    type="file"
                    x-bind:disabled="disabled"
                    x-ref="fileUploadInput"
                    x-on:change.prevent="uploadFile"
                    class="hidden"
                    @if ($acceptedFileTypes) accept="{{ implode(',', $acceptedFileTypes) }}" @endif
                />
            </div>

            <div
                class="absolute inset-0 flex flex-col items-center justify-center bg-white/80"
                x-show="processing"
                x-transition
            >
                <div class="flex flex-col items-center justify-center">
                    <span class="flex items-center justify-center inline-block"><x-filament-simple-file::spinner
                            class="w-6 h-6"
                        /></span>
                </div>

                <div
                    class="absolute top-0 left-0 z-0 block w-full h-1 bg-gray-400"
                    x-show="progress > 0"
                >
                    <span
                        class="absolute left-0 z-10 block w-full h-1 bg-primary-500"
                        x-bind:style="{ width: progress + '%' }"
                    ></span>
                </div>

            </div>

        </div>
    </div>
</x-dynamic-component>
