<template>
    <div class="grid gap-2 h-fit">
        <input
            v-if="date"
            v-model="v$.inputData.$model"
            @change="change($event)"
            type='date'
            :disabled="isdisabled"
            :id="id"
            :class="v$.inputData.$error ? 'inner-border-custom-red focus:inner-border-custom-red' : ''"
            class="w-full p-[5px] rounded-xl inner-border-[1px] border-none focus:ring-0 disabled:bg-custom-gray disabled:bg-opacity-5"
        >
        <input
            v-else-if="password"
            v-model="v$.inputData.$model"
            @change="change($event)"
            type='password'
            :id="id"
            :placeholder="placeholderText"
            :class="v$.inputData.$error ? 'inner-border-custom-red focus:inner-border-custom-red' : ''"
            class="w-full p-[5px] rounded-xl inner-border-[1px] border-none focus:ring-0 disabled:bg-custom-gray disabled:bg-opacity-5"
        >
        <input
            v-else
            v-model="v$.inputData.$model"
            @change="change($event)"
            type='text'
            :disabled="isdisabled"
            :id="id"
            :placeholder="placeholderText"
            :class="v$.inputData.$error ? 'inner-border-custom-red focus:inner-border-custom-red' : ''"
            class="w-full p-[5px] rounded-xl inner-border-[1px] border-none focus:ring-0 disabled:bg-custom-gray disabled:bg-opacity-5"
        >
    </div>
</template>

<script>
import { useVuelidate } from '@vuelidate/core'
import { required, email, helpers} from '@vuelidate/validators'

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
        index: String,
        inputValue: {
            type: String,
            default: ''
        },
        isdisabled: {
            type: Boolean,
            default: false
        },
        date: {
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

            this.$emit('inputUpdate', e.target.id, this.inputData, errors, this.index)
        }
    },
    validations () {
        return {
            inputData: this.valid,
        }
    },
}
</script>