<script setup>
import { Link } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { ref } from 'vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const isMenuOpen = ref(false);
const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};
</script>

<template>
    <header class="bg-white shadow">
        <div
            class="container mx-auto flex items-center justify-between px-4 py-6">
            <div class="flex shrink-0 items-center">
                <Link
                    :href="route('blog.index')"
                    class="flex items-center space-x-1">
                    <ApplicationLogo />
                    <h1 class="text-xl font-bold">{{ $page.props.appName }}</h1>
                </Link>
            </div>

            <nav>
                <div class="hidden space-x-6 md:flex">
                    <NavLink
                        :href="route('blog.index')"
                        :active="route().current('blog.index')">
                        Home
                    </NavLink>
                    <NavLink
                        :href="route('blog.about')"
                        :active="route().current('blog.about')">
                        About Me
                    </NavLink>
                    <NavLink
                        :href="route('blog.my-posts')"
                        :active="route().current('blog.my-posts')"
                        v-if="$page.props.auth.user">
                        My Posts
                    </NavLink>
                    <NavLink
                        :href="route('login')"
                        :active="route().current('login')"
                        v-if="!$page.props.auth.user">
                        Login
                    </NavLink>
                    <NavLink
                        :href="route('register')"
                        :active="route().current('register')"
                        v-if="!$page.props.auth.user">
                        Register
                    </NavLink>

                    <div
                        class="hidden sm:ms-6 sm:flex sm:items-center"
                        v-if="$page.props.auth.user">
                        <!-- Settings Dropdown -->
                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                            {{ $page.props.auth.user.name }}

                                            <svg
                                                class="-me-0.5 ms-2 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post">
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
                <div class="md:hidden">
                    <button
                        @click="toggleMenu"
                        class="text-gray-700 focus:outline-none">
                        <svg
                            class="h-6 w-6"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24">
                            <path
                                :class="{
                                    hidden: isMenuOpen,
                                    'inline-flex': !isMenuOpen,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path
                                :class="{
                                    hidden: !isMenuOpen,
                                    'inline-flex': isMenuOpen,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </nav>
        </div>
        <div v-if="isMenuOpen" class="md:hidden">
            <div class="flex flex-col space-y-4 p-4">
                <ResponsiveNavLink
                    :href="route('blog.index')"
                    :active="route().current('blog.index')">
                    Home
                </ResponsiveNavLink>
                <ResponsiveNavLink
                    :href="route('blog.about')"
                    :active="route().current('blog.about')">
                    About Me
                </ResponsiveNavLink>
                <ResponsiveNavLink
                    :href="route('blog.my-posts')"
                    :active="route().current('blog.my-posts')"
                    v-if="$page.props.auth.user">
                    My Posts
                </ResponsiveNavLink>
                <ResponsiveNavLink
                    :href="route('login')"
                    :active="route().current('login')"
                    v-if="!$page.props.auth.user">
                    Login
                </ResponsiveNavLink>
                <ResponsiveNavLink
                    :href="route('register')"
                    :active="route().current('register')"
                    v-if="!$page.props.auth.user">
                    Register
                </ResponsiveNavLink>
            </div>
            <!-- Responsive Settings Options -->
            <div
                class="border-t border-gray-200 pb-1 pt-4"
                v-if="$page.props.auth.user">
                <div class="px-4">
                    <div class="text-base font-medium text-gray-800">
                        {{ $page.props.auth.user.name }}
                    </div>
                    <div class="text-sm font-medium text-gray-500">
                        {{ $page.props.auth.user.email }}
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <ResponsiveNavLink :href="route('profile.edit')">
                        Profile
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('logout')"
                        method="post"
                        as="button">
                        Log Out
                    </ResponsiveNavLink>
                </div>
            </div>
        </div>
    </header>
</template>
