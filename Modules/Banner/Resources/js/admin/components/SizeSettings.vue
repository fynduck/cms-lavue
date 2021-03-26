<template>
    <b-tabs content-class="mt-3" v-if="Object.keys(settings).length">
        <b-tab :title="$t(`Banner.${index}`)" v-for="(item, index) in settings" :key="index">
            <b-form-checkbox :id="`ratio_${index}`"
                             switch
                             v-model="item.ratio"
                             :value="1"
                             :unchecked-value="0">
                {{ $t('Banner.ratio') }}
            </b-form-checkbox>
            <b-row v-if="item.ratio && item.ratios">
                <b-col class="mb-3">
                    <label for="ration_width">{{ $t('Banner.width') }}</label>
                    <b-form-input id="ration_width" v-model.number="item.ratios.width" type="number"
                                  @input="calculateSizeWithRatio(index)"></b-form-input>
                </b-col>
                <b-col class="mb-3">
                    <label for="ration_height">{{ $t('Banner.height') }}</label>
                    <b-form-input id="ration_height" v-model.number="item.ratios.height" type="number"
                                  @input="calculateSizeWithRatio(index)"></b-form-input>
                </b-col>
            </b-row>
            <b-row class="mb-1 size" v-for="(size, key) in item.sizes" :key="key">
                <b-col cols="12" sm="4" class="mb-3">
                    <b-form-group :label="$t('Banner.size_name')" :label-for="`name_${index}_${key}`">
                        <b-input-group>
                            <b-input-group-prepend is-text v-b-tooltip.hover :title="$t('Banner.mobile_size')">
                                <b-form-checkbox
                                    v-model="size.mobile"
                                    :value="1"
                                    :unchecked-value="0"
                                >
                                </b-form-checkbox>
                            </b-input-group-prepend>
                            <b-form-input :id="`name_${index}_${key}`" v-model="size.name"></b-form-input>
                        </b-input-group>
                    </b-form-group>
                </b-col>
                <b-col cols="12" sm="4" class="mb-3">
                    <label :for="`width_${index}_${key}`">{{ $t('Banner.width') }}</label>
                    <b-form-input :id="`width_${index}_${key}`" v-model.number="size.width" type="number"
                                  @input="calculateSizeWithRatio(index, size, 'w')"></b-form-input>
                </b-col>
                <b-col cols="12" sm="4" class="mb-3">
                    <label :for="`height_${index}_${key}`">{{ $t('Banner.height') }}</label>
                    <b-form-input :id="`height_${index}_${key}`" v-model.number="size.height" type="number"
                                  @input="calculateSizeWithRatio(index, size, 'h')"></b-form-input>
                </b-col>
                <fa :icon="['fas', 'trash-alt']" class="text-danger remove" @click="deleteSize(index, key)"/>
            </b-row>
            <b-row class="align-items-center">
                <b-col sm="6" class="mb-3">
                    <b-form-checkbox v-model="item.optimize" switch>
                        {{ $t('Banner.optimize') }}
                    </b-form-checkbox>
                </b-col>
                <b-col sm="6" class="mb-3">
                    <b-form-checkbox v-model="item.greyscale" switch>
                        {{ $t('Banner.greyscale') }}
                    </b-form-checkbox>
                </b-col>
                <b-col class="mb-3">
                    <b-form-group :label="$t('Banner.action')" label-for="action">
                        <b-form-select v-model="item.action" :options="resizes" size="sm" id="action"></b-form-select>
                    </b-form-group>
                </b-col>
                <b-col class="mb-3">
                    <b-form-group :label="$t('Banner.format')" label-for="encode">
                        <b-form-select v-model="item.encode" :options="formats" size="sm" id="encode"></b-form-select>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col md="4" class="mb-3">
                    <b-form-group :label="$t('Banner.blur')" label-for="blur">
                        <b-input-group>
                            <b-form-input
                                id="blur"
                                v-model.number="item.blur"
                                type="range"
                                number
                                min="0"
                                max="100"
                            ></b-form-input>
                            <b-input-group-append is-text class="text-monospace">
                                {{ item.blur }}
                            </b-input-group-append>
                        </b-input-group>
                    </b-form-group>
                </b-col>
                <b-col md="4" class="mb-3">
                    <b-form-group :label="$t('Banner.brightness')" label-for="brightness">
                        <b-input-group>
                            <b-form-input
                                id="brightness"
                                v-model.number="item.brightness"
                                type="range"
                                number
                                min="-100"
                                max="100"
                            ></b-form-input>
                            <b-input-group-append is-text class="text-monospace">
                                {{ item.brightness }}
                            </b-input-group-append>
                        </b-input-group>
                    </b-form-group>
                </b-col>
                <b-col md="4" class="mb-3">
                    <label for="background">{{ $t('Banner.background') }}</label>
                    <div class="btn-group d-flex">
                        <b-form-input v-model="item.background" id="background" type="color"></b-form-input>
                        <b-button @click="removeBg(index)">
                            <fa :icon="['fas', 'trash-alt']"/>
                        </b-button>
                    </div>
                </b-col>
            </b-row>
            <b-row>
                <b-col cols="12" class="mb-3">
                    <label for="interval">{{ $t('Banner.interval') }}</label>
                    <b-form-input id="interval" v-model.number="item.interval" type="number" min="0"></b-form-input>
                </b-col>
                <b-col class="mb-2">
                    <b-form-checkbox
                        id="show_indicators"
                        v-model="item.indicators"
                        :value="1"
                        :unchecked-value="0"
                    >
                        {{ $t('Banner.show_indicators') }}
                    </b-form-checkbox>
                </b-col>
                <b-col class="mb-2">
                    <b-form-checkbox
                        id="show_nav"
                        v-model="item.nav"
                        :value="1"
                        :unchecked-value="0"
                    >
                        {{ $t('Banner.show_nav') }}
                    </b-form-checkbox>
                </b-col>
            </b-row>
            <b-row class="justify-content-between mt-4">
                <b-col>
                    <b-button variant="info" @click.prevent="addSize(index)" :title="$t('Banner.add_size')">
                        <fa :icon="['fas', 'plus']"/>
                    </b-button>
                </b-col>
                <b-col class="text-right">
                    <b-button variant="primary" :class="{'btn-loading': loading_setting}" :title="$t('Banner.save')"
                              :disabled="loading_setting"
                              @click="saveSettings(index)">
                        <fa :icon="['fas', 'save']"/>
                    </b-button>
                </b-col>
            </b-row>
            <div v-if="errorMessage">
                <div class="alert alert-danger mt-3">{{ errorMessage }}</div>
            </div>
        </b-tab>
    </b-tabs>
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
                    text: this.$t('Banner.resize_crop')
                },
                {
                    value: 'resize',
                    text: this.$t('Banner.resize')
                },
                {
                    value: 'crop',
                    text: this.$t('Banner.crop')
                }
            ],
            formats: [this.$t('Banner.default'), 'jpeg', 'jpg', 'png', 'gif', 'webp'],
            loading_setting: false,
            errorMessage: null
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
        addSize(index) {
            this.settings[index].sizes.push(this.emptySize())
        },
        deleteSize(index, key) {
            this.settings[index].sizes.splice(key, 1)
        },
        removeBg(index) {
            this.settings[index].background = null
        },
        calculateSizeWithRatio(index, size = null, field = null) {
            if (this.settings.hasOwnProperty(index)) {
                const rationW = this.settings[index].ratios.width;
                const rationH = this.settings[index].ratios.height;
                if (this.settings[index].ratio && rationW && rationH) {

                    if (size !== null && field) {
                        if (field === 'w') {
                            size.height = Math.round((parseInt(size.width) / rationW) * rationH);
                        } else if (field === 'h') {
                            size.width = Math.round(parseInt(size.height) * (rationW / rationH))
                        }
                    } else {
                        this.settings[index].sizes.forEach(item => {
                            if (item.width) {
                                item.height = Math.round((parseInt(item.width) / rationW) * rationH)
                            } else if (item.height) {
                                item.width = Math.round(parseInt(item.height) * (rationW / rationH))
                            }
                        })
                    }
                }
            }
        },
        saveSettings(position) {
            this.errorMessage = null;
            this.loading_setting = true;
            if (this.settings.hasOwnProperty(position)) {
                this.settings[position].position = position;
                axios.post(`${this.source}-settings`, this.settings[position]).then(() => {
                    this.$bvModal.hide('banner-settings')
                    this.$toast.global.success(this.$t('Banner.settings_saved'))

                    this.loading_setting = false;
                }).catch((error) => {
                    this.errorMessage = error.response.data.message
                    this.loading_setting = false;
                })
            } else {
                this.$bvModal.hide('banner-settings')
            }
        }
    }
}
</script>