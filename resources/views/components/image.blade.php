@php
    use Apsonex\FilamentImage\FilamentImageServiceProvider;
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
            @js(filament_asset_route('resources/dist/plugin.css', FilamentImageServiceProvider::class)),
        ]"
        ax-load
        ax-load-src="{{ filament_asset_route('resources/dist/plugin.js', FilamentImageServiceProvider::class) }}"
        x-data="apsonexImageField(
            $wire.{{ $applyStateBindingModifiers("entangle('{$getStatePath()}')") }}, {
                $wire: $wire,
                disabled: {{ $isDisabled ? 'true' : 'false' }},
                id: '{{ $id }}',
                statePath: '{{ $statePath }}',
                urlPrefix: '{{ $getUrlPrefix() }}',
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                maxSize: @js(($size = $getMaxSize()) ? "'{$size} KB'" : null),
                minSize: @js(($size = $getMinSize()) ? "'{$size} KB'" : null),
            }
        )"
        x-on:apsonex-filament-image-field-uploaded.window="fileUploaded"
    >
        <div
            class="flex flex-col flex-wrap relative"
            x-cloak
        >
            <template x-if="img.src">
                <div class="relative flex w-full">
                    <div
                        class="w-full flex aspect-square relative border shadow rounded-lg overflow-hidden min-h-[100px]">
                        <img
                            x-bind:src="imgSrc"
                            class="object-contain max-w-full w-full h-auto"
                        />
                    </div>
                    <button
                        @click.prevent="deleteFile(img.key)"
                        type="button"
                        class="absolute top-0 mt-2 mr-2 right-0 text-xs font-semibold text-red-500 w-8 h-8 p-1 border border-gray-200 rounded-full z-10 bg-white shadow hover:text-primary-500 hover:bg-gray-200"
                    ><x-heroicon-o-trash class="w-full h-full" /></button>
                </div>
            </template>

            <template x-if="!img.src">
                <div class="">
                    <button
                        type="button"
                        x-bind:disabled="disabled"
                        x-on:click.prevent="
                            $refs.fileUploadInput.value = null
                            $refs.fileUploadInput.click();
                        "
                        class="px-4 py-2 text-sm text-primary-500 border rounded-lg"
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
                class="absolute inset-0 bg-white/80 flex flex-col justify-center items-center"
                x-show="processing"
                x-transition
            >
                <div class="flex flex-col justify-center items-center">
                    <span class="inline-block flex justify-center items-center"><x-filament-image::spinner
                            class="w-6 h-6"
                        /></span>
                </div>

                <div
                    class="absolute block w-full h-1 bg-gray-400 z-0 top-0 left-0"
                    x-show="progress > 0"
                >
                    <span
                        class="absolute block w-full z-10 left-0 h-1 bg-primary-500"
                        x-bind:style="{ width: progress + '%' }"
                    ></span>
                </div>

            </div>

        </div>
    </div>
</x-dynamic-component>
