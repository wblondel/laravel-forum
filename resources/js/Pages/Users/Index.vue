<template>
  <AppLayout>
    <Container>
      <table class="table-auto">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Created at</th>
            <th>Threads</th>
            <th>Count</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="user in users.data" :key="user.id">
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.created_at }}</td>
            <td class="text-right"><span v-for="thread in user.threads">{{ thread.id }}, </span></td>
            <td class="text-right">{{ user.threads?.length ?? 0 }}</td>
          </tr>
        </tbody>

        <tfoot>
          <tr>
            <th colspan="4" class="text-right">Threads count</th>
            <th class="text-right">{{ threadsCount }}</th>
          </tr>
        </tfoot>
      </table>

      <Pagination :meta="users.meta" />
    </Container>
  </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import Pagination from "@/Components/Pagination.vue";

import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

defineProps(['users'])

const threadsCount = computed(() => {
  return usePage().props.users.data.reduce((total, user) => {
    return total + (user.threads ? user.threads.length : 0);
  }, 0);
});

</script>

