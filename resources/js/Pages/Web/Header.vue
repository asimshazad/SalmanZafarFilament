<script setup>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import NavLink from '@/Components/NavLink.vue';

</script>
<template>
       <header id="main_header" class="innerHeader fixed w-100 top-0 lg:px-10 px-6 backdrop-blur-sm dark:bg-gray-900/70 dark:border-gray-700 z-30">
      <nav class="w-100">
        <!-- Primary Navigation Menu -->
        <div class="grid grid-cols-3 lg:items-start items-center gap-2 align-items-center">

          <!-- Navigation Links -->
          <div class="space-x-8 lg:flex flex-1 justify-start">
            <NavLink hidden :href="route('home')" :active="route().current('renters')">Home</NavLink>
          </div>

          <!-- Logo -->
          <div class="flex justify-center col-start-2 items-center">
            <Link :href="route('home')"><ApplicationMark class="block h-8 w-auto" /></Link>
          </div>

          <!-- Navigation Links -->
          <div class="space-x-8 flex flex-1 justify-end">
            <NavLink v-if="$page.props.auth.user" :href="route('view-cart')" :active="route().current('view-cart')">Cart</NavLink>
          </div>

        </div>
      </nav>
    </header>
</template>