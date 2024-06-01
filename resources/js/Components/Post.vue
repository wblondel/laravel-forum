<template>
  <div class="sm:flex">
    <div class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4">
      <img :src="post.user.profile_photo_url" class="h-10 w-10 rounded-full" />
    </div>
    <div class="flex-1">
      <p class="mt-1 break-all">{{ post.body }}</p>
      <span class="first-letter:uppercase block pt-1 text-xs text-gray-600">By {{ post.user.name }} {{ relativeDate(post.created_at) }}</span>
      <div class="mt-2 text-right empty:hidden">
        <form v-if="post.can?.delete" @submit.prevent="$emit('delete', post.id)">
          <button class="font-mono text-red-700 text-xs hover:font-semibold">Delete</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {relativeDate} from "@/Utilities/date.js";
import {router, usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps(['post']);

const emit = defineEmits(['delete']);
</script>