<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Notification from '@/Components/NotificationAlert.vue';
defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route('logout'));
};
</script>
<style>
body{
  background-color:rgb(243 244 246);
}
</style>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <Notification />

      <!--Nav/Menu-->
      <nav class="afterLogin border-b border-gray-100">
        <!-- Desktop Navigation Menu -->
        <div class="flex justify-between">
          <Link :href="route('home')" class="dashbrdLogo"><ApplicationMark class="block h-8 w-auto" /></Link>
          <!-- Navigation Links -->
          <div class="hidden sm:flex sm:items-center sm:ms-6">
            <!--Supper Admin Nav-->
            <NavLink class="navLinks ms-3" v-if="$page.props.auth.user && $page.props.permissions.includes('View plan')" :href="route('plans.index')" :active="route().current().startsWith('plans')">Plans</NavLink>
            <NavLink class="navLinks ms-3" v-if="$page.props.auth.user && $page.props.permissions.includes('View product')" :href="route('products.index')" :active="route().current().startsWith('products')">Products</NavLink>
            <NavLink class="navLinks ms-3" v-if="$page.props.auth.user && $page.props.permissions.includes('View country')" :href="route('countries.index')" :active="route().current().startsWith('countries')">Countries</NavLink>
            <NavLink class="navLinks ms-3" v-if="$page.props.auth.user && $page.props.permissions.includes('View visatype')" :href="route('visa-types.index')" :active="route().current().startsWith('visa-types')">Visa Types</NavLink>
            <NavLink class="navLinks ms-3" v-if="$page.props.auth.user && $page.props.permissions.includes('View user')" :href="route('users.index')" :active="route().current().startsWith('users')">Users</NavLink>
            <NavLink class="navLinks ms-3" v-if="$page.props.auth.user && $page.props.permissions.includes('View page')" :href="route('pages.index')" :active="route().current().startsWith('pages')">Pages</NavLink>
            <NavLink class="navLinks ms-3" v-if="$page.props.auth.user && $page.props.permissions.includes('View inquiry')" :href="route('inquiries.index')" :active="route().current().startsWith('inquiries')">Inquiries</NavLink>
            <NavLink class="navLinks ms-3" v-if="$page.props.auth.user && $page.props.permissions.includes('View testimonial')" :href="route('testimonials.index')" :active="route().current().startsWith('testimonials')">Testimonials</NavLink>
            <NavLink class="navLinks ms-3 hidden" v-if="$page.props.auth.user && $page.props.permissions.includes('View role')" :href="route('roles.index')" :active="route().current().startsWith('roles')">
              Roles
            </NavLink>
            <NavLink class="navLinks ms-3 hidden" v-if="$page.props.auth.user && $page.props.permissions.includes('View email template')" :href="route('email-templates.index')" :active="route().current().startsWith('email-templates')">
              Email Templates
            </NavLink>
            <!-- Settings Dropdown -->
            <Dropdown align="right" width="48" class="ms-4">
              <template #trigger v-if="$page.props.auth.user">
                <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex align-items-center inline-flex items-center text-gray-900 border-transparent text-sm font-medium leading-5 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out text-uppercase block text-blue-700">

                            <span class="relative flex h-2 w-2 me-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-65"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>

                            {{ $page.props.auth.user.name }}
                            <svg class="ms-2 -me-0.5 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                          </button>
                <span v-else class="inline-flex rounded-md">
                  <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                    {{ $page.props.auth.user.name }}
                              <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                              </svg>
                            </button>
                          </span>
              </template>

                        <template #content>
                          <DropdownLink v-if="$page.props.auth.user && $page.props.permissions.includes('View dashboard')" :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                          </DropdownLink>
                          <DropdownLink :href="route('user-profile')">Profile</DropdownLink>

                          <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">API Tokens</DropdownLink>
                          <!-- Authentication -->
                          <form @submit.prevent="logout">
                            <DropdownLink as="button">Log Out</DropdownLink>
                          </form>
                        </template>
            </Dropdown>
          </div>
          <!-- Hamburger -->
          <div class="-me-2 flex items-center sm:hidden">
            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
            </button>
          </div>
        </div>
        <!-- Responsive Navigation Menu -->
              <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden mobNav">

                <div class="flex items-center userInfo" v-if="$page.props.auth.user">
                  <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                    <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                  </div>
                  <div>
                    <div class="font-medium text-base text-gray-300">{{ $page.props.auth.user.name }}</div>
                    <div class="font-medium text-sm text-gray-100">{{ $page.props.auth.user.email }}</div>
                  </div>
                </div>

                <div class="pt-1 pb-3 space-y-1">
                  <ResponsiveNavLink v-if="$page.props.auth.user && $page.props.permissions.includes('View dashboard')" :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</ResponsiveNavLink>

                  <ResponsiveNavLink v-if="$page.props.auth.user && $page.props.permissions.includes('View plan')" :href="route('plans.index')" :active="route().current().startsWith('plans')">
                    Plans
                  </ResponsiveNavLink>
                  <ResponsiveNavLink v-if="$page.props.auth.user && $page.props.permissions.includes('View product')" :href="route('products.index')" :active="route().current().startsWith('products')">
                    Products
                  </ResponsiveNavLink>
                  <ResponsiveNavLink v-if="$page.props.auth.user && $page.props.permissions.includes('View country')" :href="route('countries.index')" :active="route().current().startsWith('countries')">
                    Countries
                  </ResponsiveNavLink>
                  <ResponsiveNavLink v-if="$page.props.auth.user && $page.props.permissions.includes('View visatype')" :href="route('visa-types.index')" :active="route().current().startsWith('visa-types')">
                    Visa types
                  </ResponsiveNavLink>
                  
                  <ResponsiveNavLink v-if="$page.props.auth.user && $page.props.permissions.includes('View user')" :href="route('users.index')" :active="route().current().startsWith('users')">
                    Users
                  </ResponsiveNavLink>
                  <ResponsiveNavLink v-if="$page.props.auth.user && $page.props.permissions.includes('View page')" :href="route('pages.index')" :active="route().current().startsWith('pages')">
                    Pages
                  </ResponsiveNavLink>
                  <ResponsiveNavLink v-if="$page.props.auth.user && $page.props.permissions.includes('View inquiry')" :href="route('inquiries.index')" :active="route().current().startsWith('inquiries')">
                    Inquiries
                  </ResponsiveNavLink>
                  <ResponsiveNavLink v-if="$page.props.auth.user && $page.props.permissions.includes('View testimonial')" :href="route('testimonials.index')" :active="route().current().startsWith('testimonials')">
                    Testimonials
                  </ResponsiveNavLink>
                  <ResponsiveNavLink hidden v-if="$page.props.auth.user && $page.props.permissions.includes('View role')" :href="route('roles.index')" :active="route().current().startsWith('roles')">
                    Roles
                  </ResponsiveNavLink>

                  <ResponsiveNavLink hidden v-if="$page.props.auth.user && $page.props.permissions.includes('View email template')" :href="route('email-templates.index')" :active="route().current().startsWith('email-templates')">
                    Email Templates
                  </ResponsiveNavLink>

                  <ResponsiveNavLink :href="route('profile.show')" :active="route().current().startsWith('profile')">Profile</ResponsiveNavLink>

                  <!-- Authentication -->
                  <form method="POST" @submit.prevent="logout">
                    <ResponsiveNavLink as="button">Log Out</ResponsiveNavLink>
                  </form>
                </div>

                </div>
      </nav>

      <!-- Page Heading -->
      <header v-if="$slots.header" class="flex flex-wrap md:items-center max-w-6xl mx-auto sm:px-6 lg:px-8 px-4 pt-12 lg:pt-28">
        <slot name="header" />
      </header>

      <!-- Page Content -->
      <main>
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
