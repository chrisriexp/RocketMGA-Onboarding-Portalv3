<template>
    <div class="grid">
        <label class="text-md text-custom-dark-blue">{{ label }}</label>
        <input
            v-if="!password"
            v-model="v$.inputData.$model"
            @change="change($event)"
            type='text'
            :disabled="isdisabled"
            :id="id"
            :placeholder="placeholderText"
            :class="v$.inputData.$error ? 'border-custom-red focus:border-custom-red' : 'border-custom-gray border-opacity-20 focus:border-custom-blue'"
            class="w-[300px] p-2 rounded-md border-2 focus:ring-0"
        >
        <input
            v-else
            v-model="v$.inputData.$model"
            @change="change($event)"
            type='password'
            :id="id"
            :placeholder="placeholderText"
            :class="v$.inputData.$error ? 'border-custom-red focus:border-custom-red' : 'border-custom-gray border-opacity-20 focus:border-custom-blue'"
            class="w-[300px] p-2 rounded-md border-2 focus:ring-0"
        >
        <div class="input-errors" v-for="(error, index) of v$.inputData.$errors" :key="index">
            <div class="error-msg text-sm text-red-400">{{ error.$message }}</div>
        </div>
    </div>
</template>

<script>
import { useVuelidate } from '@vuelidate/core'
import { required, email, minLength, maxLength, helpers} from '@vuelidate/validators'

export default {
    setup () {
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
    },
    data() {
        return {
            inputData: '',
            valid: {}
        }
    },
    props: {
        id: String,
        label: String,
        placeholderText: String,
        isdisabled: {
            type: Boolean,
            default: false
        },
        password: {
            type: Boolean,
            default: false
        },
        required: {
            type: Boolean,
            default: true
        },
        email: {
            type: Boolean,
            default: false
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
    validations () {
        return {
            inputData: this.valid,
        }
    },
}
</script>