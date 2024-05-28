<script setup>

import { onMounted, ref, computed } from 'vue';

let categories = ref([])
let tvModels = ref([])
let pagination = ref([])
let currentCategoryID = ref(null)

onMounted(async () => {

    axios.get('api/categories')
        .then(response => {
            categories.value = response.data.categories
            currentCategoryID.value = categories.value[0].id
        })
        .catch(error => {
            console.log(error)
        })

    axios.get('api/items')
        .then(response => {
            tvModels.value = response.data.tvs
            pagination.value = response.data.pagination
        })
        .catch(error => {
            console.log(error)
        })

})

let tvLink = (link) => {
    return `https://www.shoptok.si/${link}`
}

let tvImage = (image) => {
    return `storage/${image}`
}

let paginationLink = (page, pagination) => {
    return pagination.current_page === page ? `javascript:void(0)` : `api/items/${currentCategoryID.value}?page=${page}`
}

let categoryLink = (categoryID) => {
    return `api/items/${categoryID}`
}

let getCategory = (link, categoryID) => {
    axios.get(link)
        .then(response => {
            tvModels.value = response.data.tvs
            pagination.value = response.data.pagination
            currentCategoryID.value = categoryID
        })
        .catch(error => {
            console.log(error)
        })
}

let getPagination = (link) => {
    axios.get(link)
        .then(response => {
            tvModels.value = response.data.tvs
            pagination.value = response.data.pagination
        })
        .catch(error => {
            console.log(error)
        })
}

let paginationBackLink = computed(() => `api/items?page=${pagination.current_page - 1}`)
let paginationNextLink = computed(() => `api/items?page=${pagination.current_page + 1}`)

</script>



<template>
    <div class="container mx-auto flex p-5">
        <div class="w-1/4">
            <ul class="menu">
                <li v-for="category in categories"
                    class="menu-title capitalize"
                    :class="category.id === currentCategoryID ? 'bg-primary text-white' : ''"
                    :key="category.id"
                ><a
                    @click.prevent="getCategory(categoryLink(category.id), category.id)"
                    :href="categoryLink(category.id)"
                >{{ category.name }}</a></li>
            </ul>
        </div>
        <div class="w-3/4">

            <div class="divider divider-primary">TV Models ({{ pagination.total }})</div>
            <div class="grid grid-cols-3 gap-2 justify-between size-fit">



                <div class=" p-4"
                     v-for="tv in tvModels">
                    <div class="card bg-base-100 shadow-xl">
                        <figure class="px-10 pt-10">
                            <img :src="tvImage(tv.image)" />
                        </figure>
                        <div class="card-body items-center text-center">

                            <div class="card-title text-blue-900 font-normal">
                                {{ tv.price }}
                            </div>

                            <h2 class="card-title gap-0.5">
                                {{ tv.brand }}
                                <span class="font-normal" v-html="tv.name"></span>
                            </h2>
                            <div class="card-actions justify-end">
                                <a class="btn btn-primary" :href="tvLink(tv.link)">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="preview flex min-h-[6rem] min-w-[18rem] max-w-4xl flex-wrap items-center justify-center gap-2 overflow-x-hidden bg-cover bg-top p-4 ">

                <a class="join-item btn"
                   :href="paginationBackLink"
                   v-if="pagination.current_page < 1"><</a>

                <button class="join-item btn"
                        :class="{'btn-disabled': pagination.current_page === 1}"> < </button>

                <a class="join-item"
                   v-for="page in pagination.last_page"
                   @click.prevent="getPagination(paginationLink(page, pagination))"
                   :href="paginationLink(page, pagination)"
                   :class="pagination.current_page === page ? 'btn-primary' : 'btn'"
                   >{{page}}</a>

                <a class="join-item btn"
                   :href="paginationNextLink"
                   v-if="pagination.current_page !== pagination.last_page"> > </a>

                <button class="join-item btn"
                        :class="{'btn-disabled': pagination.current_page === pagination.last_page}"> > </button>

            </div>


        </div>
    </div>
</template>

<style scoped>

</style>
