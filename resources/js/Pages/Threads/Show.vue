<template>
  <AppLayout :title="thread.data.title">
    <Container>
      <h1 class="text-2xl font-bold">{{ thread.data.title }}</h1>
      <span v-if="thread.data.first_post" class="block mt-1 text-sm text-gray-600">Published {{ formattedDate }} by {{ thread.data.first_post.user.name }}</span>

      <article class="mt-6">
        <pre class="whitespace-pre-wrap">{{ thread.data.first_post?.body }}</pre>
      </article>

      <div class="mt-12">
        <h2 class="text-xl font-semibold">Posts</h2>

        <form v-if="$page.props.auth.user" @submit.prevent="() => postIdBeingEdited ? updatePost() : addPost()" class="mt-4">
          <div>
            <InputLabel for="body" class="sr-only">Post</InputLabel>
            <TextArea ref="postTextAreaRef" id="body" v-model="postForm.body" rows="4" placeholder="What's on your mind?" minlength="100" maxlength="10000"/>
            <InputError :message="postForm.errors.body" class="mt-1"></InputError>
          </div>

          <PrimaryButton type="submit" class="mt-3" :disabled="postForm.processing" v-text="postIdBeingEdited ? 'Update Post' : 'Add Post'"></PrimaryButton>
          <SecondaryButton v-if="postIdBeingEdited" @click="cancelEditPost" class="ml-2">Cancel</SecondaryButton>
        </form>

        <ul class="divide-y mt-4">
          <li v-for="post in posts.data" :key="post.id" class="px-2 py-4">
            <Post @edit="editPost" @delete="deletePost" :post="post" :postIdBeingEdited="postIdBeingEdited"></Post>
          </li>
        </ul>

        <Pagination :meta="posts.meta" :only="['posts','jetstream']"/>
      </div>
    </Container>

  </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import {computed, ref} from "vue";
import {relativeDate} from "@/Utilities/date.js"
import Pagination from "@/Components/Pagination.vue";
import Post from "@/Components/Post.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {router, useForm} from "@inertiajs/vue3";
import TextArea from "@/Components/TextArea.vue";
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {useConfirm} from "@/Utilities/Composables/useConfirm.js";

const props = defineProps(['thread', 'posts']);

const formattedDate = computed(() => relativeDate(props.thread.data.first_post.created_at));

const postForm = useForm({
  body: '',
});

const postTextAreaRef = ref(null);
const postIdBeingEdited = ref(null);
const postBeingEdited = computed(() => props.posts.data.find(post => post.id === postIdBeingEdited.value));

const editPost = (postId) => {
  postIdBeingEdited.value = postId;
  postForm.body = postBeingEdited.value?.body;
  postTextAreaRef.value?.focus();
};

const cancelEditPost = () => {
  postIdBeingEdited.value = null;
  postForm.reset();
};

const addPost = () => postForm.post(route('threads.posts.store', {
  thread: props.thread.data.id
}), {
  preserveScroll: true,
  onSuccess: () => postForm.reset(),
});

const { confirmation } = useConfirm();

const updatePost = async () => {
  if (! await confirmation('Are you sure you want to update this post?')) {
    setTimeout(() => postTextAreaRef.value?.focus(), 201);
    return;
  }

  postForm.put(route('posts.update', {
    post: postIdBeingEdited.value,
    page: props.posts.meta.current_page
  }), {
    preserveScroll: true,
    onSuccess: cancelEditPost
  });
};

const deletePost = async (postId) => {
  if (! await confirmation('Are you sure you want to delete this post?')) {
    return;
  }

  router.delete(route('posts.destroy', {
    post: postId,
    page: props.posts.meta.current_page
  }), {
    preserveScroll: true
  });
}
</script>

