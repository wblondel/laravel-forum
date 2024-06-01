<template>
  <AppLayout :title="thread.data.title">
    <Container>
      <h1 class="text-2xl font-bold">{{ thread.data.title }}</h1>
      <span class="block mt-1 text-sm text-gray-600">Published {{ formattedDate }} by {{ thread.data.first_post.user.name }}</span>

      <article class="mt-6">
        <pre class="whitespace-pre-wrap">{{ thread.data.first_post.body }}</pre>
      </article>

      <div class="mt-12">
        <h2 class="text-xl font-semibold">Posts</h2>

        <form v-if="$page.props.auth.user" @submit.prevent="addPost" class="mt-4">
          <div>
            <InputLabel for="body" class="sr-only">Post</InputLabel>
            <TextArea id="body" v-model="postForm.body" rows="4" placeholder="What's on your mind?" />
            <InputError :message="postForm.errors.body" class="mt-1"></InputError>
          </div>

          <PrimaryButton type="submit" class="mt-3" :disabled="postForm.processing">Add Post</PrimaryButton>
        </form>

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
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {useForm} from "@inertiajs/vue3";
import TextArea from "@/Components/TextArea.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps(['thread', 'posts']);

const formattedDate = computed(() => relativeDate(props.thread.data.first_post.created_at));

const postForm = useForm({
  body: '',
});

const addPost = () => postForm.post(route('threads.posts.store', props.thread.data.id), {
  preserveScroll: true,
  onSuccess: () => postForm.reset(),
});
</script>

