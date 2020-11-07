 <template>
    <el-upload
            ref="upload"
            action="#"
            :list-type="list_type"
            :auto-upload="autoUpload"
            :file-list="file_list"
            :multiple="multiple"
            :on-change="handleChange"
    >
        <i slot="default" class="el-icon-plus"></i>
        <div slot="file" slot-scope="{file}">
            <img class="el-upload-list__item-thumbnail" :src="file.url" :alt="file.name">
            <span class="el-upload-list__item-actions">
                <span class="el-upload-list__item-delete" @click="handleRemove(file)">
                  <i class="el-icon-delete"></i>
                </span>
            </span>
        </div>
    </el-upload>
</template>

<script>
    export default {
        name: "UploadImageCustom",
        props: {
            delete_action: {
                type: String,
                required: true
            },
            list_type: {
                type: String,
                default: 'picture-card'
            },
            file_list: {
                type: Array
            },
            autoUpload: {
                type: Boolean,
                default: false
            },
            multiple: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {}
        },
        methods: {
            handleRemove(file) {
                if (this.delete_action !== '#') {
                    axios.delete(this.delete_action, {params: {image_name: file.name}}).then((response) => {
                        this.$refs.upload.uploadFiles.splice(this.$refs.upload.uploadFiles.findIndex(uploadFile => uploadFile.uid === file.uid), 1);
                        this.convertToBase64(this.$refs.upload.uploadFiles)
                    }).catch((error) => {
                        console.log(error)
                    })
                } else {
                    this.$refs.upload.uploadFiles.splice(this.$refs.upload.uploadFiles.findIndex(uploadFile => uploadFile.uid === file.uid), 1);
                    this.convertToBase64(this.$refs.upload.uploadFiles)
                }
            },
            handleChange(fileData, fileList) {
                const file = fileData.raw;
                if (!file.type.includes('image/')) {
                    alert('Please select an image file');
                    return;
                }

                this.convertToBase64(fileList)
            },
            convertToBase64(fileList) {
                let images = [];
                if (typeof FileReader === 'function') {

                    for (let i = 0; i < fileList.length; i++) {
                        if (Object.keys(fileList[i]).includes('raw')) {
                            const reader = new FileReader();
                            reader.onload = (event) => {
                                images.push(event.target.result)
                            };
                            reader.readAsDataURL(fileList[i].raw);
                        }
                    }
                } else {
                    alert('Sorry, FileReader API not supported');
                }

                this.$emit('images', images);
            }
        },
    }
</script>
