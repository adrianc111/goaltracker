<template>
    <Head title="Goal details" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="pb-12">
                <!-- Back button -->
                <Link :href="route('goals.index', { year: goal.year || new Date().getFullYear() })" class="my-4 flex text-gray-500 hover:text-gray-700">
                    <ChevronLeft class="mr-1" />Back to Goals
                </Link>

                <!-- Title Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>
                            <Input class="text-xl" v-model="goal.title" @keyup="debouncedUpdateGoal" />
                        </CardTitle>
                    </CardHeader>
                </Card>

                <!-- Two Columns Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Description Card -->
                        <Card class="mt-6">
                            <CardHeader>
                                <CardTitle>Description</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <quill-editor
                                    theme="snow"
                                    content-type="html"
                                    v-model:content="goal.description"
                                    @update:content="debouncedUpdateGoal"
                                    class="w-full h-40 p-2 border"
                                />
                            </CardContent>
                        </Card>

                        <!-- Tasks with TodoList -->
                        <Card class="mt-6">
                            <CardHeader>
                                <CardTitle>
                                    Tasks
                                    <Badge class="text-xs" variant="secondary">{{goal.tasks.length || 0}}</Badge>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <todo-list
                                    :items="goal.tasks"
                                    @item-added="onTaskAdd"
                                    @item-checked="onTaskCheck"
                                    @order-updated="onOrderUpdate"
                                    @item-removed="onTaskRemove"
                                    @date-changed="onTaskUpdate"
                                    @title-changed="onTaskUpdate"
                                >
                                    <template #dropdown-menu-item="{ item }">
                                        <DropdownMenuItem v-if="item?.due_date" @click="removeFromGoal(item)">Remove from goal</DropdownMenuItem>
                                    </template>
                                </todo-list>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Notes -->
                        <Card class="mt-6">
                            <CardHeader>
                                <CardTitle>Notes</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <quill-editor
                                    theme="snow"
                                    content-type="html"
                                    v-model:content="noteContent"
                                    @update:content="debouncedUpdateNote"
                                    class="w-full h-40 p-2 border"
                                />
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TodoList from "@/Pages/Goals/TodoList.vue";
import { Badge } from "@/shadcn/ui/badge/index.js";
import { Card, CardContent, CardHeader, CardTitle } from "@/shadcn/ui/card/index.js";
import { DropdownMenuItem } from "@/shadcn/ui/dropdown-menu/index.js";
import { Input } from "@/shadcn/ui/input/index.js";
import { Head, Link } from "@inertiajs/vue3";
import { QuillEditor } from "@vueup/vue-quill";
import { format } from "date-fns";
import debounce from "lodash.debounce";
import { ChevronLeft } from "lucide-vue-next";
import { useToast } from "vue-toastification";

export default {
  components: {
    Badge,
    Input,
    Link,
    AuthenticatedLayout,
    TodoList,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    Head,
    QuillEditor,
    ChevronLeft,
    DropdownMenuItem,
  },
  props: ["goal"],
  data() {
    return {
      toast: useToast(),
      noteContent: this.goal.notes.length ? this.goal.notes[0].content : "",
    };
  },
  watch: {
    "goal.notes": {
      immediate: true,
      handler(newNotes) {
        this.noteContent = newNotes.length ? newNotes[0].content : "";
      },
    },
  },
  methods: {
    onTaskAdd(tasks) {
      const payload = [];
      for (const task of tasks) {
        payload.push({ ...task, order: -1 });
      }

      this.$inertia.post(`/goals/${this.goal.id}/tasks`, tasks, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },

    onTaskCheck(task) {
      this.$inertia.put(`/goals/${this.goal.id}/tasks/${task.id}`, task, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },

    onOrderUpdate(tasks) {
      axios.post(`/goals/${this.goal.id}/tasks/reorder`, { tasks });
    },

    onTaskUpdate(task) {
      this.$inertia.put(`/goals/${this.goal.id}/tasks/${task.id}`, task, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },

    onTaskRemove(action) {
      this.$inertia.delete(`/goals/${this.goal.id}/tasks/${action.id}`, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },

    removeFromGoal(updatedTask) {
      const task = { ...updatedTask, goal_id: null };
      this.$inertia.put(`/goals/${this.goal.id}/tasks/${task.id}`, task, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },

    updateGoal() {
      axios.put(`/goals/${this.goal.id}`, this.goal);
    },

    updateNote() {
      axios.put(`/goals/${this.goal.id}/notes/${this.goal.notes[0].id}`, { content: this.noteContent });
    },

    debouncedUpdateGoal: debounce(function () {
      this.updateGoal();
    }, 300),

    debouncedUpdateNote: debounce(function () {
      this.updateNote();
    }, 300),
  },
};
</script>
