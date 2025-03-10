<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { useToast } from '@/components/ui/toast';

interface Role {
  id: number;
  name: string;
}

defineProps<{
  roles: Role[];
}>();

const { toast } = useToast();
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  roles: [] as number[],
});

function handleSubmit() {
  form.post(route('users.store'), {
    onSuccess: () => {
      toast({
        title: 'Success',
        description: 'User created successfully',
        variant: 'success',
      });
    },
    onError: () => {
      toast({
        title: 'Error',
        description: 'Failed to create user. Please check the form for errors.',
        variant: 'destructive',
      });
    }
  });
}

function toggleRole(roleId: number) {
  const index = form.roles.indexOf(roleId);
  if (index === -1) {
    form.roles.push(roleId);
  } else {
    form.roles.splice(index, 1);
  }
}

function isRoleSelected(roleId: number): boolean {
  return form.roles.includes(roleId);
}
</script>

<template>
  <Head title="Create User" />

  <DefaultLayout>
    <div class="container mx-auto py-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Create User</h1>
        <Link :href="route('users.index')">
          <Button variant="outline">Back to Users</Button>
        </Link>
      </div>

      <Card class="w-full max-w-2xl mx-auto">
        <CardHeader>
          <CardTitle>User Information</CardTitle>
          <CardDescription>Create a new user account with assigned roles.</CardDescription>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <div class="space-y-2">
              <Label for="name" :class="{ 'text-destructive': form.errors.name }">Name</Label>
              <Input
                id="name"
                v-model="form.name"
                :class="{ 'border-destructive': form.errors.name }"
                placeholder="Enter name"
              />
              <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
            </div>

            <div class="space-y-2">
              <Label for="email" :class="{ 'text-destructive': form.errors.email }">Email</Label>
              <Input
                id="email"
                type="email"
                v-model="form.email"
                :class="{ 'border-destructive': form.errors.email }"
                placeholder="Enter email"
              />
              <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>

            <div class="space-y-2">
              <Label for="password" :class="{ 'text-destructive': form.errors.password }">Password</Label>
              <Input
                id="password"
                type="password"
                v-model="form.password"
                :class="{ 'border-destructive': form.errors.password }"
                placeholder="Enter password"
              />
              <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>

            <div class="space-y-2">
              <Label for="password_confirmation">Confirm Password</Label>
              <Input
                id="password_confirmation"
                type="password"
                v-model="form.password_confirmation"
                placeholder="Confirm password"
              />
            </div>

            <div class="space-y-3">
              <Label>Assign Roles</Label>
              <div class="grid grid-cols-2 gap-4">
                <div v-for="role in roles" :key="role.id" class="flex items-center space-x-2">
                  <Checkbox
                    :id="`role-${role.id}`"
                    :checked="isRoleSelected(role.id)"
                    @update:checked="() => toggleRole(role.id)"
                  />
                  <Label :for="`role-${role.id}`" class="cursor-pointer">{{ role.name }}</Label>
                </div>
              </div>
              <p v-if="form.errors.roles" class="text-sm text-destructive">{{ form.errors.roles }}</p>
            </div>
          </form>
        </CardContent>

        <CardFooter class="flex justify-end space-x-2">
          <Link :href="route('users.index')">
            <Button variant="outline">Cancel</Button>
          </Link>
          <Button
            type="submit"
            :disabled="form.processing"
            @click="handleSubmit"
          >
            Create User
          </Button>
        </CardFooter>
      </Card>
    </div>
  </DefaultLayout>
</template>
