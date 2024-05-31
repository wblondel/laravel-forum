<template>
  <AppLayout :title="thread.data.title">
    <Container>
      <h1 class="text-2xl font-bold">{{ thread.data.title }}</h1>
      <span class="block mt-1 text-sm text-gray-600">Published {{ formattedDate }} by {{ thread.data.first_post.user.name }}</span>

      <article class="mt-6">
        <pre class="whitespace-pre-wrap">{{ thread.data.first_post.body }}</pre>
      </article>

      <div class="mt-12">
        <h2 class="text-xl font-semibold">Comments</h2>

        <ul class="divide-y mt-4">
          <li v-for="post in posts.data" :key="post.id" class="px-2 py-4">
            <Post :post="post"></Post>
          </li>
        </ul>

        <Pagination :meta="posts.meta" :only="['posts']"/>
      </div>
    </Container>

  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import {computed} from "vue";
import {relativeDate} from "@/Utilities/date.js"
import Pagination from "@/Components/Pagination.vue";
import Post from "@/Components/Post.vue";

const props = defineProps(['thread', 'posts']);

const formattedDate = computed(() => relativeDate(props.thread.data.first_post.created_at));
</script>

