<template>
  <AppLayout>
    <Container>
      <ul class="divide-y">
        <li v-for="thread in threads.data" :key="thread.id">
          <Link :href="thread.routes.show" class="block group px-2 py-4">
            <span class="font-bold text-lg group-hover:text-indigo-500">{{ thread.title }}</span>
            <span class="block pt-1 text-sm text-gray-600">{{ formattedDate(thread) }} by {{ thread.user.name ?? 'Deleted user' }}</span>

            <div class="mt-4" v-if="thread.latest_post">
              Latest post by: {{ thread.latest_post?.user.name ?? 'Deleted user' }} {{ formattedDate(thread.latest_post) }}
            </div>

            <div class="mt-4" v-if="hasPosts(thread)">
              Replies: {{ nbReplies(thread) }}
            </div>
          </Link>
        </li>
      </ul>

      <Pagination :meta="threads.meta"/>
    </Container>
  </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import Pagination from "@/Components/Pagination.vue";
import {Link} from "@inertiajs/vue3";
import { relativeDate } from "@/Utilities/date";

defineProps(['threads'])

const hasPosts = (thread) => thread.posts_count > 0;
const nbReplies = (thread) => thread.posts_count - 1;
const formattedDate = (thread) => relativeDate(thread.created_at);
</script>

