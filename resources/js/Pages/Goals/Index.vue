<template>
  <Head title="Goals" />

  <AuthenticatedLayout>
    <div class="pt-2 pb-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="flex items-center justify-center space-x-4">
              <Link :href="formatYearUrl(selectedYear - 1)" class="text-gray-500 hover:text-gray-700">
                  <ChevronLeft />
              </Link>
              <span class="text-lg font-semibold">{{ formattedSelectedYear }}</span>
              <Link :href="formatYearUrl(selectedYear + 1)" class="text-gray-500 hover:text-gray-700">
                  <ChevronRight />
              </Link>
          </div>

        <div class="text-right">
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button class="mb-2" variant="outline">Settings</Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuCheckboxItem
                v-model:checked="showCompleted"
                @update:checked="toggleCompleted()"
              >
                Show completed
              </DropdownMenuCheckboxItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <Card v-for="goalType in filteredGoals" :key="goalType.id">
            <CardHeader>
              <CardTitle>{{ goalType.title }}
                  <Badge class="text-xs" variant="secondary">{{goalType.goals.length || 0}}</Badge>
              </CardTitle>
            </CardHeader>
            <CardContent>
              <todo-list
                :items="goalType.goals"
                :allow-title-edit="false"
                @item-added="goals => onGoalAdd(goalType.id, goals)"
                @item-checked="goal => onGoalUpdate(goalType.id, goal)"
                @item-removed="goal => onGoalRemove(goalType.id, goal)"
                @order-updated="goalList => onGoalOrderUpdate(goalType.id, goalList)"
                @date-changed="goal => onGoalUpdate(goalType.id, goal)"
              >
                <!-- Custom dropdown menu content -->
                <template #dropdown-menu-item="{ item }">
                  <DropdownMenuGroup>
                      <DropdownMenuSub>
                        <DropdownMenuSubTrigger>
                          <span>Move to category</span>
                        </DropdownMenuSubTrigger>
                        <DropdownMenuPortal>
                          <DropdownMenuSubContent>
                            <DropdownMenuItem v-for="category in Object.values(this.goals)">
                              <DropdownMenuCheckboxItem
                                  :checked="goalType.id === category.id"
                                  @update:checked="moveGoalToCategory(item, category.type)"
                              >
                                {{category.title}}
                              </DropdownMenuCheckboxItem>
                            </DropdownMenuItem>
                          </DropdownMenuSubContent>
                        </DropdownMenuPortal>
                      </DropdownMenuSub>
                  </DropdownMenuGroup>
                  <DropdownMenuItem @click="moveToNextYear(item)">Move to next year</DropdownMenuItem>
                </template>
              </todo-list>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TodoList from "@/Pages/Goals/TodoList.vue";
import { Badge } from "@/shadcn/ui/badge/index.js";
import { Button } from "@/shadcn/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/shadcn/ui/card/index.js";
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
} from "@/shadcn/ui/dropdown-menu";
import { Head, Link } from "@inertiajs/vue3";
import axios from "axios";
import { ChevronLeft, ChevronRight } from "lucide-vue-next";
import { useToast } from "vue-toastification";

export default {
  components: {
    Badge,
    AuthenticatedLayout,
    TodoList,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    Head,
    Link,
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
    DropdownMenuItem,
    DropdownMenuCheckboxItem,
    DropdownMenuSub,
    DropdownMenuSubTrigger,
    DropdownMenuPortal,
    DropdownMenuSubContent,
    DropdownMenuSeparator,
    DropdownMenuGroup,
    Button,
    ChevronLeft,
    ChevronRight,
  },
  props: {
    selectedYear: Number,
    goals: Object,
  },
  data() {
    return {
      toast: null,
      showCompleted: false,
    };
  },
  computed: {
    filteredGoals() {
      // Filter goals based on the `completed` state and `showCompleted` toggle
      return Object.values(this.goals).map((goalType) => {
        return {
          ...goalType,
          goals: goalType.goals.filter((goal) => this.showCompleted || !goal.completed),
        };
      });
    },
    formattedSelectedYear() {
      return this.selectedYear;
    },
  },
  mounted() {
    this.toast = useToast();
    const storedShowCompleted = localStorage.getItem("showCompleted");
    if (storedShowCompleted !== null) {
      this.showCompleted = JSON.parse(storedShowCompleted);
    }
  },
  methods: {
    toggleCompleted() {
      localStorage.setItem("showCompleted", this.showCompleted);
    },
    onGoalAdd(typeId, goals) {
      const payload = [];
      for (const goal of goals) {
        payload.push({
          title: goal.title,
          type: typeId,
          completed: false,
          order: -1,
          due_date: null,
          year: this.selectedYear,
        });
      }

      this.$inertia.post("/goals", payload, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    onGoalUpdate(typeId, goal) {
      if (!goal.due_date && !goal.year) {
        goal.year = this.selectedYear;
      }

      this.$inertia.put(`/goals/${goal.id}`, goal, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    onGoalRemove(typeId, goal) {
      this.$inertia.delete(`/goals/${goal.id}`, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    onGoalOrderUpdate(typeId, goals) {
      axios.post("/goals/order", { goals });
    },
    moveGoalToCategory(updatedGoal, newType) {
      const goal = { ...updatedGoal, type: newType };
      this.$inertia.put(`/goals/${goal.id}`, goal, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    moveToNextYear(updatedGoal) {
      const goal = { ...updatedGoal, due_date: null, year: this.selectedYear + 1 };
      this.$inertia.put(`/goals/${goal.id}`, goal, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: (errors) => {},
      });
    },
    formatYearUrl(year) {
      return route("goals.index", { year: year });
    },
  },
};
</script>
