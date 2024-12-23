<template>
  <footer class="w-full lg:pt-2 pt-5 relative flex flex-col items-center justify-center">
    <img :src="$page.props.setting.site_footer_logo_url">
    <div class="footer_copyright relative w-full px-6 bg-black/5 text-center uppercase py-6">
      <span>Copyright © {{ currentYear }}</span> - <a class="font-semibold" href="javascript:void(0)"> {{ $page.props.setting.site_title }}</a> - ALL RIGHTS RESERVED.
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue';

const currentYear = ref(new Date().getFullYear());
</script>