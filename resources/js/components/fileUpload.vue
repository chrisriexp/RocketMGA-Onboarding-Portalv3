<template>
    <div class="grid h-fit">
        <label class="text-[16px] text-custom-dark-blue font-medium">{{ label }} <span v-if="required" class="text-custom-red">*</span></label>
        <input @change="uploadFile($event)" ref="logoFileUploader" type="file" accept=".png, .jpg, .jpeg, .pdf" name="fileUpload" id="fileUpload" class="hidden">
        <div v-on:click="$refs.logoFileUploader.click()" class="w-full flow-root align-items-center mt-2 p-2 rounded-md border-2">
            <p class="float-left text-[13px] text-custom-gray opacity-20 font-medium">Upload {{ label }}</p>
            <img src="../../assets/upload_icon.png" alt="Upload Icon" class="float-right my-auto">
        </div>
        <p v-if="!file" class="text-custom-red text-[12px]">Pending</p>
        <p v-else class="text-[#58BB3F] text-[12px]">Success!</p>
    </div>
</template>

<script>

export default {
    name: "File Upload",
    props: {
        label: String,
        inputValue: {
            type: String,
            default: ''
        },
        type: {
            type: String,
            default: 'file'
        },
        required: {
            type: Boolean,
            default: true
        },
    },
    data() {
        return {
            file: '',
            form: {}
        }
    },
    created() {
        this.file = this.inputValue

        if(this.type == 'agency_license_file'){
            this.form = {
                agency_license_file: ''
            }
        } else if(this.type == 'agent_license_file'){
            this.form = {
                agent_license_file: ''
            }
        } else if(this.type == 'agency_logo'){
            this.form = {
                agency_logo: ''
            }
        } else if(this.type == 'eo'){
            this.form = {
                eo: ''
            }
        }
    },
    methods: {
        async uploadFile (e) {
            const file = e.target.files[0];
            if (!file) {
                return
            }

            this.file = file
            

            //Upload Agency Logo
            if(typeof this.file === 'object'){
                //Upload file
                const fileHeader = {
                    headers: {'content-type': 'multipart/form-data'}
                }

                let fileData = new FormData();
                fileData.append('file', this.file);
                fileData.append('type', this.type);
                
                await axios.post('/api/upload', fileData, fileHeader)
                .then(response => {
                    this.form[this.type] = response.data['id']
                    this.$emit('fileUploaded', this.type, this.form[this.type])
                })
                .catch(error => {
                    console.log(error.response)
                    const keys = Object.keys(error.response.data.message)

                    keys.forEach(key => {
                        error.response.data.message[key].forEach(error => {
                            this.$alert({
                                title: 'Validation Error',
                                text: error,
                                type: 'warn'
                            })
                        })
                    })
                })

                //Add file ID to database
                await axios.post('/api/onboarding/update', {
                    'data': this.form
                })
                .then(response => {
                    
                })
                .catch(error => {
                    console.log(error)
                    const keys = Object.keys(error.response.data.message)

                    keys.forEach(key => {
                        error.response.data.message[key].forEach(error => {
                            this.$alert({
                                title: 'Validation Error',
                                text: error,
                                type: 'warn'
                            })
                        })
                    })
                })
                return
            }
        }
    }
}
</script>