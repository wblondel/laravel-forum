<template>
  <AppLayout>
    <Container>
      <ul class="divide-y">
        <li v-for="thread in threads.data" :key="thread.id">
          <Link :href="route('threads.show', thread.id)" class="block group px-2 py-4">
            <span class="font-bold text-lg group-hover:text-indigo-500">{{ thread.title }}</span>
            <span class="block pt-1 text-sm text-gray-600">{{
                formattedDate(thread.first_post)
              }} by {{ thread.first_post?.user.name ?? 'Unknown' }}</span>

            <br>
            <div v-if="thread.first_post">First post by: {{ thread.first_post?.user.name ?? 'Deleted user' }} on
              {{ thread.first_post.created_at }}
            </div>
            <div v-if="thread.latest_post">Latest post by: {{ thread.latest_post?.user.name ?? 'Deleted user' }} on
              {{ thread.latest_post.created_at }}
            </div>
            <div>Posts: {{ thread.posts_count }}</div>
          </Link>
        </li>
      </ul>

      <Pagination :meta="threads.meta"/>
    </Container>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import Pagination from "@/Components/Pagination.vue";
import {Link} from "@inertiajs/vue3";
import {formatDistance, parseISO} from "date-fns";

defineProps(['threads'])

const formattedDate = (thread) => {
  return formatDistance(
      parseISO(thread.created_at),
      new Date(),
      {addSuffix: true}
  )
}
</script>

