function randomName() {
    return (
        [1e7] +
        -1e3 +
        -4e3 +
        -8e3 +
        -1e11
    ).replace(/[018]/g, (c) =>
        (
            c ^
            (crypto.getRandomValues(new Uint8Array(1))[0] &
                (15 >> (c / 4)))
        ).toString(16),
    );
}

export default function apsonexSimpleFileField(state, args) {
    return {
        id: null,
        state: null,
        statePath: null,
        wire: null,
        disabled: false,
        progress: 0,
        processing: false,
        img: { src: null, key: null, blob: null },
        fileKeyIndex: {},
        init() {
            this.id = args.id;
            this.statePath = args.statePath;
            this.disabled = args.disabled;
            this.wire = args.$wire;
            this.state = state;
            this.getFiles();
        },
        async getFiles() {
            this.processing = true;
            // https://github.com/filamentphp/filament/blob/3.x/packages/forms/resources/views/components/file-upload.blade.php
            // https://github.com/filamentphp/filament/blob/3.x/packages/forms/resources/js/components/file-upload.js#L118
            let uploadedFiles = await args.getFormUploadedFiles();
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

            await args.deleteUploadedFileUsing(this.img.key);
            this.img.src = null;
            this.img.key = null;
        },
        uploadFile($event) {
            this.processing = true;
            this.progress = 0;

            this.readFile(
                $event.target.files[0],
                (async (file, blob) => {
                    let fileKey = randomName();
                    this.processing = true;

                    this.img.blob = blob;

                    await args.uploadUsing(
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
    };
}