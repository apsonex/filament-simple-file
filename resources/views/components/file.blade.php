@php
    use Apsonex\FilamentSimpleFile\FilamentSimpleFileServiceProvider;
    $id = $getId();
    $isConcealed = $isConcealed();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $acceptedFileTypes = $getAcceptedFileTypes();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-ignore
        class=""
        x-load-css="[
            @js(filament_asset_route('resources/dist/plugin.css', FilamentSimpleFileServiceProvider::class)),
        ]"
        ax-load
        ax-load-src="{{ filament_asset_route('resources/dist/plugin.js', FilamentSimpleFileServiceProvider::class) }}"
        x-data="apsonexSimpleFileField(
            $wire.{{ $applyStateBindingModifiers("entangle('{$getStatePath()}')") }}, {
                $wire: $wire,
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
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                maxSize: @js(($size = $getMaxSize()) ? "'{$size} KB'" : null),
                minSize: @js(($size = $getMinSize()) ? "'{$size} KB'" : null),
            }
        )"
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
