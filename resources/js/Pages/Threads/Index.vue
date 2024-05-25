<template>
  <AppLayout>
    <Container>
      <ul class="divide-y">
        <li v-for="thread in threads.data" :key="thread.id" class="px-2 py-4">
          <span class="font-bold text-lg">{{ thread.id }} - {{ thread.title }}</span>
          <div>Author: {{ thread.first_post.user.name }}</div>
          <div>First post by: {{ thread.first_post.user.name }} on {{ thread.first_post.created_at }}</div>
          <div>Latest post by: {{ thread.latest_post.user.name }} on {{ thread.latest_post.created_at }} </div>
          <div>Posts: {{ thread.posts_count }}</div>
        </li>
      </ul>

      <Pagination :meta="threads.meta" />
    </Container>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import Pagination from "@/Components/Pagination.vue";

defineProps(['threads'])

</script>

