<template>
    <div class="d-flex flex-wrap">
        <b-form-file
            class="upload_files"
            :multiple="multiple"
            v-model="file"
            :placeholder="$t('choose_file_drag')"
            :drop-placeholder="$t('drop_files')" @input="onFileChange"
            :accept="accept"
            browse-text=""
        >
            <template slot="file-name" slot-scope="{ names }">
                <span>{{ $t('selected') }}:{{ urls.length }}</span>
            </template>
        </b-form-file>
        <div v-for="(img, index) in urls" v-if="urls.length" class="list_uploaded">
            <div class="hover">
                <fa :icon="['fas', 'trash-alt']" @click.prevent="confirmRemove(index)"/>
            </div>
            <img :src="img" alt="">
        </div>
        <b-overlay :show="busy" no-wrap fixed>
            <template v-slot:overlay>
                <p><strong>{{ $t('are_you_sure') }}</strong></p>
                <div class="d-flex">
                    <b-button variant="outline-danger" class="mr-3" @click="cancelRemove()">
                        {{ $t('cancel') }}
                    </b-button>
                    <b-button variant="outline-success" @click="removeItem">{{ $t('yes') }}</b-button>
                </div>
            </template>
        </b-overlay>
    </div>
</template>

<script>
export default {
    name: "Upload",
    props: {
        value: {
            type: String | Array,
            default: null
        },
        accept: {
            type: String,
            default: '.jpg, .png, .gif'
        }
    },
    data() {
        return {
            file: null,
            urls: [],
            uploaded: null,
            multiple: false,
            busy: false,
            removeKey: null
        }
    },
    created() {
        // this.file = this.value
        this.uploaded = Array.isArray(this.value) ? [] : null;
        this.multiple = Array.isArray(this.value);
        if (this.value)
            this.urls = Array.isArray(this.value) ? this.value : [this.value];
    },
    methods: {
        onFileChange(e) {
            if (Array.isArray(e)) {
                for (let i = 0; i < e.length; i++) {
                    this.convertToBase64(e[i])
                    this.generateUrls(e[i])
                }
            } else {
                this.convertToBase64(e)

                if (!Array.isArray(this.value))
                    this.urls = [];

                this.generateUrls(e)
            }
        },
        convertToBase64(file) {
            const reader = new FileReader();

            reader.onload = (event) => {
                if (Array.isArray(this.uploaded))
                    this.uploaded.push(event.target.result)
                else
                    this.uploaded = event.target.result

                this.$emit('input', this.uploaded)
            };

            reader.readAsDataURL(file);
        },
        generateUrls(file) {
            this.urls.push(URL.createObjectURL(file));
        },
        confirmRemove(index) {
            this.busy = true;
            this.removeKey = index;
        },
        cancelRemove() {
            this.busy = false;
            this.removeKey = null;
        },
        removeItem() {
            if (this.uploaded) {
                if ((Array.isArray(this.uploaded) && this.uploaded.length))
                    this.uploaded.splice(this.removeKey, 1)
                else
                    this.uploaded = null
            }

            this.urls.splice(this.removeKey, 1)
            this.$nextTick(() => {
                this.busy = false;
                this.$emit('input', this.uploaded)

                this.removeKey = null;
            })
        }
    }
}
</script>
<style lang="stylus">
.custom-file
    &.upload_files
        width 120px
        height 120px
        cursor pointer
        margin .1rem
        overflow hidden

        .custom-file-input
            height 100%
            cursor pointer

            ~ .custom-file-label::after
                content none

        .custom-file-label
            height 100%
            cursor pointer
            font-size 14px
            color #cccccc
            display flex
            align-items center
            border-width 4px
            border-style dotted
            white-space pre-wrap

.list_uploaded
    width 120px
    height 120px
    border 1px solid #cccccc
    margin .1rem
    position relative
    display flex
    justify-content center
    align-items center

    .hover
        cursor pointer
        z-index 1
        display flex
        justify-content center
        align-items center
        transition all .5s

        svg
            position absolute
            opacity 0
            color #fff

    &:hover
        .hover
            background-color rgba(0, 0, 0, 0.57)
            position absolute
            left 0
            top 0
            width 100%
            height 100%

            svg
                opacity 1

    img
        max-width 100%
        max-height 100%

</style>
