<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Trash } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
  title?: string;
  description?: string;
  dangerButtonText?: string;
  cancelButtonText?: string;
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Delete Confirmation',
  description: 'Are you sure you want to delete this item? This action cannot be undone.',
  dangerButtonText: 'Delete',
  cancelButtonText: 'Cancel',
});

const emit = defineEmits(['confirm', 'cancel']);

const open = ref(false);

const showDialog = () => {
  open.value = true;
};

const handleConfirm = () => {
  emit('confirm');
  open.value = false;
};

const handleCancel = () => {
  emit('cancel');
  open.value = false;
};

// Expose the showDialog method to the parent component
defineExpose({ showDialog });
</script>

<template>
  <Dialog :open="open" @update:open="open = $event">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>{{ title }}</DialogTitle>
        <DialogDescription>
          {{ description }}
        </DialogDescription>
      </DialogHeader>
      <DialogFooter class="flex space-x-2 justify-end">
        <Button variant="outline" @click="handleCancel">
          {{ cancelButtonText }}
        </Button>
        <Button variant="destructive" @click="handleConfirm" class="bg-red-600 hover:bg-red-700">
          <Trash class="mr-2 h-4 w-4" />
          {{ dangerButtonText }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>

  <!-- Hidden slot to allow parent component to trigger the dialog -->
  <slot></slot>
</template>
