<script setup>
import ImageViewer from "@/Components/ImageViewer.vue";
import { Button } from "@/shadcn/ui/button/index.js";
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from "@/shadcn/ui/carousel/index.js";
import { Head } from "@inertiajs/vue3";

defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
});
</script>

<template>
    <Head title="GoalTracker.co - Track Your Goals, Build Habits"/>
    <header class="w-full z-10">
        <nav class="bg-white border-gray-200 py-2.5 dark:bg-gray-900">
            <div class="flex flex-wrap items-center justify-between max-w-screen-xl px-4 mx-auto">
                <a class="flex items-center" href="#">
                    <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">GoalTracker.co</span>
                </a>
                <div class="flex items-center lg:order-2">
                    <a :href="route('login')">
                        <Button
                            class="text-white focus:ring-4 font-medium rounded-lg text-sm px-4 lg:px-5 py-2 lg:py-2.5 sm:mr-2 lg:mr-0 focus:outline-none"
                            target="_blank">Login
                        </Button>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <section class="bg-white dark:bg-gray-900">
        <div class="grid max-w-screen-xl px-4 pt-20 pb-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12 lg:pt-32">
            <div class="mr-auto place-self-center lg:col-span-7">
                <h1 class="max-w-2xl mb-4 text-4xl font-extrabold leading-none tracking-tight md:text-5xl xl:text-6xl dark:text-white">
                    Achieve Your Goals with GoalTracker
                </h1>
                <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">
                    Stay on top of your goals, track habits, and organize your week. Whether you're building new habits or planning your
                    next project, GoalTracker helps you stay focused.
                </p>
                <div class="space-y-4 sm:flex sm:space-y-0 sm:space-x-4">
                    <a :href="route('register')" target="_blank">
                    <Button
                        class="inline-flex items-center justify-center w-full px-5 py-3 text-sm font-medium text-center text-white rounded-lg sm:w-auto focus:ring-4"
                    >
                        Register
                    </Button>
                    </a>
                </div>
            </div>
            <div class="hidden lg:mt-0 lg:col-span-5 lg:flex">
                <ImageViewer
                    alt="goal tracking illustration"
                    className="rounded-md shadow-lg"
                    src="images/screen_2.png"
                />
            </div>
        </div>
    </section>

    <section class="bg-gray-50 dark:bg-gray-800">
        <div class="max-w-screen-xl px-4 py-8 mx-auto space-y-12 lg:space-y-20 lg:py-24 lg:px-6">
            <div class="items-center gap-8 lg:grid lg:grid-cols-2 xl:gap-16">
                <div class="text-gray-500 sm:text-lg dark:text-gray-400">
                    <h2 class="mb-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">Track Progress, Build Habits</h2>
                    <p class="mb-8 font-light lg:text-xl">Our tools allow you to set achievable goals, monitor your progress, and build
                        lasting habits that lead to success.</p>
                    <ul class="pt-8 space-y-5 border-t border-gray-200 my-7 dark:border-gray-700" role="list">
                        <li class="flex space-x-3">
                            <svg class="flex-shrink-0 w-5 h-5 text-black-500 dark:text-black-400" fill="currentColor" viewBox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      fill-rule="evenodd"></path>
                            </svg>
                            <span class="text-base font-medium leading-tight text-gray-900 dark:text-white">Goal Setting</span>
                        </li>
                        <li class="flex space-x-3">
                            <svg class="flex-shrink-0 w-5 h-5 text-black-500 dark:text-black-400" fill="currentColor" viewBox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      fill-rule="evenodd"></path>
                            </svg>
                            <span class="text-base font-medium leading-tight text-gray-900 dark:text-white">Habit Tracker</span>
                        </li>
                        <li class="flex space-x-3">
                            <svg class="flex-shrink-0 w-5 h-5 text-black-500 dark:text-black-400" fill="currentColor" viewBox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      fill-rule="evenodd"></path>
                            </svg>
                            <span class="text-base font-medium leading-tight text-gray-900 dark:text-white">Weekly Planner</span>
                        </li>
                        <li class="flex space-x-3">
                            <svg class="flex-shrink-0 w-5 h-5 text-black-500 dark:text-black-400" fill="currentColor" viewBox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      fill-rule="evenodd"></path>
                            </svg>
                            <span class="text-base font-medium leading-tight text-gray-900 dark:text-white">Documents</span>
                        </li>
                    </ul>
                </div>

                <Carousel>
                    <CarouselContent>
                        <CarouselItem>
                            <ImageViewer
                                alt="goal tracking illustration 3"
                                className="rounded-md shadow-lg"
                                src="images/screen_3.png"
                            />
                        </CarouselItem>
                        <CarouselItem>
                            <ImageViewer
                                alt="goal tracking illustration 4"
                                className="rounded-md shadow-lg"
                                src="images/screen_4.png"
                            />
                        </CarouselItem>
                    </CarouselContent>
                    <CarouselPrevious/>
                    <CarouselNext/>
                </Carousel>
            </div>
        </div>
    </section>

    <footer class="bg-white dark:bg-gray-800">
        <div class="max-w-screen-xl p-4 py-6 mx-auto lg:py-16 md:p-8 lg:p-10">
            <div class="text-right">
                <h3 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Legal</h3>
                <ul class="text-gray-500 dark:text-gray-400">
                    <li class="mb-4">
                        <a class="hover:underline" href="/privacy-policy">Privacy Policy</a>
                    </li>
                </ul>
            </div>
            <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8">
            <div class="text-center">
                <a class="flex items-center justify-center mb-5 text-2xl font-semibold text-gray-900 dark:text-white"
                   href="#">
                    GoalTracker
                </a>
                <span class="block text-sm text-center text-gray-500 dark:text-gray-400">© 2024 GoalTracker™. All Rights Reserved.</span>
            </div>
        </div>
    </footer>
</template>
