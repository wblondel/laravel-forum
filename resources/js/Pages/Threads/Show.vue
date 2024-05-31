<template>
  <AppLayout :title="thread.data.title">
    <Container>
      <h1 class="text-2xl font-bold">{{ thread.data.title }}</h1>
      <span class="block mt-1 text-sm text-gray-600">Published {{ formattedDate }} by {{ thread.data.first_post.user.name }}</span>

      <article class="mt-6">
        <pre class="whitespace-pre-wrap">{{ thread.data.first_post.body }}</pre>
      </article>
    </Container>

  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import {computed} from "vue";
import {formatDistance, parseISO} from "date-fns";

const props = defineProps(['thread']);

const formattedDate = computed(() => formatDistance(
    parseISO(props.thread.data.first_post.created_at),
    new Date(),
    {addSuffix: true}
));
</script>

