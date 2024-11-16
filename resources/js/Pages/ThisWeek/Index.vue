<template>
    <Head title="This week"/>

    <AuthenticatedLayout>
        <div class="pt-6 pb-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Date Navigation -->
                <div class="flex items-center justify-center space-x-4">
                    <Link :href="formatWeekUrl(subWeeks(selectedWeekStart, 1))" class="text-gray-500 hover:text-gray-700">
                        <ChevronLeft/>
                    </Link>
                    <span class="text-lg font-semibold">
                        {{ formattedDateRange }}
                    </span>
                    <Link :href="formatWeekUrl(addWeeks(selectedWeekStart, 1))" class="text-gray-500 hover:text-gray-700">
                        <ChevronRight/>
                    </Link>
                </div>

                <div class="text-right mb-2">
                    <button class="underline" type="button" @click="toggleAllContent">Toggle all</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <Card
                            v-for="(day, key) in tasksByDay"
                            :key="day.title"
                            class="mb-6"
                            :class="{
                                'border-black': isCurrentDay(day.date)
                            }"
                        >
                            <CardHeader class="cursor-pointer" @click="toggleContent(key)">
                                <CardTitle :class="{
                                    'font-normal': !isCurrentDay(day.date),
                                    'underline': isCurrentDay(day.date),
                                }" class="flex justify-between">
                                    <span>
                                        {{ day.title }}
                                        <Badge class="text-xs" variant="secondary">{{ incompleteTasksByDay[key] || 0 }}</Badge>
                                    </span>
                                    <ChevronRight v-if="isVisible(key)"/>
                                    <ChevronDown v-if="!isVisible(key)"/>
                                </CardTitle>
                                <CardDescription>{{ day.date }}</CardDescription>
                            </CardHeader>
                            <CardContent v-if="isVisible(key)">
                                <todo-list
                                    :items="day.tasks"
                                    @item-added="tasks => onTaskAdd(tasks, day.date)"
                                    @item-checked="onTaskCheck"
                                    @item-removed="onTaskRemove"
                                    @date-changed="onTaskUpdate"
                                    @order-updated="onOrderUpdate"
                                    @title-changed="onTaskUpdate"
                                >
                                    <template #dropdown-menu-item="{ item }">
                                        <DropdownMenuGroup>
                                            <DropdownMenuSub>
                                                <DropdownMenuSubTrigger>
                                                    <span>Assign to goal</span>
                                                </DropdownMenuSubTrigger>
                                                <DropdownMenuPortal>
                                                    <DropdownMenuSubContent class="overflow-y-auto h-64">
                                                        <DropdownMenuItem v-for="goal in Object.values(this.goals)">
                                                            <DropdownMenuCheckboxItem
                                                                :checked="goal.id === item.goal_id"
                                                                @update:checked="assignToGoal(item, goal)"
                                                            >
                                                                {{goal.title}}
                                                            </DropdownMenuCheckboxItem>
                                                        </DropdownMenuItem>
                                                    </DropdownMenuSubContent>
                                                </DropdownMenuPortal>
                                            </DropdownMenuSub>
                                        </DropdownMenuGroup>
                                    </template>
                                </todo-list>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <Card>
                            <CardHeader class="cursor-pointer" @click="toggleContent('thisWeek')">
                                <CardTitle class="flex justify-between">
                                    <span>
                                        This Week
                                        <Badge class="text-xs" variant="secondary">{{ incompleteTasksThisWeek || 0 }}</Badge>
                                    </span>
                                    <ChevronRight v-if="isVisible('thisWeek')"/>
                                    <ChevronDown v-if="!isVisible('thisWeek')"/>
                                </CardTitle>
                            </CardHeader>
                            <CardContent v-if="isVisible('thisWeek')">
                                <todo-list
                                    :allow-reorder="false"
                                    :items="tasksThisWeek"
                                    @item-added="onThisWeekTaskAdd"
                                    @item-checked="onTaskCheck"
                                    @item-removed="onThisWeekTaskRemove"
                                    @date-changed="onThisWeekTaskDateChange"
                                    @order-updated="onOrderUpdate"
                                    @title-changed="onTaskUpdate"
                                >
                                    <!-- Custom dropdown menu content for each task -->
                                    <template #dropdown-menu-item="{ item }">
                                        <DropdownMenuGroup>
                                            <DropdownMenuSub>
                                                <DropdownMenuSubTrigger>
                                                    <span>Assign to goal</span>
                                                </DropdownMenuSubTrigger>
                                                <DropdownMenuPortal>
                                                    <DropdownMenuSubContent class="overflow-y-auto h-64">
                                                        <DropdownMenuItem v-for="goal in Object.values(this.goals)">
                                                            <DropdownMenuCheckboxItem
                                                                :checked="goal.id === item.goal_id"
                                                                @update:checked="assignToGoal(item, goal)"
                                                            >
                                                                {{goal.title}}
                                                            </DropdownMenuCheckboxItem>
                                                        </DropdownMenuItem>
                                                    </DropdownMenuSubContent>
                                                </DropdownMenuPortal>
                                            </DropdownMenuSub>
                                        </DropdownMenuGroup>
                                        <DropdownMenuItem @click="moveToNextWeek(item)">Move to next week</DropdownMenuItem>
                                    </template>
                                </todo-list>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader class="cursor-pointer" @click="toggleContent('habitTracker')">
                                <CardTitle class="flex justify-between">
                                    <span>Habit tracker</span>
                                    <ChevronRight v-if="isVisible('habitTracker')"/>
                                    <ChevronDown v-if="!isVisible('habitTracker')"/>
                                </CardTitle>
                            </CardHeader>
                            <CardContent v-if="isVisible('habitTracker')">
                                <habit-tracker :habits="habitsThisWeek"/>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader class="cursor-pointer" @click="toggleContent('shoppingList')">
                                <CardTitle class="flex justify-between">
                                    <span>
                                        Shopping List
                                        <Badge class="text-xs" variant="secondary">{{ incompleteShoppingListItems || 0 }}</Badge>
                                    </span>
                                    <ChevronRight v-if="isVisible('shoppingList')"/>
                                    <ChevronDown v-if="!isVisible('shoppingList')"/>
                                </CardTitle>
                            </CardHeader>
                            <CardContent v-if="isVisible('shoppingList')">
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

                        <Card>
                            <CardHeader class="cursor-pointer" @click="toggleContent('notes')">
                                <CardTitle class="flex justify-between">
                                    <span>Notes</span>
                                    <ChevronRight v-if="isVisible('notes')"/>
                                    <ChevronDown v-if="!isVisible('notes')"/>
                                </CardTitle>
                            </CardHeader>
                            <CardContent v-if="isVisible('notes')">
                                <quill-editor
                                    v-model:content="note.content"
                                    class="w-full h-40 p-2 border"
                                    content-type="html"
                                    theme="snow"
                                    @update:content="debouncedUpdateNote"
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
import HabitTracker from "@/Pages/ThisWeek/HabitTracker.vue";
import { Badge } from "@/shadcn/ui/badge/index.js";
import { Button } from "@/shadcn/ui/button/index.js";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/shadcn/ui/card/index.js";
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuPortal,
  DropdownMenuSeparator,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger,
} from "@/shadcn/ui/dropdown-menu/index.js";
import { Head, Link } from "@inertiajs/vue3";
import { QuillEditor } from "@vueup/vue-quill";
import { addWeeks, endOfWeek, format, getWeek, isSameDay, startOfWeek, subWeeks } from "date-fns";
import debounce from "lodash.debounce";
import { ChevronDown, ChevronLeft, ChevronRight } from "lucide-vue-next";
import { useToast } from "vue-toastification";

export default {
  components: {
    Badge,
    Button,
    QuillEditor,
    AuthenticatedLayout,
    TodoList,
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
    HabitTracker,
    Head,
    ChevronLeft,
    ChevronRight,
    ChevronDown,
    Link,
    DropdownMenuItem,
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
    DropdownMenuCheckboxItem,
    DropdownMenuSub,
    DropdownMenuSubTrigger,
    DropdownMenuPortal,
    DropdownMenuSubContent,
    DropdownMenuSeparator,
    DropdownMenuGroup,
  },
  props: {
    tasksByDay: Object,
    tasksThisWeek: Array,
    tasksWaiting: Array,
    note: Object,
    selectedWeekStartDate: String,
    habitsThisWeek: Array,
    shoppingList: Array,
    goals: Array,
  },
  data() {
    const selectedWeekStart = startOfWeek(new Date(this.selectedWeekStartDate), { weekStartsOn: 1 });
    const selectedWeekId = getWeek(selectedWeekStart, { weekStartsOn: 1, firstWeekContainsDate: 4 });

    return {
      toast: useToast(),
      selectedWeekStart,
      selectedWeekId, // Added
      visibleCards: {},
      allVisible: true, // Flag to track if all cards are opened or closed
    };
  },
  computed: {
    formattedDateRange() {
      const start = format(this.selectedWeekStart, "MMMM dd, yyyy");
      const end = format(endOfWeek(this.selectedWeekStart, { weekStartsOn: 1 }), "MMMM dd, yyyy");
      return `${start} - ${end}`;
    },

    // Method to count incomplete tasks for each day
    incompleteTasksByDay() {
      const result = {};
      for (const [key, day] of Object.entries(this.tasksByDay)) {
        result[key] = day.tasks.filter((task) => !task.completed).length;
      }
      return result;
    },

    // Method to count incomplete tasks for the whole week
    incompleteTasksThisWeek() {
      return this.tasksThisWeek.filter((task) => !task.completed).length;
    },

    // Method to count incomplete tasks in the shopping list
    incompleteShoppingListItems() {
      return this.shoppingList.filter((item) => !item.completed).length;
    },
  },
  mounted() {
    // Load visibleCards from localStorage if it exists
    const storedVisibleCards = localStorage.getItem("visibleCards");
    if (storedVisibleCards) {
      this.visibleCards = JSON.parse(storedVisibleCards);
    }
  },
  watch: {
    visibleCards: {
      handler(newVal) {
        // Update localStorage whenever visibleCards changes
        localStorage.setItem("visibleCards", JSON.stringify(newVal));
      },
      deep: true,
    },
  },
  methods: {
    subWeeks,
    addWeeks,

    onTaskAdd(tasks, date) {
      const payload = [];
      for (const task of tasks) {
        payload.push({ ...task, due_date: date, order: -1 });
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

    onThisWeekTaskAdd(tasks) {
      const payload = [];
      for (const task of tasks) {
        payload.push({ ...task, week_reference: this.selectedWeekId, order: -1 });
      }

      this.$inertia.post("/tasks", payload, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    onThisWeekTaskDateChange(updatedTask) {
      const task = { ...updatedTask, week_reference: null };
      this.$inertia.put(`/tasks/${task.id}`, task, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    onThisWeekTaskRemove(task) {
      this.$inertia.delete(`/tasks/${task.id}`, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    moveToNextWeek(updatedTask) {
      const task = { ...updatedTask, week_reference: this.selectedWeekId + 1 };
      this.$inertia.put(`/tasks/${task.id}`, task, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    assignToGoal(updatedTask, goal) {
      const task = { ...updatedTask, goal_id: goal.id };
      this.$inertia.put(`/tasks/${task.id}`, task, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },

    updateNote() {
      axios.put(`/notes/${this.note.id}`, { content: this.note.content });
    },
    debouncedUpdateNote: debounce(function () {
      this.updateNote();
    }, 300),

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

    isCurrentDay(date) {
      return isSameDay(new Date(date), new Date());
    },
    formatWeekUrl(startOfWeek) {
      return route("this-week", { week_start: format(startOfWeek, "yyyy-MM-dd") });
    },
    toggleContent(key) {
      const weekKey = `${this.selectedWeekId}-${key}`;
      if (!Object.hasOwn(this.visibleCards, weekKey)) {
        this.visibleCards[weekKey] = false;
      } else {
        this.visibleCards[weekKey] = !this.visibleCards[weekKey];
      }

      localStorage.setItem("visibleCards", JSON.stringify(this.visibleCards));
    },
    toggleAllContent() {
      // Get the week-specific key for each card and set all to the same visibility
      for (const key in this.tasksByDay) {
        const weekKey = `${this.selectedWeekId}-${key}`;
        this.visibleCards[weekKey] = !this.allVisible;
      }

      // Update other specific cards like 'This Week', 'habitTracker', etc.
      const extraCards = ["thisWeek", "habitTracker", "shoppingList", "notes"];
      for (const key of extraCards) {
        const weekKey = `${this.selectedWeekId}-${key}`;
        this.visibleCards[weekKey] = !this.allVisible;
      }

      // Toggle the allVisible flag
      this.allVisible = !this.allVisible;

      // Store the updated visibleCards in localStorage
      localStorage.setItem("visibleCards", JSON.stringify(this.visibleCards));
    },
    isVisible(key) {
      const weekKey = `${this.selectedWeekId}-${key}`;
      return this.visibleCards[weekKey] !== false;
    },
  },
};
</script>

<style scoped>
/* Add any additional styles if needed */
</style>
