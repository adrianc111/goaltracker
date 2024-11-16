<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
<!--        <template #header>-->
<!--            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>-->
<!--        </template>-->

        <div class="pt-2 pb-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Left Column -->
                    <div class="space-y-6 mt-6">
                        <!-- Today Card -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex justify-between">
                                    <span>
                                        {{ today.title }}
                                        <Badge class="text-xs" variant="secondary">{{ incompleteTasksToday || 0 }}</Badge>
                                    </span>
                                </CardTitle>
                                <CardDescription>{{ today.date }}</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <todo-list
                                    :items="today.tasks"
                                    @item-added="onTaskAdd"
                                    @item-checked="onTaskCheck"
                                    @item-removed="onTaskRemove"
                                    @date-changed="onTaskUpdate"
                                    @order-updated="onOrderUpdate"
                                    @title-changed="onTaskUpdate"
                                />
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader class="cursor-pointer">
                                <CardTitle class="flex justify-between">
                                    <span>
                                        Shopping List
                                        <Badge class="text-xs" variant="secondary">{{ incompleteShoppingListItems || 0 }}</Badge>
                                    </span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <todo-list
                                    :allow-date-change="false"
                                    :allow-reorder="false"
                                    :items="shoppingList"
                                    @item-added="onShoppingListAdd"
                                    @item-checked="onShoppingListUpdate"
                                    @item-removed="onShoppingListRemove"
                                    @title-changed="onShoppingListUpdate"
                                />
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6 mt-6">
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex justify-between">
                                    <span>Habit tracker</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <habit-tracker :habits="habits"/>
                            </CardContent>
                        </Card>
                    </div>
                </div>

<!--                    <vue-monaco-editor-->
<!--                        v-model:value="code"-->
<!--                        :options="MONACO_EDITOR_OPTIONS"-->
<!--                        theme="vs-dark"-->
<!--                        @mount="handleMount"-->
<!--                        @change="debouncedUpdateNote"-->
<!--                        class=" min-h-[800px] max-h-full"-->
<!--                    />-->
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TodoList from "@/Pages/Goals/TodoList.vue";
import HabitTracker from "@/Pages/ThisWeek/HabitTracker.vue";
import { Badge } from "@/shadcn/ui/badge/index.js";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/shadcn/ui/card/index.js";
import VueMonacoEditor from "@guolao/vue-monaco-editor";
import { Head } from "@inertiajs/vue3";
import axios from "axios";
import { format } from "date-fns";
import debounce from "lodash.debounce";
import { ChevronDown, ChevronRight } from "lucide-vue-next";
import { shallowRef } from "vue";
import { useToast } from "vue-toastification";

export default {
  components: {
    HabitTracker,
    CardDescription,
    ChevronDown,
    ChevronRight,
    TodoList,
    Card,
    Badge,
    CardContent,
    CardTitle,
    CardHeader,
    AuthenticatedLayout,
    VueMonacoEditor,
    Head,
  },
  props: {
    today: Object,
    habits: Object,
    shoppingList: Array,
    // note: String,
  },
  computed: {
    incompleteTasksToday() {
      return this.today.tasks.filter((task) => !task.completed).length;
    },
    incompleteShoppingListItems() {
      return this.shoppingList.filter((item) => !item.completed).length;
    },
  },
  data() {
    return {
      // code: this.note,
      // editorRef: shallowRef(),
      // toast: useToast(),
    };
  },
  methods: {
    onTaskAdd(taskData) {
      const payload = [];
      for (const task of taskData) {
        payload.push({ ...task, due_date: format(new Date(), "yyyy-MM-dd"), order: -1 });
      }

      this.$inertia.post("/tasks", payload, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    onTaskCheck(task) {
      this.$inertia.put(`/tasks/${task.id}`, task, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    onTaskUpdate(task) {
      this.$inertia.put(`/tasks/${task.id}`, task, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    onOrderUpdate(tasks) {
      axios.post("/tasks/reorder", { tasks });
    },
    onTaskRemove(task) {
      this.$inertia.delete(`/tasks/${task.id}`, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },

    onShoppingListAdd(items) {
      this.$inertia.post("/shopping-lists", items, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    onShoppingListUpdate(item) {
      this.$inertia.put(`/shopping-lists/${item.id}`, item, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    onShoppingListRemove(item) {
      this.$inertia.delete(`/shopping-lists/${item.id}`, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },

    //   handleMount(editor) {
    //     this.editorRef = editor;
    //   },
    //   formatCode() {
    //     this.editorRef?.getAction("editor.action.formatDocument").run();
    //   },
    //   updateNote() {
    //     axios
    //       .post("/dashboard/update", { note: this.code })
    //       .then(() => {
    //         // this.toast.success("Note updated successfully");
    //       })
    //       .catch((error) => {
    //         console.error("Error updating note:", error);
    //         this.toast.error("Error updating note");
    //       });
    //   },
    //   debouncedUpdateNote: debounce(function () {
    //     this.updateNote();
    //   }, 300),
    // },
    // computed: {
    //   MONACO_EDITOR_OPTIONS() {
    //     return {
    //       automaticLayout: true,
    //       formatOnType: true,
    //       formatOnPaste: true,
    //       autocomplete: false,
    //       trimAutoWhitespace: false,
    //     };
    //   },
  },
};
</script>
<style scoped>

</style>
