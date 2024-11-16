<template>
    <div v-if="allowAdd" class="flex items-center">
        <Input v-model="newItem"
               class="flex-grow mr-2 rounded-b-none ring-0 border-0 focus-visible:border-b-2 px-0
               focus-visible:ring-offset-0 focus-visible:ring-0"
               placeholder="+ Add new" @keyup.enter="addItem"
               @paste="handlePaste"
        />
    </div>

    <!-- task list with drag-and-drop -->
    <draggable v-model="localItems" :itemKey="'id'" @end="updateOrder" handle=".handle">
        <template #item="{ element: item }">
            <li :key="item.id" class="flex justify-between items-center">
                <GripHorizontal v-if="allowReorder" class="mr-2 handle cursor-grab" />

                <div class="flex items-center w-full">
                    <Checkbox
                        :id="`item_${item.id}`"
                        v-model="item.completed"
                        :default-checked="item.completed"
                        class="mr-2"
                        @update:checked="checkItem(item)"
                    />
                    <input v-if="editingItem === item.id"
                           v-model="item.title"
                           @blur="finishEditing(item)"
                           @keyup.enter="finishEditing(item)"
                           class="flex-grow text-sm font-medium px-1 border-b focus:outline-none"
                    />
                    <span v-else
                          :class="{ 'line-through': item.completed, 'text-gray-400': item.completed }"
                          class="text-sm text-left font-medium leading-none flex-grow"
                          @dblclick="enableEditing(item)"
                    >
                        <Link v-if="item.url" :href="item.url" class="underline">{{ item.title }}</Link>
                        <span v-else v-html="item.title"></span>
                    </span>

                    <Badge v-if="item?.goal" class="mr-1 mt-0.5 text-center" :variant="item.completed ? 'secondary' : ''">
                        <Link :href="item?.goal?.url">{{ item.goal.title }}</Link>
                    </Badge>

                    <due-date v-if="allowDateChange"
                              :classes="{'text-gray-400': item.completed }"
                              :date="item.due_date"
                              @on-date-change="date => dateChange(item, date)"
                    />
                </div>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button size="icon" variant="ghost">
                            <MoreVertical class="w-4 h-4"/>
                            <span class="sr-only">More</span>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <slot name="dropdown-menu-item" :item="item"></slot>
                        <DropdownMenuItem @click="removeItem(item)">Delete</DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </li>
        </template>
    </draggable>

    <Dialog :open="showModal">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Confirm Multiple Items</DialogTitle>
                <DialogDescription />
            </DialogHeader>
            <p>You've pasted multiple lines. Do you want to add them as separate items?</p>
            <DialogFooter>
                <Button type="button" variant="destructive" @click="cancelModal">
                    Cancel
                </Button>
                <Button type="button" @click="confirmAddMultiple">
                    Save changes
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script>
import DueDate from "@/Components/DueDate.vue";
import Modal from "@/Components/Modal.vue";
import { Badge } from "@/shadcn/ui/badge/index.js";
import { Button } from "@/shadcn/ui/button/index.js";
import { Checkbox } from "@/shadcn/ui/checkbox/index.js";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/shadcn/ui/dialog/index.js";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/shadcn/ui/dropdown-menu/index.js";
import { Input } from "@/shadcn/ui/input/index.js";
import { Link } from "@inertiajs/vue3";
import { format } from "date-fns";
import { GripHorizontal, MoreVertical } from "lucide-vue-next";
import draggable from "vuedraggable";

export default {
  components: {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
    Modal,
    Badge,
    DueDate,
    Button,
    Checkbox,
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
    Input,
    MoreVertical,
    draggable,
    Link,
    GripHorizontal,
  },
  props: {
    items: {
      type: Array,
      required: true,
      default: () => [],
    },
    allowAdd: {
      type: Boolean,
      default: true,
    },
    allowDateChange: {
      type: Boolean,
      default: true,
    },
    allowReorder: {
      type: Boolean,
      default: true,
    },
    allowTitleEdit: {
      type: Boolean,
      default: true,
    },
  },
  emits: ["itemAdded", "itemRemoved", "itemChecked", "orderUpdated", "dateChanged", "titleChanged"],
  data() {
    return {
      newItem: "",
      localItems: [...this.items],
      showModal: false,
      multiLineItems: [],
      editingItem: null,
    };
  },
  watch: {
    items(newItems) {
      this.localItems = [...newItems];
    },
  },
  methods: {
    addItem() {
      this.newItem = this.newItem.trim();
      if (this.newItem) {
        const emitItems = [];
        emitItems.push({
          title: this.newItem,
          completed: false,
          order: this.localItems.length,
          due_date: null,
        });

        this.$emit("itemAdded", emitItems);
        this.newItem = "";
      }
    },
    removeItem(item) {
      this.$emit("itemRemoved", item);
    },
    checkItem(item) {
      item.completed = !item.completed;
      this.localItems = [...this.localItems];
      this.$emit("itemChecked", item);
    },
    dateChange(item, date) {
      item.due_date = date ? format(date, "yyyy-MM-dd") : null;
      this.$emit("dateChanged", item);
    },
    updateOrder() {
      if (!this.allowReorder) {
        return;
      }
      this.localItems.forEach((item, index) => {
        item.order = index + 1;
      });
      this.localItems = [...this.localItems];
      this.$emit("orderUpdated", this.localItems);
    },
    handlePaste(event) {
      const pastedText = (event.clipboardData || window.clipboardData).getData("text");
      const lines = pastedText
        .split("\n")
        .map((line) => line.trim())
        .filter((line) => line.length > 0);

      if (lines.length > 1) {
        event.preventDefault();
        this.multiLineItems = lines;
        this.showModal = true;
      }
    },
    confirmAddMultiple() {
      const emitItems = [];

      for (const item of this.multiLineItems) {
        emitItems.push({
          title: item,
          completed: false,
          order: 0,
          due_date: null,
        });
      }

      this.$emit("itemAdded", emitItems);
      this.multiLineItems = [];
      this.showModal = false;
    },
    cancelModal() {
      this.multiLineItems = [];
      this.showModal = false;
    },
    enableEditing(item) {
      if (this.allowTitleEdit === false) {
        return;
      }
      this.editingItem = item.id;
    },
    finishEditing(item) {
      this.editingItem = null;
      this.$emit("titleChanged", item);
    },
  },
};
</script>

<style scoped>
.line-through {
    text-decoration: line-through;
}
.handle:active {
    cursor: grabbing;
    cursor: -moz-grabbing;
    cursor: -webkit-grabbing;
}
</style>
