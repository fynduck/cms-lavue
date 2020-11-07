<template>
    <div>
        <form @submit.prevent="onClickSubmit">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <input name="name" v-model="name" v-validate="'required'"
                           :class="{'form-control': true, 'is-invalid': errors.has('name') }" type="text"
                           :placeholder="trans.your_name + '*'" :data-vv-as="trans.your_name">
                    <div class="invalid-feedback" v-show="errors.has('name')">{{ errors.first('name') }}</div>
                </div>
                <div class="col form-group col-md-12">
                    <input name="phone" v-model="phone" v-validate="'required'"
                           :class="{'form-control': true, 'is-invalid': errors.has('phone') }" type="text"
                           :placeholder="trans.your_phone + '*'" :data-vv-as="trans.your_phone">
                    <div class="invalid-feedback" v-show="errors.has('phone')">{{ errors.first('phone') }}</div>
                </div>
                <div class="form-group col-md-12">
                    <input name="email" v-model="email" v-validate="'required|email'"
                           :class="{'form-control': true, 'is-invalid': errors.has('email') }" type="email"
                           :placeholder="trans.your_email + '*'" :data-vv-as="trans.your_email">
                    <div class="invalid-feedback" v-show="errors.has('email')">{{ errors.first('email') }}</div>
                </div>
                <div class="form-group col-md-12">
                    <select name="region" class="form-control">
                        <option value="">{{ trans.your_region }}</option>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    <textarea name="question_message" v-model="question_message" rows="3"
                          :class="{'form-control': true, 'is-invalid': errors.has('phone') }"
                          v-validate="'required'" :data-vv-as="trans.your_message"
                          :placeholder="trans.your_message + '*'"></textarea>
                    <div class="invalid-feedback" v-show="errors.has('email')">{{ errors.first('question_message') }}</div>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-custom py-2">
                        <span v-if="loading == false">{{ trans.send }}</span>
                    </button>
                </div>

            </div>

        </form>
        <modal v-if="showModalQ" @close="showModalQ = false, $root.oneClick = false">
            <div slot="body">{{ message }}</div>
        </modal>
    </div>
</template>

<script>
    import Modal from '../../../../resources/js/components/Modal';

    export default {
        name: "contact-form",
        components: {
            Modal
        },
        props: {
            source: {
                type: String,
                required: true
            },
            trans_source: {
                type: String,
                required: true
            },
            page: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                name: '',
                phone: '',
                email: '',
                question_message: '',
                color: '#5e699c',
                showModalQ: false,
                message: '',
                loading: false,
                trans: []
            }
        },
        mounted() {
            this.getTrans();
        },
        methods: {
            getTrans() {
                axios.get(this.trans_source).then((response) => {
                    this.trans = response.data
                }).catch(function (error) {
                    console.log(error);
                });
            },
            onClickSubmit() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.loading = true;
                        let data = {name: this.name, phone: this.phone, email: this.email, message: this.question_message, page_id: this.page_id};
                        axios.post(this.source, data).then((response) => {
                            this.message = response.data.message;
                            this.showModalQ = true;
                            this.loading = false;
                            this.name = '';
                            this.phone = '';
                            this.email = '';
                            this.question_message = '';

                            this.$nextTick().then(() => {
                                this.$validator.reset();
                                this.errors.clear();
                            });
                        }).catch(function (error) {
                            console.log(error);
                        });
                    }
                });
            },
        }
    }
</script>
