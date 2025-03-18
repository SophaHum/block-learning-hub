<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  FlexRender,
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table';
import { ArrowUpDown, ChevronDown, Edit, Eye, Trash } from 'lucide-vue-next';
import { h } from 'vue';
import { Checkbox } from '@/components/ui/checkbox';
import type {
  ColumnDef,
  ColumnFiltersState,
  ExpandedState,
  SortingState,
  VisibilityState,
} from '@tanstack/vue-table';
import { valueUpdater } from '@/utils';
import {
  DropdownMenuItem,
  DropdownMenuSeparator
} from '@/components/ui/dropdown-menu';
import DeleteConfirmationDialog from '@/components/DeleteConfirmationDialog.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: '/posts',
    },
];

// Define the Post type matching your database structure
interface Post {
  id: number;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  featured_image: string | null;
  category_id: number;
  user_id: number;
  is_published: boolean;
  published_at: string | null;
  created_at: string;
  updated_at: string;
  category: {
    id: number;
    name: string;
    slug: string;
    description: string;
    color: string;
    created_at: string;
    updated_at: string;
  };
  user: {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
  };
}

// Define props to receive posts data from the controller using Laravel's pagination structure
// This exactly matches the structure from your database
const props = defineProps<{
  posts: {
    current_page: number;
    data: Post[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
  };
}>();

// Post table columns definition with proper typing
const columns: ColumnDef<Post>[] = [
  {
    id: 'select',
    header: ({ table }) => h(Checkbox, {
      'modelValue': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
      'onUpdate:modelValue': (value: boolean) => table.toggleAllPageRowsSelected(!!value),
      'ariaLabel': 'Select all',
    }),
    cell: ({ row }) => h(Checkbox, {
      'modelValue': row.getIsSelected(),
      'onUpdate:modelValue': (value: boolean) => row.toggleSelected(!!value),
      'ariaLabel': 'Select row',
    }),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: 'title',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Title', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('title')),
  },
  {
    // Change from accessorKey to accessorFn for nested property
    id: 'category',
    header: 'Category',
    accessorFn: (row) => row.category?.name,
    cell: ({ getValue }) => h('div', { class: 'capitalize' }, String(getValue())),
  },
  {
    accessorKey: 'is_published',
    header: 'Status',
    cell: ({ row }) => {
      const status = row.getValue('is_published') as boolean;
      return h('div', {
        class: `inline-block px-3 py-1 rounded-full text-xs font-medium text-center min-w-[80px] ${
          status
            ? 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-300'
            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
        }`
      }, status ? 'Published' : 'Draft');
    },
  },
  {
    // Change from accessorKey to accessorFn for nested property
    id: 'author',
    header: 'Author',
    accessorFn: (row) => row.user?.name,
    cell: ({ getValue }) => h('div', {}, String(getValue())),
  },
  {
    accessorKey: 'created_at',
    header: 'Created',
    cell: ({ row }) => {
      const date = new Date(row.getValue('created_at') as string);
      return h('div', {}, date.toLocaleDateString());
    },
  },
  {
    id: 'actions',
    header: 'Actions',
    enableHiding: false,
    cell: ({ row }) => {
      const post = row.original;

      return h(DropdownMenu, {}, {
        default: () => [
          h(DropdownMenuTrigger, { asChild: true }, {
            default: () => h(Button, {
              variant: 'ghost',
              class: 'h-8 w-8 p-0 data-[state=open]:bg-primary/10 dark:hover:bg-primary/20 hover:bg-primary/10'
            }, {
              default: () => [
                h('div', { class: 'flex items-center justify-center' }, [
                  h('svg', {
                    xmlns: 'http://www.w3.org/2000/svg',
                    width: '16',
                    height: '16',
                    viewBox: '0 0 24 24',
                    fill: 'none',
                    stroke: 'currentColor',
                    'stroke-width': '2',
                    'stroke-linecap': 'round',
                    'stroke-linejoin': 'round',
                    class: 'lucide lucide-more-vertical h-4 w-4'
                  }, [
                    h('circle', { cx: '12', cy: '12', r: '1' }),
                    h('circle', { cx: '12', cy: '5', r: '1' }),
                    h('circle', { cx: '12', cy: '19', r: '1' })
                  ]),
                  h('span', { class: 'sr-only' }, 'Actions')
                ])
              ]
            })
          }),
          h(DropdownMenuContent, { align: 'end', class: 'w-40 dark:bg-zinc-900 dark:border-zinc-800' }, {
            default: () => [
              h(DropdownMenuItem, {
                onClick: () => router.visit(`/posts/${post.id}`),
                class: 'dark:focus:bg-zinc-800 dark:focus:text-white cursor-pointer'
              }, {
                default: () => [
                  h(Eye, { class: 'mr-2 h-4 w-4 dark:text-zinc-400' }),
                  'View'
                ]
              }),
              h(DropdownMenuItem, {
                onClick: () => router.visit(`/posts/${post.id}/edit`),
                class: 'dark:focus:bg-zinc-800 dark:focus:text-white cursor-pointer'
              }, {
                default: () => [
                  h(Edit, { class: 'mr-2 h-4 w-4 dark:text-zinc-400' }),
                  'Edit'
                ]
              }),
              h(DropdownMenuSeparator, { class: 'dark:border-zinc-800' }),
              h(DropdownMenuItem, {
                onClick: () => confirmDelete(post),
                class: 'text-red-600 dark:text-red-500 dark:focus:bg-zinc-800 dark:focus:text-red-400 cursor-pointer'
              }, {
                default: () => [
                  h(Trash, { class: 'mr-2 h-4 w-4' }),
                  'Delete'
                ]
              })
            ]
          })
        ]
      });
    },
  },
];

const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const columnVisibility = ref<VisibilityState>({});
const rowSelection = ref({});
const expanded = ref<ExpandedState>({});

// Filter posts data to be compatible with the table
const postsData = props.posts.data;

// Initialize the table with the posts data
const table = useVueTable({
  get data() {
    return postsData;
  },
  columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
  onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
  onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
  onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
  onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
  state: {
    get sorting() { return sorting.value },
    get columnFilters() { return columnFilters.value },
    get columnVisibility() { return columnVisibility.value },
    get rowSelection() { return rowSelection.value },
    get expanded() { return expanded.value },
  },
});

// Function to navigate to next/previous pages using Laravel pagination with proper type safety
const goToPage = (url: string | null) => {
  if (url) {
    router.visit(url);
  }
};

// Add state for the post to be deleted
const postToDelete = ref<Post | null>(null);
const deleteDialog = ref<InstanceType<typeof DeleteConfirmationDialog> | null>(null);

// Function to confirm deletion of a post
const confirmDelete = (post: Post) => {
  postToDelete.value = post;
  deleteDialog.value?.showDialog();
};

// Handle confirmed deletion
const handleDeleteConfirm = () => {
  if (postToDelete.value) {
    router.delete(`/posts/${postToDelete.value.id}`);
  }
};
</script>

<template>
    <Head title="Posts" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Posts</h1>
                <Link href="/posts/create">
                    <Button variant="default" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create Post
                    </Button>
                </Link>
            </div>

            <div class="w-full">
                <div class="flex items-center py-4">
                  <Input
                    class="max-w-sm"
                    placeholder="Filter by title..."
                    :model-value="table.getColumn('title')?.getFilterValue() as string"
                    @update:model-value="table.getColumn('title')?.setFilterValue($event)"
                  />
                  <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                      <Button variant="outline" class="ml-auto">
                        Columns <ChevronDown class="ml-2 h-4 w-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuCheckboxItem
                        v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
                        :key="column.id"
                        class="capitalize"
                        :model-value="column.getIsVisible()"
                        @update:model-value="(value: boolean) => {
                          column.toggleVisibility(!!value)
                        }"
                      >
                        {{ column.id }}
                      </DropdownMenuCheckboxItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </div>
                <div class="rounded-md border">
                  <Table>
                    <TableHeader>
                      <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                          <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
                        </TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      <template v-if="table.getRowModel().rows?.length">
                        <template v-for="row in table.getRowModel().rows" :key="row.id">
                          <TableRow :data-state="row.getIsSelected() && 'selected'">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                              <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </TableCell>
                          </TableRow>
                        </template>
                      </template>

                      <TableRow v-else>
                        <TableCell
                          :colspan="columns.length"
                          class="h-24 text-center"
                        >
                          No posts found.
                        </TableCell>
                      </TableRow>
                    </TableBody>
                  </Table>
                </div>

                <!-- Laravel Pagination -->
                <div class="flex items-center justify-between space-x-2 py-4">
                  <div class="flex-1 text-sm text-muted-foreground">
                    Showing {{ props.posts.from || 0 }} to {{ props.posts.to || 0 }} of {{ props.posts.total || 0 }} posts
                  </div>
                  <div class="space-x-2">
                    <Button
                      variant="outline"
                      size="sm"
                      :disabled="!props.posts.prev_page_url"
                      @click="goToPage(props.posts.prev_page_url)"
                    >
                      Previous
                    </Button>
                    <Button
                      variant="outline"
                      size="sm"
                      :disabled="!props.posts.next_page_url"
                      @click="goToPage(props.posts.next_page_url)"
                    >
                      Next
                    </Button>
                  </div>
                </div>
              </div>
        </div>
    </AppLayout>

    <DeleteConfirmationDialog
      ref="deleteDialog"
      title="Delete Post"
      description="Are you sure you want to delete this post? This action cannot be undone and all content will be permanently removed."
      dangerButtonText="Delete Post"
      @confirm="handleDeleteConfirm"
    />
</template>
