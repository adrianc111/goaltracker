<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                variant="none"
                :class="cn(
                    'justify-start text-left font-normal cursor-pointer p-3 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-50',
                  !selectedDate && 'text-muted-foreground',
                  this.classes
                )"
            >
                <CalendarIcon class="h-4 w-4" />
                <span v-if="this.date" class="ml-1">
                    {{ format(selectedDate, "d MMM") }}
                </span>
<!--                <template v-else>-->
<!--                    <span>Due date</span>-->
<!--                </template>-->
            </Button>
        </PopoverTrigger>
        <PopoverContent class="flex w-auto flex-col space-y-2 p-2">
            <Select @update:model-value="onDateChange">
                <SelectTrigger>
                    <SelectValue placeholder="Select" />
                </SelectTrigger>
                <SelectContent position="popper">
                    <SelectItem value="0">
                        Today
                    </SelectItem>
                    <SelectItem value="1">
                        Tomorrow
                    </SelectItem>
                    <SelectItem value="3">
                        In 3 days
                    </SelectItem>
                    <SelectItem value="7">
                        In a week
                    </SelectItem>
                    <SelectItem value="next_year">
                        Next year
                    </SelectItem>
                    <SelectItem v-if="this.date" value="-1" class="text-red-500">
                        Clear
                    </SelectItem>
                </SelectContent>
            </Select>
            <div class="rounded-md border">
                <Calendar @update:model-value="onDateChange" mode="single" />
            </div>
        </PopoverContent>
    </Popover>
</template>

<script>
import { format } from "date-fns";
import { Calendar as CalendarIcon } from "lucide-vue-next";

import { cn } from "@/lib/utils";
import { Button } from "@/shadcn/ui/button";
import { Popover, PopoverContent, PopoverTrigger } from "@/shadcn/ui/popover";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/shadcn/ui/select";
import { Calendar } from "@/shadcn/ui/v-calendar";

export default {
  components: {
    Popover,
    PopoverContent,
    PopoverTrigger,
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
    Calendar,
    CalendarIcon,
    Button,
  },
  props: ["date", "classes"],
  data() {
    return {
      selectedDate: new Date(this.date),
    };
  },
  emits: ["onDateChange"],
  methods: {
    cn,
    format,
    onDateChange(date) {
      const newDate = new Date();

      switch (date) {
        case "-1":
          // Set to today's date
          this.selectedDate = null;
          break;
        case "0":
          // Set to today's date
          this.selectedDate = new Date();
          break;
        case "1":
          // Set to tomorrow's date
          newDate.setDate(newDate.getDate() + 1);
          this.selectedDate = newDate;
          break;
        case "3":
          // Add 3 days
          newDate.setDate(newDate.getDate() + 3);
          this.selectedDate = newDate;
          break;
        case "7":
          // Add 7 days
          newDate.setDate(newDate.getDate() + 7);
          this.selectedDate = newDate;
          break;
        case "next_year": {
          // Add 7 days
          const nextYear = new Date().getFullYear() + 1;
          this.selectedDate = new Date(nextYear, 0, 1);
          break;
        }
        default:
          this.selectedDate = date;
      }

      if (this.selectedDate) {
        // Normalize selectedDate to London timezone and format as 'YYYY-MM-DD'
        this.selectedDate = format(this.selectedDate, "yyyy-MM-dd");
      }

      // Emit the updated date
      this.$emit("onDateChange", this.selectedDate);
    },
  },
};
</script>
