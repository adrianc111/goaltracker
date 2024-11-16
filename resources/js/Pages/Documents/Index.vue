<template>
    <Head title="Documents"/>

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-12 gap-6">

                    <!-- Left Column (3/12) -->
                    <div class="col-span-12 md:col-span-3 space-y-6 mt-6">
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex justify-between items-center">
                                    <span>Documents</span>
                                    <Button @click="addNewDocument" variant="outline">New</Button>
                                </CardTitle>
                                <Input
                                    type="text"
                                    v-model="search"
                                class="mt-4 w-full p-2 border-b"
                                placeholder="Search documents"
                                />
                            </CardHeader>
                            <CardContent class="h-[64vh] overflow-y-auto">
                                <!-- Loop over documents and display them as a list -->
                                <ul class="space-y-2">
                                    <li v-for="(doc, index) in filteredDocuments" :key="index">
                                        <div class="flex justify-between items-center">
                                            <button
                                                class="w-full text-left p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition truncate"
                                                :class="{
                                                    'bg-gray-100 dark:bg-gray-600': doc.id === selectedDocument?.id
                                                }"
                                                @click="selectDocument(doc)">
                                                {{ doc.title }}
                                            </button>
                                            <!-- Dropdown Menu for Delete -->
                                            <DropdownMenu>
                                                <DropdownMenuTrigger as-child>
                                                    <Button size="icon" variant="ghost">
                                                        <MoreVertical class="w-4 h-4"/>
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end">
                                                    <DropdownMenuItem @click="deleteDocument(doc)">Delete</DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </div>
                                    </li>
                                </ul>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Right Column (9/12) -->
                    <div class="col-span-12 md:col-span-9 space-y-6 mt-6">
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex justify-between">
                                    <Input
                                        v-if="selectedDocument"
                                        v-model="selectedDocument.title"
                                        @blur="updateDocument"
                                        class="border-b focus:outline-none w-full text-xl"
                                        placeholder="Enter title"
                                    />
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <!-- Show QuillEditor if a document is selected -->
                                <div v-if="selectedDocument">
                                    <quill-editor
                                        v-model:content="selectedDocument.content"
                                        content-type="html"
                                        theme="snow"
                                        class="h-64"
                                        :key="selectedDocument.id"
                                        @update:content="debouncedUpdateDocument"
                                    />
                                </div>
                                <div v-else class="text-gray-500">
                                    No document selected.
                                </div>
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
import { Button } from "@/shadcn/ui/button/index.js";
import { Card, CardContent, CardHeader, CardTitle } from "@/shadcn/ui/card/index.js";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/shadcn/ui/dropdown-menu/index.js";
import { Input } from "@/shadcn/ui/input/index.js";
import { Head } from "@inertiajs/vue3";
import { QuillEditor } from "@vueup/vue-quill";
import axios from "axios";
import debounce from "lodash.debounce";
import { MoreVertical } from "lucide-vue-next";

export default {
  components: {
    Input,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    AuthenticatedLayout,
    QuillEditor,
    Head,
    Button,
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    MoreVertical,
  },
  props: {
    documents: Object,
  },
  data() {
    return {
      selectedDocument: null,
      search: "", // Search input model
    };
  },
  computed: {
    // Filter the documents based on the search query
    filteredDocuments() {
      if (!this.search) {
        return this.documents;
      }

      // Case-insensitive search through titles and content
      const searchQuery = this.search.toLowerCase();
      return this.documents.filter(
        (doc) => doc.title.toLowerCase().includes(searchQuery) || doc?.content?.toLowerCase().includes(searchQuery),
      );
    },
  },
  methods: {
    selectDocument(doc) {
      this.selectedDocument = doc;
    },
    addNewDocument() {
      const newDoc = { title: "Untitled", content: "" };

      axios
        .post("/documents", {
          title: newDoc.title,
          content: newDoc.content,
        })
        .then((response) => {
          this.documents.push(response.data);
          this.selectedDocument = response.data;
        });
    },
    updateDocument() {
      if (!this.selectedDocument) {
        return;
      }

      axios.put(`/documents/${this.selectedDocument.id}`, {
        title: this.selectedDocument.title,
        content: this.selectedDocument.content,
      });
    },
    deleteDocument(doc) {
      if (confirm("Are you sure you want to delete this document?")) {
        this.$inertia.delete(`/documents/${doc.id}`, {
          preserveScroll: true,
          onSuccess: (page) => {
            // reset editor if selected file was just deleted
            if (doc.id === this.selectedDocument.id) {
              this.selectedDocument = null;
            }
          },
          onError: (errors) => {},
        });
      }
    },
    debouncedUpdateDocument: debounce(function () {
      this.updateDocument();
    }, 300),
  },
};
</script>

<style scoped>
/* Additional styles if needed */
</style>
