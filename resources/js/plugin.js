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
        img: { src: null, urlPrefix: null, dataUri: false },
        get imgSrc() {
            if (this.img.src && !this.img.dataUri) {
                return this.img.urlPrefix + this.img.src;
            }

            if (this.img.src && this.img.dataUri) {
                return this.img.src;
            }

            return null;
        },
        init() {
            this.id = args.id;
            this.statePath = args.statePath;
            this.disabled = args.disabled;
            this.wire = args.$wire;
            this.state = state;
            this.img.src = state.initialValue;
            this.img.urlPrefix = args.urlPrefix;
        },
        async deleteFile() {
            this.img.src = null
            await this.$wire.$set(this.statePath, null);
        },
        uploadFile($event) {
            this.processing = true;
            this.progress = 0;

            this.readFile(
                $event.target.files[0],
                (async (file, blob) => {
                    await this.$wire.upload(
                        this.statePath,
                        file,
                        (uploadedFileName) => this.uploadSuccess(blob, uploadedFileName),
                        (err) => { this.processing = false, this.progress = 0 },
                        (event) => this.progress = event.detail.progress,
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