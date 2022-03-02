<template>
    <b-overlay :show="busy" no-wrap fixed>
        <template v-slot:overlay>
            <p><strong>{{ text }}</strong></p>
            <div class="d-flex justify-content-center">
                <b-button variant="outline-danger" class="me-3" @click="handleSelect('cancel')">
                    {{ cancel }}
                </b-button>
                <b-button variant="outline-success" @click="handleSelect('yes')">{{ yes }}</b-button>
            </div>
        </template>
    </b-overlay>
</template>

<script>
    export default {
        name: "Confirm",
        props: {
            value: {
                default: ''
            },
            show: {
                type: Boolean,
                default: false
            },
            text: {
                type: String,
                default: 'Are you sure'
            },
            cancel: {
                type: String,
                default: 'Cancel'
            },
            yes: {
                type: String,
                default: 'Yes'
            }
        },
        data() {
            return {
                busy: true
            }
        },
        methods: {
            handleSelect(action = 'cancel') {
                this.busy = false;
                if (action === 'cancel')
                    this.$emit('input', false)
                else
                    this.$emit('input', this.value)
            }
        }
    }
</script>