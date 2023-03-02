<template>
    <div class="h-fit grid gap-8 justify-items-center mb-8 md:mb-0 px-6 md:px-0">
        <!--Favicon Image-->
        <img src="../../../assets/favicon.png" alt="Rocket Favicon" class="h-[53px] md:h-[64px] mt-12">

        <!--Submitted Header-->
        <div class="grid h-fit text-center text-custom-dark-blue">
            <h2 class="text-[16px] md:text-[20px] font-semibold">Thank You for Signing Up with RocketMGA</h2>
        </div>

        <img src="../../../assets/submitted_image.png" alt="Submitted Image">

        <p class="w-full md:w-[502px] text-[16px] md:text-[22px] text-custom-blue text-center font-semibold">We are processing your appointment and will be in touch shortly.</p>
        <p class="w-full md:w-[502px] text-[14px] md:text-[16px] text-custom-gray text-center">For assistance please contact <a href="mailto:onboarding@rocketmga.com" class="text-custom-blue">onboarding@rocketmga.com</a></p>
    </div>
</template>

<script>

export default {
    name: "Congrats"
}
</script>