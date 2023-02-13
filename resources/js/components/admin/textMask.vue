<template>
    <div class="grid gap-2 h-fit">
        <input
            v-model="v$.inputData.$model"
            @change="change($event)"
            type='text'
            :id="id"
            v-mask="[maskText]"
            :disabled="isdisabled"
            :placeholder="placeholderText"
            :class="v$.inputData.$error ? 'inner-border-custom-red focus:inner-border-custom-red' : ''"
            class="w-full p-[5px] rounded-xl inner-border-[1px] border-none focus:ring-0 disabled:bg-custom-gray disabled:bg-opacity-5"
        >
    </div>
</template>

<script>
import { useVuelidate } from '@vuelidate/core'
import { required, email, helpers} from '@vuelidate/validators'
import {mask} from 'vue-the-mask'

export default {
    setup() {
        return {
            v$: useVuelidate()
        }
    },
    created() {
        if(this.required){
            let validRules = {}

            if(this.required){
                validRules = {
                    required: helpers.withMessage(`${this.label} is required`, required)
                }

                if(this.email){
                    validRules = {
                        required: helpers.withMessage(`${this.label} is required`, required),
                        email: helpers.withMessage(`Please enter a valid email.`, email)
                    }
                }
            }

            this.valid = validRules
        }

        this.inputData = this.inputValue
    },
    data() {
        return {
            inputData: '',
            valid: {}
        }
    },
    props: {
        id: String,
        placeholderText: String,
        maskText: String,
        inputValue: {
            type: String,
            default: ''
        },
        isdisabled: {
            type: Boolean,
            default: false
        },
        required: {
            type: Boolean,
            default: true
        }
    },
    watch: {
        inputValue(newValue, oldValue) {
            this.inputData = newValue
        }
    },
    methods: {
        change(e){
            let errors = []

            this.v$.inputData.$errors.forEach(error => {
                errors.push(error.$message)
            })

            this.$emit('inputUpdate', e.target.id, this.inputData, errors)
        }
    },
    directives: {mask},
    validations () {
        return {
            inputData: this.valid,
        }
    },
}
</script>