<template>
  <AppLayout>
    <Container>
      <ul class="divide-y">
        <li v-for="thread in threads" :key="thread.id" class="px-2 py-4">
          <span class="font-bold text-lg">{{ thread.title }}</span>
        </li>
      </ul>
    </Container>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";

defineProps(['threads'])
</script>

