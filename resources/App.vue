<script setup>

import { onMounted, ref, computed } from 'vue';

let categories = ref([])
let tvModels = ref([])
let pagination = ref([])
let currentCategory = ref(null)

onMounted(async () => {

    axios.get('api/categories')
        .then(response => {
            categories.value = response.data.categories
            currentCategory.value = categories.value[0]

            console.log(categories.value)
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
    return pagination.current_page === page ? `javascript:void(0)` : `api/items/${currentCategory.value.id}?page=${page}`
}

let categoryLink = (categoryID) => {
    return `api/items/${categoryID}`
}

let getCategory = (link, category) => {

    if(category.id === currentCategory.value.id) return

    axios.get(link)
        .then(response => {
            tvModels.value = response.data.tvs
            pagination.value = response.data.pagination
            currentCategory.value = category
        })
        .catch(error => {
            console.log(error)
        })
}

let getPagination = (link) => {

    if(link === 'javascript:void(0)') return

    axios.get(link)
        .then(response => {
            tvModels.value = response.data.tvs
            pagination.value = response.data.pagination
        })
        .catch(error => {
            console.log(error)
        })
}


let paginationArray = computed(() => {
    let pages = [];
    let startPage = Math.max(pagination.value.current_page - 2, 1);
    let endPage = Math.min(startPage + 2, pagination.value.last_page);

    if (endPage - startPage < 2) {
        startPage = Math.max(endPage - 2, 1);
    }

    if (startPage > 1) {
        pages.push(1);
        pages.push('...');
    }

    for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
    }

    if (endPage < pagination.value.last_page) {
        pages.push('...');
        pages.push(pagination.value.last_page);
    }

    return pages;
});

let paginationBackLink = computed(() => `api/items?page=${pagination.value.current_page - 1}`)
let paginationNextLink = computed(() => `api/items?page=${pagination.value.current_page + 1}`)

</script>

<template>
    <div class="container mx-auto flex p-5">
        <div class="w-1/4">
            <ul class="menu">
                <li v-for="category in categories"
                    class="menu-title capitalize"
                    :class="category.id === currentCategory.id ? 'bg-primary text-white pointer-events-none' : ''"
                    :key="category.id"
                ><a
                    @click.prevent="getCategory(categoryLink(category.id), category)"
                    :href="categoryLink(category.id)"
                >{{ category.name }}
                    <span class="badge badge-info float-end">{{ category.items_count }}</span>
                </a></li>
            </ul>
        </div>
        <div class="w-3/4">

            <div class="divider divider-primary uppercase">{{ currentCategory?.name }} ({{ pagination.total }})</div>
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

                            <h2 class="flex card-title items-center text-center justify-center">
                                <div class="text-lg">{{ tv.brand }}</div>
                                <div class="font-normal text-sm" v-html="tv.name"></div>
                            </h2>
                            <div class="card-actions justify-end">
                                <a class="btn btn-primary" :href="tvLink(tv.link)"
                                   target="_blank">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="preview flex min-h-[6rem] min-w-[18rem] max-w-4xl flex-wrap items-center justify-center gap-2 overflow-x-hidden bg-cover bg-top p-4 ">

                <a class="join-item btn"
                   @click.prevent="getPagination(paginationBackLink)"
                   :href="paginationBackLink"
                   v-if="pagination.current_page > 1"><</a>

                <button v-else
                        class="join-item btn"
                        :class="{'btn-disabled': pagination.current_page === 1}"> < </button>

                <div v-for="(page, index) in paginationArray" :key="index">
                    <a class="join-item"

                       v-if="page !== '...'"
                       @click.prevent="getPagination(paginationLink(page, pagination))"
                       :href="paginationLink(page, pagination)"
                       :class="pagination.current_page === page ? 'btn btn-primary pointer-events-none' : 'btn'"
                       >{{page}}</a>

                    <span v-else>{{ page }}</span>
                </div>

                <a class="join-item btn"
                   :href="paginationNextLink"
                   @click.prevent="getPagination(paginationNextLink)"
                   v-if="pagination.current_page !== pagination.last_page"> > </a>

                <button v-else
                        class="join-item btn"
                        :class="{'btn-disabled': pagination.current_page === pagination.last_page}"> > </button>

            </div>

        </div>
    </div>
</template>
