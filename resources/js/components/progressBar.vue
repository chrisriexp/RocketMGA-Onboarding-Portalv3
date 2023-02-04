<template>
    <div class="">
        <p class="text-center font-light mb-2"><span class="text-custom-blue">{{ width }}</span> Completed</p>
        <div class="w-full">
            <div class="w-full bg-custom-light-blue h-4 mb-2 rounded-full">
                <div class="bg-custom-blue h-4 rounded-full" :style="{'width': width}"></div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'Progress Bar',
    props: {
        width: String
    }
}
</script>