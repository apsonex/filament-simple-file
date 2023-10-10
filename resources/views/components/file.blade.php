@php
    use Apsonex\FilamentSimpleFile\FilamentSimpleFileServiceProvider;
    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath(true);
    $acceptedFileTypes = $getAcceptedFileTypes();
    $maxSize = $getMaxSize() ?: 'null';
    $minSize = $getMinSize() ?: 'null';
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-ignore
        ax-load
        x-load-css="[
            '{{ \Filament\Support\Facades\FilamentAsset::getStyleHref('filament-simple-file-css-plugin', 'apsonex/filament-simple-file') }}{{ app()->environment('local') ? '&cache=' . now()->getTimestamp() : '' }}'
        ]"
        x-data="{
            randomName: () => ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, (c) =>(c ^(crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4))) ).toString(16),),
            disabled: {{ $isDisabled ? 'true' : 'false' }},
            id: '{{ $id }}',
            statePath: '{{ $statePath }}',
            getFormUploadedFiles: async () => await $wire.getFormUploadedFiles(@js($statePath)),
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
            fileKeyIndex: {},
            init() {
                this.getFiles();
            },
            async getFiles() {
                this.processing = true;
                // https://github.com/filamentphp/filament/blob/3.x/packages/forms/resources/views/components/file-upload.blade.php
                // https://github.com/filamentphp/filament/blob/3.x/packages/forms/resources/js/components/file-upload.js#L118
                let uploadedFiles = await this.getFormUploadedFiles();
                this.fileKeyIndex = uploadedFiles ?? {}
                this.img.key = Object.keys(this.fileKeyIndex)[0];
                this.img.src = this.img.key ? this.fileKeyIndex[this.img.key].url : null;
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
            uploadSuccess(blob, fileName) {
                this.img.dataUri = true;
                this.img.src = blob;
                this.processing = false;
            },
            readFile(file, onSuccess) {
                var reader = new FileReader();
                reader.onload = () => onSuccess(file, reader.result);
                reader.readAsDataURL(file);
            }
        }"
    >
        <div
            class="relative flex flex-col flex-wrap"
            x-cloak
        >
            <template x-if="img.blob || img.src">
                <div class="relative flex w-full">
                    <div
                        class="w-full flex aspect-square relative border shadow rounded-lg overflow-hidden min-h-[100px]">
                        <img
                            x-bind:src="img.blob || img.src"
                            class="object-contain w-full h-auto max-w-full"
                        />
                    </div>
                    <button
                        @click.prevent="deleteFile(img.key)"
                        type="button"
                        class="absolute top-0 right-0 z-10 w-8 h-8 p-1 mt-2 mr-2 text-xs font-semibold text-red-500 bg-white border border-gray-200 rounded-full shadow hover:text-primary-500 hover:bg-gray-200"
                    ><x-heroicon-o-trash class="w-full h-full" /></button>
                </div>
            </template>

            <template x-if="!img.src && !img.blob">
                <div class="">
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
            </template>

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
