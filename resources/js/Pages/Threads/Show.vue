<template>
  <AppLayout>
    <Container>
      {{ thread }}
    </Container>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import Pagination from "@/Components/Pagination.vue";

defineProps(['thread'])

</script>

