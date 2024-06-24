<script setup>
import Footer from '@/Components/Footer.vue';
import Header from '@/Components/Header.vue';
import Banner from '@/Components/Banner.vue';
</script>

<template>
    <Banner />

    <div class="bg-white">
        <Header />
        <main>
            <slot />
        </main>
        <Footer />
    </div>
</template>
