<template>
    <div class="">
        <p class="text-center font-light mb-2"><span class="text-custom-blue">{{ widthStr }}</span> Completed</p>
        <div class="w-full">
            <div class="w-full bg-custom-light-blue h-4 mb-2 rounded-full">
                <div class="bg-custom-blue h-4 rounded-full" :style="{'width': widthStr}"></div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'Progress Bar',
    data() {
        return {
            widthStr: ''
        }
    },
    created() {
        this.widthStr = this.width+'%'
    },
    props: {
        width: String
    }
}
</script>