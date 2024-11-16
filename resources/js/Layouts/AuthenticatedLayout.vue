<template>
    <div :class="isDarkMode ? 'dark' : ''">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 dark:text-white">
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-12 w-auto fill-current text-gray-800 dark:text-gray-200"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :active="route().current('dashboard')" :href="route('dashboard')">
                                    Dashboard
                                </NavLink>
                                <NavLink :active="route().current('goals.index') || route().current('goals.show')"
                                         :href="thisYearUrl">
                                    Goals
                                </NavLink>
                                <NavLink :active="route().current('this-week')" :href="thisWeekUrl">
                                    This week
                                </NavLink>
                                <NavLink :active="route().current('documents.index')" :href="route('documents.index')">
                                    Documents
                                </NavLink>
                                <!--                                <NavLink :href="route('tasks.thisMonth')" :active="route().current('tasks.thisMonth')">-->
                                <!--                                    This month-->
                                <!--                                </NavLink>-->
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Dark Mode Toggle -->
                            <button class="ms-4 px-3 py-2 rounded-md text-sm font-medium text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-100"
                                    @click="toggleDarkMode">
                                {{ isDarkMode ? 'Light Mode' : 'Dark Mode' }}
                            </button>

                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-100 focus:outline-none transition ease-in-out duration-150"
                                                type="button"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        clip-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        fill-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profile</DropdownLink>
                                        <DropdownLink :href="route('logout')" as="button" method="post">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-gray-300 transition duration-150 ease-in-out"
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                            >
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        d="M4 6h16M4 12h16M4 18h16"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        d="M6 18L18 6M6 6l12 12"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <!-- Mobile menu-->
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :active="route().current('dashboard')" :href="route('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :active="route().current('goals.index') || route().current('goals.show')"
                                           :href="thisYearUrl">
                            Goals
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :active="route().current('this-week')" :href="thisWeekUrl">
                            This week
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :active="route().current('documents.index')" :href="route('documents.index')">
                            Documents
                        </ResponsiveNavLink>
                    </div>
                    <!-- Mobile menu-->

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" as="button" method="post">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header"/>
                </div>
            </header>

            <!-- Page Content -->
            <main class="px-2 lg:px-0">
                <slot/>
            </main>
        </div>
    </div>
</template>

<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import { format, startOfWeek } from "date-fns";
import { computed, onMounted, ref } from "vue";

const showingNavigationDropdown = ref(false);
const isDarkMode = ref(false);

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  localStorage.setItem("darkMode", isDarkMode.value ? "true" : "false");
};

onMounted(() => {
  const savedDarkMode = localStorage.getItem("darkMode");
  if (savedDarkMode) {
    isDarkMode.value = savedDarkMode === "true";
  }
});

const thisWeekUrl = computed(() => {
  const currentDate = new Date();
  const startDate = startOfWeek(currentDate, { weekStartsOn: 1 }); // Week starts on Monday (1)

  // Format the date as yyyy-MM-dd
  const formattedStartDate = format(startDate, "yyyy-MM-dd");

  return route("this-week", { week_start: formattedStartDate });
});

const thisYearUrl = computed(() => {
  const currentYear = new Date().getFullYear(); // Get the current year
  return route("goals.index", { year: currentYear }); // Pass the year as a parameter
});
</script>
