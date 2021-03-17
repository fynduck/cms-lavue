<template>
    <div>
        <b-form-checkbox id="ration"
                         class="mb-3"
                         switch
                         v-model="settings.ratio"
                         :value="1"
                         :unchecked-value="0">
            {{ $t('Menu.ratio') }}
        </b-form-checkbox>
        <b-row v-if="settings.ratio && settings.ratios">
            <b-col class="mb-3">
                <label for="ration_width">{{ $t('Menu.width') }}</label>
                <b-form-input id="ration_width" v-model.number="settings.ratios.width" type="number"></b-form-input>
            </b-col>
            <b-col class="mb-3">
                <label for="ration_height">{{ $t('Menu.height') }}</label>
                <b-form-input id="ration_height" v-model.number="settings.ratios.height" type="number"></b-form-input>
            </b-col>
        </b-row>
        <b-row class="mb-1 size" v-for="(size, key) in settings.sizes" :key="key">
            <b-col class="mb-3">
                <label :for="`name_${key}`">{{ $t('Menu.size_name') }}</label>
                <b-form-input :id="`name_${key}`" v-model="size.name"></b-form-input>
            </b-col>
            <b-col class="mb-3">
                <label :for="`width_${key}`">{{ $t('Menu.width') }}</label>
                <b-form-input :id="`width_${key}`" v-model.number="size.width" type="number"
                              @input="calculateSizeWithRatio(size, 'w')"></b-form-input>
            </b-col>
            <b-col class="mb-3">
                <label :for="`height_${key}`">{{ $t('Menu.height') }}</label>
                <b-form-input :id="`height_${key}`" v-model.number="size.height" type="number"
                              @input="calculateSizeWithRatio(size, 'h')"></b-form-input>
            </b-col>
            <fa :icon="['fas', 'trash-alt']" class="text-danger remove" @click="deleteSize(key)"/>
        </b-row>
        <b-row class="align-items-center">
            <b-col sm="6" class="mb-3">
                <b-form-select v-model="settings.action" :options="resizes" size="sm" class="my-3"></b-form-select>
            </b-col>
            <b-col sm="6" class="mb-3">
                <b-form-checkbox v-model="settings.greyscale" switch>
                    {{ $t('Menu.greyscale') }}
                </b-form-checkbox>
            </b-col>
        </b-row>
        <b-row>
            <b-col md="4" class="mb-3">
                <b-form-group :label="$t('Menu.blur')" label-for="blur">
                    <b-input-group>
                        <b-form-input
                            id="blur"
                            v-model.number="settings.blur"
                            type="range"
                            number
                            min="0"
                            max="100"
                        ></b-form-input>
                        <b-input-group-append is-text class="text-monospace">
                            {{ settings.blur }}
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-col>
            <b-col md="4" class="mb-3">
                <b-form-group :label="$t('Menu.brightness')" label-for="brightness">
                    <b-input-group>
                        <b-form-input
                            id="brightness"
                            v-model.number="settings.brightness"
                            type="range"
                            number
                            min="-100"
                            max="100"
                        ></b-form-input>
                        <b-input-group-append is-text class="text-monospace">
                            {{ settings.brightness }}
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-col>
            <b-col md="4" class="mb-3">
                <label for="background">{{ $t('Menu.background') }}</label>
                <div class="btn-group d-flex">
                    <b-form-input v-model="settings.background" id="background" type="color"></b-form-input>
                    <b-button @click="removeBg">
                        <fa :icon="['fas', 'trash-alt']"/>
                    </b-button>
                </div>
            </b-col>
        </b-row>
        <b-row class="justify-content-between">
            <b-col>
                <b-button variant="info" @click.prevent="addSize" :title="$t('Menu.add_size')">
                    <fa :icon="['fas', 'plus']"/>
                </b-button>
            </b-col>
            <b-col class="text-right">
                <b-button variant="primary" :class="{'btn-loading': loading_setting}" :title="$t('Menu.save')"
                          @click="saveSettings"
                          :disabled="loading_setting">
                    <fa :icon="['fas', 'save']"/>
                </b-button>
            </b-col>
        </b-row>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "SizeSettings",
    props: {
        settings: {
            type: Object,
            required: true
        },
        source: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            resizes: [
                {
                    value: 'resize-crop',
                    text: this.$t('Menu.resize_crop')
                },
                {
                    value: 'resize',
                    text: this.$t('Menu.resize')
                },
                {
                    value: 'crop',
                    text: this.$t('Menu.crop')
                }
            ],
            loading_setting: false
        }
    },
    watch: {
        'settings.ratios': {
            handler() {
                this.calculateSizeWithRatio()
            },
            deep: true
        }
    },
    methods: {
        emptySize() {
            return {
                name: '',
                width: 0,
                height: 0,
            }
        },
        addSize() {
            this.settings.sizes.push(this.emptySize())
        },
        deleteSize(index) {
            this.settings.sizes.splice(index, 1)
        },
        removeBg() {
            this.settings.background = null
        },
        saveSettings() {
            this.loading_setting = true;
            axios.post(`${this.source}-settings`, this.settings).then(response => {
                this.$bvModal.hide('menu-settings')
                this.$toast.global.success(this.$t('Menu.settings_saved'))
                this.loading_setting = false
            }).catch(error => {
            })
        },
        calculateSizeWithRatio(size = null, field = null) {
            const rationW = this.settings.ratios.width;
            const rationH = this.settings.ratios.height;
            if (this.settings.ratio && rationW && rationH) {

                if (size !== null && field) {
                    if (field === 'w') {
                        size.height = Math.round((parseInt(size.width) / rationW) * rationH);
                    } else if (field === 'h') {
                        size.width = Math.round(parseInt(size.height) * (rationW / rationH))
                    }
                } else {
                    this.settings.sizes.forEach(item => {
                        if (item.width) {
                            item.height = Math.round((parseInt(item.width) / rationW) * rationH)
                        } else if (item.height) {
                            item.width = Math.round(parseInt(item.height) * (rationW / rationH))
                        }
                    })
                }
            }
        }
    }
}
</script>