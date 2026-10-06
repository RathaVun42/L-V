<template>
    <div class="p-4 sm:p-6">

        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ isEditing ? 'Edit Daily Menu' : 'Create Daily Menu' }}
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    {{ isEditing
                        ? 'Update the products available for this day.'
                        : 'Choose the products available for the selected day.'
                    }}
                </p>
            </div>

            <AppButton :loading="saving" @click="saveMenu">
                {{ isEditing  ? 'Update Menu' : 'Save Menu' }}
            </AppButton>
        </div>
        <!-- Existing Menus -->
        <div class="mb-6 h-96 overflow-auto rounded-xl border border-gray-200 bg-white p-4 shadow-sm ">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    Existing Menus
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Select a menu to edit it.
                </p>
            </div>

            <AppLoading v-if="menusLoading" text="Loading menus..." />

            <AppEmptyState v-else-if="menus.length === 0" title="No menus yet"
                description="Create your first daily menu below." />

            <div v-else class="space-y-3">
                <div v-for="menu in menus" :key="menu.id"
                    class="flex items-center justify-between rounded-lg border border-gray-200 p-4">
                    <div>
                        <p class="font-medium text-gray-900">
                            {{ formatMenuDate(menu.date) }}
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ menu.categories.length }} categories ·
                            {{ menu.products.length }} products
                        </p>
                    </div>

                    <AppButton variant="secondary" @click="editExistingMenu(menu)">
                        {{ isTodayMenu(menu) ? 'edit' : 'view' }}
                    </AppButton>
                </div>
            </div>
        </div>
        <!-- Date -->
        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <AppInput v-model="menuDate" type="date" label="Menu Date" required />
        </div>

        <!-- Category Filter -->
        <div class="mb-6">
            <h2 class="mb-3 text-sm font-semibold text-gray-700">
                Filter by category
            </h2>

            <div class="flex gap-2 overflow-x-auto p-2 no-scrollbar">
                <Chip :active="selectedCategory === null" @click="selectedCategory = null">
                    All
                </Chip>

                <Chip v-for="category in categories" :key="category.id" :active="selectedCategory === category.id"
                    @click="selectedCategory = category.id">
                    {{ category.name }}
                </Chip>
            </div>
        </div>

        <!-- Products ref here is not the same ref() although they work together.
        but ref() will get teh actual DOM element that named the same it in ref(ref in element).
        
            ref="productsSection"
            ↓
            Find the JavaScript ref with this name
                    ↓
            const productsSection = ref(null)

            then productsSection.value now points to the actual <div> (this ref is similar to document.querySelector(...))

        we will use ref to interact with actual DOM instead of document.querySelector or others. those interactions are 
        productsSection.value.scrollIntoView(), productsSection.value.focus(), or productsSection.value.getBoundingClientRect().

        *remember name of ref in DOM element and ref() inside script must be the same.

        -->
        <div ref="productsSection" class="mb-6">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">
                    Products
                </h2>

                <span class="text-sm text-gray-500">
                    {{ selectedProducts.length }} selected
                </span>
            </div>

            <AppLoading v-if="loading" text="Loading products..." />

            <AppEmptyState v-else-if="filteredProducts.length === 0" title="No products found"
                description="There are no products in this category." />
            

            <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <MenuProductCard v-for="product in filteredProducts" :key="product.id" :product="product"
                    :selected="isProductSelected(product.id)" :price="getProductPrice(product.id)"
                    :available="getProductAvailability(product.id)" @toggle="toggleProduct" @update:price="
                        updateProductPrice(product.id, $event)
                        " @update:available="
                            updateProductAvailability(product.id, $event)
                            " />
            </div>
        </div>

        <!-- Selected summary -->
        <div v-if="selectedProducts.length" class="rounded-xl border border-green-200 bg-green-50 p-4">
            <h2 class="font-semibold text-green-900">
                Selected Products
            </h2>

            <div class="mt-3 space-y-2">
                <div v-for="item in selectedProducts" :key="item.product_id"
                    class="flex items-center justify-between text-sm">
                    <span class="text-green-900">
                        {{ getProductName(item.product_id) }}
                    </span>

                    <span class="font-medium text-green-800">
                        ${{ Number(item.price || 0).toFixed(2) }}
                    </span>
                </div>
            </div>
        </div>


        <!-- Success -->
        <AppModal :open="successSubmission.isOpen" :title="successSubmission.title" @close="closeSuccess">
            <p class="text-sm text-gray-600">
                {{ successSubmission.message }}
            </p>

            <template #footer>
                <AppButton @click="closeSuccess">
                    OK
                </AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup>
import { computed, nextTick, onMounted, reactive, ref, watch } from 'vue'

import AppButton from '@/components/ui/AppButton.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppLoading from '@/components/ui/AppLoading.vue'
import AppEmptyState from '@/components/ui/AppEmptyState.vue'
import AppModal from '@/components/ui/AppModal.vue'
import Chip from '@/components/ui/Chip.vue'
import MenuProductCard from '@/components/menus/MenuProductCard.vue'

import { getCategories } from '@/services/category'
import { getProducts } from '@/services/product'
import { useMenuStore } from '@/stores/menu'
import { storeToRefs } from 'pinia'

const productsSection = ref(null)
const menuStore = useMenuStore()
const menuDate = ref('')
const categories = ref([])
const products = ref([])
const saving = ref(false)
const loading = ref(false)
const { menus, loading: menuLaoding, error } = storeToRefs(menuStore)

const selectedCategory = ref(null)

/*
|--------------------------------------------------------------------------
| Products selected for this menu
|--------------------------------------------------------------------------
|
| Example:
|
| [
|     {
|         product_id: 1,
|         price: 2.50,
|         is_available: true
|     }
| ]
|
*/

const selectedProducts = ref([])
const selectedCategories = computed(() => {
    return [
        ...new Set( // ... is spread operator take values out of the set
            // normally set will give un an unduplicated array {1,2,4, ...}
            selectedProducts.value.map(item => {
                const product = products.value.find(
                    product => product.id === item.product_id
                )

                return product.category_id
            })
        )
    ]
})
const existingMenu = computed(() => {
    return menus.value.find(menu => {
        return menu.date.startsWith(menuDate.value)
    })
})

function isTodayMenu(menu) {
    const today = new Date()
        .toISOString()
        .split('T')[0]

    return menu.date.startsWith(today)
}

function loadExistingMenu() {
    if (!existingMenu.value) {
        selectedProducts.value = []
        return
    }

    selectedProducts.value = existingMenu.value.products.map(product => ({
        product_id: product.id,
        price: Number(product.price),
        is_available: product.is_available,
    }))
}
async function editExistingMenu(menu) {
    menuDate.value = menu.date.substring(0, 10)
    await nextTick() //which waits for Vue's DOM update.
    setTimeout(() => {
        productsSection.value?.scrollIntoView({
            behavior: 'smooth',
            block: 'start', // block is where the scroll should be stopped for our DOM(they can be start, center, or end. these will arrange the stop behaviour vertically)
        })
    }, 100)

}
function formatMenuDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}
const isEditing = computed(() => {
    return !!existingMenu.value
})

const successSubmission = reactive({
    isOpen: false,
    title: '',
    message: '',
})

/*
|--------------------------------------------------------------------------
| Filter products
|--------------------------------------------------------------------------
*/

const filteredProducts = computed(() => {
    if (selectedCategory.value === null) {
        return products.value
    }

    return products.value.filter(
        product => product.category_id === selectedCategory.value
    )
})

/*
|--------------------------------------------------------------------------
| Product selection
|--------------------------------------------------------------------------
*/

function isProductSelected(productId) {
    return selectedProducts.value.some(
        item => item.product_id === productId
    )
}

function toggleProduct(product) {
    const index = selectedProducts.value.findIndex(
        item => item.product_id === product.id
    )


    if (index !== -1) {
        selectedProducts.value.splice(index, 1) // slice is an array's method use to delete item at index with qty 1

        return
    }

    selectedProducts.value.push({
        product_id: product.id,
        price: 0,
        is_available: true,
    })
}

/*
|--------------------------------------------------------------------------
| Product settings
|--------------------------------------------------------------------------
*/

function getProductPrice(productId) {
    const item = selectedProducts.value.find(
        item => item.product_id === productId
    )

    return item?.price ?? ''
}

function getProductAvailability(productId) {
    const item = selectedProducts.value.find(
        item => item.product_id === productId
    )

    return item?.is_available ?? true
}

function updateProductPrice(productId, price) {
    const item = selectedProducts.value.find(
        item => item.product_id === productId
    )

    if (item) {
        item.price = price
    }
}

function updateProductAvailability(productId, available) {
    const item = selectedProducts.value.find(
        item => item.product_id === productId
    )

    if (item) {
        item.is_available = available
    }
}

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function getProductName(productId) {
    const product = products.value.find(
        product => product.id === productId
    )

    return product?.name ?? 'Unknown product'
}

/*
|--------------------------------------------------------------------------
| Load data
|--------------------------------------------------------------------------
*/

async function fetchData() {
    loading.value = true

    try {
        const [categoryData, productData] = await Promise.all([
            getCategories(),
            getProducts(),
        ])

        categories.value = categoryData
        products.value = productData
    } catch (error) {
        console.error('Failed to load menu data:', error)
    } finally {
        loading.value = false
    }
}

/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

async function saveMenu() {
    saving.value = true

    try {
        const data = {
            date: menuDate.value,
            categories: selectedCategories.value,
            products: selectedProducts.value,
        }

        if (existingMenu.value) {
            await menuStore.editMenu(existingMenu.value.id, data)

            Object.assign(successSubmission, {
                isOpen: true,
                title: 'Success',
                message: 'Menu updated successfully.',
            })
        } else {
            await menuStore.addMenu(data)

            Object.assign(successSubmission, {
                isOpen: true,
                title: 'Success',
                message: 'Menu created successfully.',
            })
        }
    } catch (error) {
        console.error('Failed to save menu:', error)

        const message =
            error.response?.data?.message ??
            'Failed to save menu.'

        Object.assign(successSubmission, {
            isOpen: true,
            title: 'Oops',
            message,
        })
    } finally {
        saving.value = false
    }
}

/*
|--------------------------------------------------------------------------
| Success modal
|--------------------------------------------------------------------------
*/

function closeSuccess() {
    successSubmission.isOpen = false
}

watch(existingMenu, (menu, old_existing_value) => { // menu here is the new value of existingMenu
    if (!menu) {
        selectedProducts.value = []
        return
    }

    loadExistingMenu()

})

onMounted(async () => {
    const today = new Date()

    menuDate.value = today
        .toISOString()
        .split('T')[0]


    await Promise.all([
        fetchData(),
        menuStore.fetchMenus()
    ])
})
</script>