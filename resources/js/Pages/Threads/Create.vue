<template>
  <AppLayout title="Create a Thread">
    <Container>
      <h1 class="text-2xl font-bold">Create a Thread</h1>

      <form @submit.prevent="createThread" class="mt-6">
        <div>
          <InputLabel for="title" class="sr-only">Title</InputLabel>
          <TextInput id="title" class="w-full" v-model="form.title" placeholder="Give it a great title..." />
          <InputError :message="form.errors.title" class="mt-1" />
        </div>

        <div class="mt-3">
          <InputLabel for="body" class="sr-only">Body</InputLabel>
          <TextArea id="body" v-model="form.body" rows="25" />
          <InputError :message="form.errors.body" class="mt-1" />
        </div>

        <div class="mt-3">
          <PrimaryButton type="submit">Create Thread</PrimaryButton>
        </div>
      </form>
    </Container>
  </AppLayout>
</template>

<script setup>
import {useForm} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue";

const form = useForm({
  title: '',
  body: '',
});

const createThread = () => form.post(route('threads.store'));
</script>

