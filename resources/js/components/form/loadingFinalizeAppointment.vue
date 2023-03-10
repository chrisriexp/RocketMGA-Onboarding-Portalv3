<template>
    <div class="h-fit grid gap-8 justify-items-center px-6 md:px-0">
        <!--Favicon Image-->
        <img src="../../../assets/favicon.png" alt="Rocket Favicon" class="h-[53px] md:h-[64px] mt-12">

        <!--Loading Finalize Appointment Header-->
        <div class="grid h-fit text-center text-custom-dark-blue">
            <h2 class="text-[24px] md:text-[32px] font-semibold">Almost Done... Hold Tight</h2>
            <p class="text-[14px] md:text-[16px] opacity-70">Please wait while we finalize your appointment..</p>
        </div>

        <div class="my-8 mx-auto">
            <loading />
        </div>
    </div>
</template>

<script>
import loading from '../loading-red.vue'

export default {
    name: 'Finalizing Appointment',
    components: {
        loading
    }
}
</script>