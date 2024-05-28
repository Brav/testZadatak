<script setup>

import { onMounted, ref, computed } from 'vue';

let tvModels = ref([])
let pagination = ref([])

onMounted(async () => {
    const response = await fetch("api/");

    let data = await response.json();

    tvModels = data.tvs
    pagination = data.pagination

    console.log(pagination)

})


</script>

<template>



</template>

<style scoped>

</style>
