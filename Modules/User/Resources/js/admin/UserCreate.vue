<template>
    <div>
        <p class="title_form">
            {{ routeEdit ? $t('User.edit_user') : $t('User.add_user') }}
        </p>
        <form @submit.prevent="onSubmit" v-if="!loading">
            <router-link class="btn btn-light submit_absolute cancel" :to="{name: 'user.index'}" :title="$t('User.cancel')">
                <fa :icon="['fas', 'reply']"/>
            </router-link>
            <button :class="{'btn btn-primary submit_absolute': true, 'btn-loading': submit}" type="submit" :title="$t('User.save')"
                    :disabled="submit">
                <fa :icon="['fas', 'save']"/>
            </button>
            <div class="row">
                <div class="form-group col-md-6 col-lg-4">
                    <label for="username">{{ $t('User.username') }}</label>
                    <input type="text" :class="['form-control', errors['username'] ? ' is-invalid' : '']"
                           v-model="form.username"
                           id="username">
                    <div class="invalid-feedback" v-if="errors['username']">
                        <strong v-for="error in errors['username']">{{ error }}</strong>
                    </div>
                </div>
                <div class="form-group col-md-6 col-lg-4">
                    <label for="name">{{ $t('User.name') }}</label>
                    <input type="text" :class="['form-control', errors['name'] ? ' is-invalid' : '']"
                           v-model="form.name"
                           id="name">
                </div>
                <div class="form-group col-md-6 col-lg-4">
                    <label for="email">{{ $t('User.email') }}</label>
                    <input type="email" :class="['form-control', errors['email'] ? ' is-invalid' : '']"
                           v-model="form.email"
                           id="email">
                    <div class="invalid-feedback" v-if="errors['email']">
                        <strong v-for="error in errors['email']">{{ error }}</strong>
                    </div>
                </div>
                <div class="form-group col-md-6 col-lg-4">
                    <label for="birthday">{{ $t('User.birthday') }}</label>
                    <input type="date" :class="['form-control', errors['birthday'] ? ' is-invalid' : '']"
                           v-model="form.birthday"
                           id="birthday">
                    <div class="invalid-feedback" v-if="errors['birthday']">
                        <strong v-for="error in errors['birthday']">{{ error }}</strong>
                    </div>
                </div>
                <div class="form-group col-md-6 col-lg-4">
                    <label for="phone">{{ $t('User.phone') }}</label>
                    <input type="text" :class="['form-control', errors['phone'] ? ' is-invalid' : '']"
                           v-model="form.phone"
                           id="phone">
                </div>
                <div class="form-group col-md-6 col-lg-4">
                    <label for="group">{{ $t('User.group') }}</label>
                    <select v-model="form.group_id" id="group"
                            :class="['form-control', errors['group_id'] ? ' is-invalid' : '']">
                        <option :value="group_id" v-for="(group, group_id) in groups">{{ group }}</option>
                    </select>
                </div>
                <div class="form-group col-md-6 col-lg-4">
                    <label for="password">{{ $t('User.password') }}</label>
                    <input type="text" :class="['form-control', errors['password'] ? ' is-invalid' : '']"
                           v-model="form.password"
                           id="password">
                </div>
            </div>
            <p class="text-right">
                <router-link class="btn btn-light" :to="{name: 'user.index'}" :title="$t('User.cancel')">
                    <fa :icon="['fas', 'reply']"/>
                </router-link>
                <button :class="{'btn btn-primary': true, 'btn-loading': submit}" type="submit" :title="$t('User.save')"
                        :disabled="submit">
                    <fa :icon="['fas', 'save']"/>
                </button>
            </p>
        </form>
    </div>
</template>

<script>
import axios from 'axios'
import {mapGetters} from 'vuex'

export default {
    middleware: 'auth',
    head() {
        const title = this.routeEdit ? this.$t('User.edit_user') : this.$t('User.add_user');
        return {title}
    },
    data() {
        return {
            form: {},
            groups: {},
            errors: {},
            loading: true,
            submit: false
        }
    },
    computed: {
        ...mapGetters({
            locale: 'lang/locale',
            locales: 'lang/locales',
            token: 'auth/token',
        }),
        routeEdit() {
            return typeof this.$route.params.id !== "undefined";
        },
        sourceActionMethod() {
            const arrayRoute = this.$route.name.split('.');

            let action = `/admin/${arrayRoute[0]}`;
            let method = 'post'

            if (this.routeEdit) {
                action += `/${this.$route.params.id}`
                method = 'put'
            }

            return {
                'action': action,
                'method': method
            };
        },
        source() {
            const arrayRoute = this.$route.name.split('.');
            let action = `/admin/${arrayRoute[0]}`;
            if (typeof this.$route.params.id !== "undefined")
                return `${action}/${this.$route.params.id}`;

            return `${action}/0`;
        }
    },
    mounted() {
        this.getItem()
    },
    methods: {
        getItem() {
            axios.get(this.source).then(response => {
                this.form = response.data.data;
                this.groups = response.data.groups;
                this.loading = false;
            }).catch(error => {
                console.log(error)
            })
        },
        onSubmit() {
            this.submit = true;
            axios({
                method: this.sourceActionMethod.method,
                url: this.sourceActionMethod.action,
                data: this.form
            }).then(() => {
                this.$toast.global.success(this.$t('User.data_save'))

                setTimeout(() => {
                    this.$router.push({
                        name: `${this.$route.name.split('.')[0]}.index`
                    })
                }, 1000)
            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors
                    this.submit = false;
                }
            })
        }
    }
}
</script>
