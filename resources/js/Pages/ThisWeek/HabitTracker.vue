<template>
          <div class="text-right">
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button class="mb-2" variant="outline">Settings</Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuCheckboxItem
                v-model:checked="showArchived"
                @update:checked="toggleArchived()"
              >
                Show archived
              </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
    </DropdownMenu>
  </div>

    <div class="mb-2">
        <Table>
            <!-- Table Header with days of the week -->
            <TableHeader>
                <TableRow>
                    <TableHead class="w-[120px]">Habit</TableHead>
                    <TableHead v-for="(day, index) in days" :key="index" class="text-center">{{ day }}</TableHead>
                    <TableHead></TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <template v-for="(habits, groupName) in groupedHabits" :key="groupName" class="mb-6">
                    <h2 class="text-sm uppercase font-semibold mt-4">{{ groupName }}</h2>
                    <TableRow v-for="habit in habits" :key="habit.id">
                        <TableCell class="font-medium p-0 min-w-[200px]" :class="{'line-through': habit.archived}">{{ habit.name }}</TableCell>

                        <!-- Status for each day of the week -->
                        <TableCell v-for="(day, index) in habit.days" :key="index"
                                   :class="{'bg-gray-100 dark:bg-gray-800': day.isToday }"
                                   class="text-center"
                        >
                            <input
                                :checked="day.status"
                                :disabled="habit.archived"
                                class="form-checkbox h-5 w-5 text-green-600 accent-black dark:accent-white"
                                type="checkbox"
                                @change="toggleHabit(habit.id, day.date, $event)"
                            />
                        </TableCell>

                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button class="mt-2" size="icon" variant="ghost">
                                    <MoreVertical class="w-4 h-4"/>
                                    <span class="sr-only">More</span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem @click="showEditModal = true;selectedHabit = habit;">Edit</DropdownMenuItem>
                                <DropdownMenuItem @click="archiveHabit(habit)">{{ habit.archived ? 'Un-archive' : 'Archive' }}</DropdownMenuItem>
                                <DropdownMenuItem class="text-red-500" @click="deleteHabit(habit.id)">Delete</DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </TableRow>
                </template>
            </TableBody>
        </Table>
    </div>

    <!-- Add New Habit -->
    <div class="flex items-center">
        <Input v-model="newHabit.name"
               class="flex-grow mr-2 rounded-b-none ring-0 border-0 focus-visible:border-b-2 px-0
                   focus-visible:ring-offset-0 focus-visible:ring-0" placeholder="+ Add new" @keyup.enter="addHabit"/>
    </div>

    <Dialog :open="showEditModal">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Edit habit</DialogTitle>
                <DialogDescription>
                    Edit and save your habit.
                </DialogDescription>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label class="text-right" for="title">
                        Title
                    </Label>
                    <Input id="title" v-model="selectedHabit.name" :value="selectedHabit.name" class="col-span-3"/>
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label class="text-right" for="group">
                        Group
                    </Label>
                    <Select id="group" v-model="selectedHabit.group">
                        <SelectTrigger class="w-[275px]">
                            <SelectValue placeholder="Select group"/>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem v-for="group in groups"
                                    :value="group.key"
                                >{{ group.label }}</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <DialogFooter>
                <Button type="button" variant="destructive" @click="showEditModal = false">
                    Cancel
                </Button>
                <Button type="button" @click="editHabit">
                    Save changes
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script>
import Checkbox from "@/Components/Checkbox.vue";
import { Button } from "@/shadcn/ui/button/index.js";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/shadcn/ui/dialog/index.js";
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/shadcn/ui/dropdown-menu/index.js";
import { Input } from "@/shadcn/ui/input/index.js";
import { Label } from "@/shadcn/ui/label/index.js";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/shadcn/ui/select/index.js";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/shadcn/ui/table/index.js";
import { MoreVertical } from "lucide-vue-next";

export default {
  name: "HabitTracker",
  components: {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
    DropdownMenuCheckboxItem,
    Button,
    MoreVertical,
    Input,
    TableCell,
    TableBody,
    TableHead,
    TableRow,
    TableHeader,
    Table,
    Checkbox,
    Dialog,
    DialogTrigger,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectGroup,
    SelectLabel,
    SelectItem,
    Label,
  },
  props: {
    habits: Array,
  },
  data() {
    return {
      days: ["M", "T", "W", "T", "F", "S", "S"],
      groups: [
        { key: "morning", label: "Morning" },
        { key: "day", label: "Day" },
        { key: "evening", label: "Evening" },
        { key: "uncategorised", label: "Uncategorised" },
      ],
      newHabit: {
        name: "",
      },
      showEditModal: false,
      selectedHabit: null,
      showArchived: false,
    };
  },
  mounted() {
    const storedShowArchived = localStorage.getItem("showArchivedHabits");
    if (storedShowArchived !== null) {
      this.showArchived = JSON.parse(storedShowArchived);
    }
  },
  computed: {
    groupedHabits() {
      return this.habits
        .filter((habit) => this.showArchived || !habit.archived) // Show archived based on the toggle
        .reduce((groups, habit) => {
          const group = habit.group || "uncategorised"; // Default to 'Uncategorised' if no group
          if (!groups[group]) {
            groups[group] = [];
          }
          groups[group].push(habit);
          return groups;
        }, {});
    },
  },
  methods: {
    async addHabit() {
      if (this.newHabit.name.trim() !== "") {
        this.$inertia.post("/habits", this.newHabit, {
          preserveScroll: true,
          onSuccess: (page) => {
            this.newHabit.name = "";
          },
          onError: (errors) => {},
        });
      }
    },
    async toggleHabit(habitId, date, event) {
      const status = event.target.checked;
      this.$inertia.put(
        `/habits/${habitId}`,
        { date, status },
        {
          preserveScroll: true,
          onSuccess: (page) => {},
          onError: (errors) => {},
        },
      );
    },
    async editHabit() {
      if (!this.selectedHabit) {
        return;
      }

      this.$inertia.put(
        `/habits/${this.selectedHabit.id}`,
        {
          name: this.selectedHabit.name,
          group: this.selectedHabit.group,
        },
        {
          preserveScroll: true,
          onSuccess: (page) => {
            this.showEditModal = false;

            console.log(this.selectedHabit);
            console.log(this.showEditModal);
          },
          onError: (errors) => {},
        },
      );
    },
    async archiveHabit(habit) {
      this.$inertia.put(
        `/habits/${habit.id}`,
        { archived: !habit.archived },
        {
          preserveScroll: true,
          onSuccess: (page) => {},
          onError: (errors) => {},
        },
      );
    },
    async deleteHabit(habitId) {
      if (confirm("Are you sure you want to delete this habit?")) {
        this.$inertia.delete(`/habits/${habitId}`, {
          preserveScroll: true,
          onSuccess: (page) => {},
          onError: (errors) => {},
        });
      }
    },
    toggleArchived() {
      localStorage.setItem("showArchivedHabits", this.showArchived);
    },
  },
};
</script>
