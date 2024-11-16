<template>
    <Dialog>
        <DialogTrigger as-child>
            <img
                :alt="alt || ''"
                :class="className"
                :src="src"
                class="w-full h-auto cursor-pointer"
            />
        </DialogTrigger>
        <DialogContent class="max-w-7xl border-0 bg-transparent p-0">
            <div class="relative h-[calc(100vh-220px)] w-full overflow-hidden rounded-md bg-transparent shadow-md">
                <img :alt="alt || ''" :src="src" class="h-full w-full object-contain"/>
            </div>
        </DialogContent>
    </Dialog>
</template>

<script>
import { Dialog, DialogContent, DialogHeader, DialogTrigger } from "@/shadcn/ui/dialog/index.js";

export default {
  props: {
    src: {
      type: String,
      required: true,
    },
    alt: {
      type: String,
      default: "",
    },
    className: {
      type: String,
      default: "",
    },
  },
  components: {
    DialogHeader,
    Dialog,
    DialogContent,
    DialogTrigger,
  },
};
</script>

<style scoped>
/* Add any scoped styles if necessary */
</style>
