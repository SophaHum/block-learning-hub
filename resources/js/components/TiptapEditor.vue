<template>
  <div class="tiptap-editor">
    <div class="border rounded-md">
      <div class="border-b p-2 flex flex-wrap gap-1 bg-muted/50">
        <div class="flex gap-1">
          <Toggle
            :pressed="editor?.isActive('heading', { level: 1 }) || false"
            @update:pressed="() => editor?.chain().focus().toggleHeading({ level: 1 }).run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleHeading({ level: 1 }).run()"
          >
            <Heading1 class="h-4 w-4" />
          </Toggle>

          <Toggle
            :pressed="editor?.isActive('heading', { level: 2 }) || false"
            @update:pressed="() => editor?.chain().focus().toggleHeading({ level: 2 }).run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleHeading({ level: 2 }).run()"
          >
            <Heading2 class="h-4 w-4" />
          </Toggle>

          <Toggle
            :pressed="editor?.isActive('heading', { level: 3 }) || false"
            @update:pressed="() => editor?.chain().focus().toggleHeading({ level: 3 }).run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleHeading({ level: 3 }).run()"
          >
            <Heading3 class="h-4 w-4" />
          </Toggle>
        </div>

        <Separator orientation="vertical" class="h-6" />

        <div class="flex gap-1">
          <Toggle
            :pressed="editor?.isActive('bold') || false"
            @update:pressed="() => editor?.chain().focus().toggleBold().run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleBold().run()"
          >
            <Bold class="h-4 w-4" />
          </Toggle>

          <Toggle
            :pressed="editor?.isActive('italic') || false"
            @update:pressed="() => editor?.chain().focus().toggleItalic().run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleItalic().run()"
          >
            <Italic class="h-4 w-4" />
          </Toggle>

          <Toggle
            :pressed="editor?.isActive('strike') || false"
            @update:pressed="() => editor?.chain().focus().toggleStrike().run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleStrike().run()"
          >
            <Strikethrough class="h-4 w-4" />
          </Toggle>

          <Toggle
            :pressed="editor?.isActive('code') || false"
            @update:pressed="() => editor?.chain().focus().toggleCode().run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleCode().run()"
          >
            <Code class="h-4 w-4" />
          </Toggle>
        </div>

        <Separator orientation="vertical" class="h-6" />

        <div class="flex gap-1">
          <Toggle
            :pressed="editor?.isActive('bulletList') || false"
            @update:pressed="() => editor?.chain().focus().toggleBulletList().run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleBulletList().run()"
          >
            <List class="h-4 w-4" />
          </Toggle>

          <Toggle
            :pressed="editor?.isActive('orderedList') || false"
            @update:pressed="() => editor?.chain().focus().toggleOrderedList().run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleOrderedList().run()"
          >
            <ListOrdered class="h-4 w-4" />
          </Toggle>

          <Toggle
            :pressed="editor?.isActive('taskList') || false"
            @update:pressed="() => editor?.chain().focus().toggleTaskList().run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleTaskList().run()"
          >
            <CheckSquare class="h-4 w-4" />
          </Toggle>
        </div>

        <Separator orientation="vertical" class="h-6" />

        <div class="flex gap-1">
          <Toggle
            :pressed="editor?.isActive('blockquote') || false"
            @update:pressed="() => editor?.chain().focus().toggleBlockquote().run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleBlockquote().run()"
          >
            <Quote class="h-4 w-4" />
          </Toggle>

          <Toggle
            :pressed="editor?.isActive('codeBlock') || false"
            @update:pressed="() => editor?.chain().focus().toggleCodeBlock().run()"
            size="sm"
            :disabled="!editor?.can().chain().focus().toggleCodeBlock().run()"
          >
            <FileCode class="h-4 w-4" />
          </Toggle>

          <Dialog>
            <DialogTrigger as-child>
              <Button variant="ghost" class="h-8 w-8 p-0" size="sm">
                <Link class="h-4 w-4" />
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Insert Link</DialogTitle>
                <DialogDescription>
                  Add a link to your content
                </DialogDescription>
              </DialogHeader>
              <div class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                  <Label for="link-url" class="text-right">URL</Label>
                  <Input id="link-url" v-model="linkUrl" class="col-span-3" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                  <Label for="link-text" class="text-right">Text</Label>
                  <Input id="link-text" v-model="linkText" class="col-span-3" />
                </div>
              </div>
              <DialogFooter>
                <Button @click="insertLink">Insert Link</Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>

          <Dialog>
            <DialogTrigger as-child>
              <Button variant="ghost" class="h-8 w-8 p-0" size="sm">
                <ImageIcon class="h-4 w-4" />
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Insert Image</DialogTitle>
                <DialogDescription>
                  Add an image to your content
                </DialogDescription>
              </DialogHeader>
              <div class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                  <Label for="image-url" class="text-right">URL</Label>
                  <Input id="image-url" v-model="imageUrl" class="col-span-3" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                  <Label for="image-alt" class="text-right">Alt Text</Label>
                  <Input id="image-alt" v-model="imageAlt" class="col-span-3" />
                </div>
              </div>
              <DialogFooter>
                <Button @click="insertImage">Insert Image</Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>

          <Dialog>
            <DialogTrigger as-child>
              <Button variant="ghost" class="h-8 w-8 p-0" size="sm">
                <Table class="h-4 w-4" />
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Insert Table</DialogTitle>
                <DialogDescription>
                  Add a table to your content
                </DialogDescription>
              </DialogHeader>
              <div class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                  <Label for="table-rows" class="text-right">Rows</Label>
                  <Input id="table-rows" v-model.number="tableRows" type="number" min="1" max="10" class="col-span-3" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                  <Label for="table-cols" class="text-right">Columns</Label>
                  <Input id="table-cols" v-model.number="tableCols" type="number" min="1" max="10" class="col-span-3" />
                </div>
              </div>
              <DialogFooter>
                <Button @click="insertTable">Insert Table</Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>
        </div>

        <Separator orientation="vertical" class="h-6" />

        <div class="flex gap-1">
          <Button
            variant="ghost"
            size="sm"
            class="h-8 w-8 p-0"
            @click="editor?.chain().focus().undo().run()"
            :disabled="!editor?.can().chain().focus().undo().run()"
          >
            <Undo class="h-4 w-4" />
          </Button>

          <Button
            variant="ghost"
            size="sm"
            class="h-8 w-8 p-0"
            @click="editor?.chain().focus().redo().run()"
            :disabled="!editor?.can().chain().focus().redo().run()"
          >
            <Redo class="h-4 w-4" />
          </Button>
        </div>
      </div>

      <div class="relative min-h-[200px] max-h-[600px] overflow-y-auto p-4">
        <EditorContent :editor="editor" class="prose prose-sm max-w-none" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onBeforeUnmount, computed } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Placeholder from '@tiptap/extension-placeholder'
import TiptapLink from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import Table from '@tiptap/extension-table'
import TableRow from '@tiptap/extension-table-row'
import TableCell from '@tiptap/extension-table-cell'
import TableHeader from '@tiptap/extension-table-header'
import TaskList from '@tiptap/extension-task-list'
import TaskItem from '@tiptap/extension-task-item'
import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight'
import { createLowlight } from 'lowlight'
import javascript from 'highlight.js/lib/languages/javascript'
import python from 'highlight.js/lib/languages/python'
import php from 'highlight.js/lib/languages/php'
import bash from 'highlight.js/lib/languages/bash'
import css from 'highlight.js/lib/languages/css'
import { Toggle } from '../components/ui/toggle'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Bold,
  Italic,
  Strikethrough,
  Heading1,
  Heading2,
  Heading3,
  List,
  ListOrdered,
  Quote,
  Code,
  FileCode,
  Link,
  Image as ImageIcon,
  CheckSquare,
  Undo,
  Redo
} from 'lucide-vue-next'

// Initialize lowlight with languages
const lowlight = createLowlight()
lowlight.register('javascript', javascript)
lowlight.register('js', javascript)
lowlight.register('python', python)
lowlight.register('php', php)
lowlight.register('bash', bash)
lowlight.register('css', css)

const props = defineProps<{
  modelValue: string
  placeholder?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

// Link dialog state
const linkUrl = ref('')
const linkText = ref('')

// Image dialog state
const imageUrl = ref('')
const imageAlt = ref('')

// Table dialog state
const tableRows = ref(3)
const tableCols = ref(3)

const content = computed(() => props.modelValue || '')

// Initialize the editor
const editor = useEditor({
  content: content.value,
  extensions: [
    StarterKit.configure({
      heading: {
        levels: [1, 2, 3]
      },
      codeBlock: false // We'll use CodeBlockLowlight instead
    }),
    Placeholder.configure({
      placeholder: props.placeholder || 'Write something...',
    }),
    TiptapLink.configure({
      openOnClick: false,
      HTMLAttributes: {
        class: 'text-primary underline'
      }
    }),
    Image,
    Table.configure({
      resizable: true,
    }),
    TableRow,
    TableHeader,
    TableCell,
    TaskList,
    TaskItem.configure({
      nested: true,
    }),
    CodeBlockLowlight.configure({
      lowlight,
      defaultLanguage: 'javascript',
      HTMLAttributes: {
        class: 'hljs rounded-md overflow-hidden'
      }
    })
  ],
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
  }
})

// Handle inserting a link
const insertLink = () => {
  if (linkUrl.value) {
    editor.value?.chain().focus().extendMarkRange('link').setLink({ href: linkUrl.value }).run()

    // Clear the form
    linkUrl.value = ''
    linkText.value = ''
  }
}

// Handle inserting an image
const insertImage = () => {
  if (imageUrl.value) {
    editor.value?.chain().focus().setImage({ src: imageUrl.value, alt: imageAlt.value }).run()

    // Clear the form
    imageUrl.value = ''
    imageAlt.value = ''
  }
}

// Handle inserting a table
const insertTable = () => {
  editor.value?.chain().focus().insertTable({ rows: tableRows.value, cols: tableCols.value }).run()

  // Reset values to defaults
  tableRows.value = 3
  tableCols.value = 3
}

// Sync content when modelValue changes
watch(() => props.modelValue, (newContent) => {
  const currentContent = editor.value?.getHTML()

  if (newContent !== currentContent) {
    editor.value?.commands.setContent(newContent, false)
  }
})

// Clean up
onBeforeUnmount(() => {
  editor.value?.destroy()
})
</script>

<style>
/* Add required styles for the editor here */
.tiptap-editor .ProseMirror:focus {
  outline: none;
}

.tiptap-editor .ProseMirror p.is-editor-empty:first-child::before {
  color: #adb5bd;
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
}

/* Styles for task lists */
.tiptap-editor ul[data-type="taskList"] {
  list-style: none;
  padding: 0;
}

.tiptap-editor ul[data-type="taskList"] li {
  display: flex;
  align-items: flex-start;
  margin-bottom: 0.5em;
}

.tiptap-editor ul[data-type="taskList"] li > label {
  margin-right: 0.5em;
  user-select: none;
}

.tiptap-editor ul[data-type="taskList"] li > div {
  flex: 1;
}

/* Table styles */
.tiptap-editor table {
  border-collapse: collapse;
  margin: 0;
  overflow: hidden;
  table-layout: fixed;
  width: 100%;
}

.tiptap-editor table td,
.tiptap-editor table th {
  border: 2px solid #ced4da;
  box-sizing: border-box;
  min-width: 1em;
  padding: 0.5em 1em;
  position: relative;
  vertical-align: top;
}

.tiptap-editor table th {
  background-color: #f8f9fa;
  font-weight: bold;
  text-align: left;
}

/* Code block styles */
.tiptap-editor pre {
  background: #0d1117;
  color: #e6edf3;
  font-family: 'JetBrainsMono', monospace;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
}

.tiptap-editor pre code {
  color: inherit;
  padding: 0;
  background: none;
  font-size: 0.875rem;
}

.tiptap-editor pre .hljs-comment,
.tiptap-editor pre .hljs-quote {
  color: #8b949e;
}

.tiptap-editor pre .hljs-keyword,
.tiptap-editor pre .hljs-selector-tag {
  color: #ff7b72;
}

.tiptap-editor pre .hljs-string,
.tiptap-editor pre .hljs-attr {
  color: #a5d6ff;
}

.tiptap-editor pre .hljs-title,
.tiptap-editor pre .hljs-function .hljs-title {
  color: #d2a8ff;
}

.tiptap-editor pre .hljs-variable,
.tiptap-editor pre .hljs-template-variable {
  color: #ffa657;
}

.tiptap-editor pre .hljs-number {
  color: #a5d6ff;
}
</style>
