<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Notification from '@/Components/NotificationAlert.vue';
import Header from '../Pages/Web/Header.vue';
defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

onMounted( () => {
  var header = document.getElementById("main_header");
function toggleHeaderClass() {
    if (window.scrollY > 100) {
        header.classList.add("sticky_Header");
    } else {
        header.classList.remove("sticky_Header");
    }
}

window.addEventListener("scroll", toggleHeaderClass);
})

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
  <div>
    <Head :title="title" />
    <Notification />

    <Header></Header>

    <main class="FrontAppLayouts">
      <slot />
    </main>
  </div>
</template>
<style>

/* Common scroll bar */
.common-scroll-bar{
    overflow: hidden;
}
.common-scroll-bar:hover{
    overflow-y: scroll;
}
.common-scroll-bar::-webkit-scrollbar-track
{
    border-radius: 10px;
    background-color: #ffffff;
}
.common-scroll-bar::-webkit-scrollbar
{
    width: 2px;
    background-color: #ffffff;
}
.common-scroll-bar::-webkit-scrollbar-thumb
{
    border-radius: 10px;
    background-color: #929292;
}
.common-scroll-bar::-webkit-scrollbar-track:hover{
    background-color: #AEB6FF;
}
</style>
