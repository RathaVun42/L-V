<template>
    <div class="p-4 sm:p-6">

        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Products
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage food products for your canteen.
                </p>
            </div>

            <AppButton @click="openCreateModal">
                + Add Product
            </AppButton>
        </div>

        <!-- Loading -->
        <AppLoading v-if="productStore.loading" text="Loading products..." />

        <!-- Error -->
        <div v-else-if="error" class="rounded-xl border border-red-200 bg-red-50 p-5">
            <p class="font-medium text-red-700">
                Failed to load products.
            </p>

            <p class="mt-1 text-sm text-red-600">
                Please try again.
            </p>

            <AppButton class="mt-4" variant="danger" size="sm" @click="fetchData">
                Try Again
            </AppButton>
        </div>

        <!-- Empty -->
        <AppEmptyState v-else-if="productStore.products.length === 0" title="No products found"
            description="Create your first food product.">
            <template #action>
                <AppButton @click="openCreateModal">
                    + Add Product
                </AppButton>
            </template>
        </AppEmptyState>

        <!-- Products -->
        <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <ProductCard v-for="product in productStore.products" :key="product.id" :product="product"
                @edit="openEditModal" @delete="deleteProductItem" />
        </div>

        <!-- Product Form Modal -->
        <AppModal :open="showModal" :title="editingProduct ? 'Edit Product' : 'Create Product'" @close="closeModal">
            <ProductForm :product="editingProduct" :categories="categoryOptions" :loading="saving" :errors="formErrors"
                @submit="handleSubmit" @cancel="closeModal" />
        </AppModal>

        <!-- Success Modal -->
        <AppModal :open="successSubmission.isOpen" :title="successSubmission.title" @close="closeSuccessModal">
            <p class="text-sm text-gray-600">
                {{ successSubmission.message }}
            </p>

            <template #footer>
                <AppButton @click="closeSuccessModal">
                    OK
                </AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { getCategories } from '@/services/category'
import AppButton from '@/components/ui/AppButton.vue'
import AppModal from '@/components/ui/AppModal.vue'
import AppLoading from '@/components/ui/AppLoading.vue'
import AppEmptyState from '@/components/ui/AppEmptyState.vue'
import ProductCard from '@/components/products/ProductCard.vue'
import ProductForm from '@/components/products/ProductForm.vue'
import { useProductStore } from '@/stores/productStore'

const productStore = useProductStore()

const categories = ref([])

const error = ref(null)
const saving = ref(false)

const showModal = ref(false)
const editingProduct = ref(null)
const formErrors = ref({})

const successSubmission = reactive({
    isOpen: false,
    title: '',
    message: '',
})

/*
|--------------------------------------------------------------------------
| Category options
|--------------------------------------------------------------------------
*/

const categoryOptions = computed(() => {
    return categories.value.map(category => ({
        value: category.id,
        label: category.name,
    }))
})

/*
|--------------------------------------------------------------------------
| Load data
|--------------------------------------------------------------------------
*/

async function fetchData() {
    error.value = null

    try {
        await Promise.all([
            productStore.fetchProducts(),
            fetchCategories(),
        ])
    } catch (err) {
        console.error('Failed to load products:', err)
        error.value = err
    }
}

async function fetchCategories() {
    categories.value = await getCategories()
}

/*
|--------------------------------------------------------------------------
| Modal
|--------------------------------------------------------------------------
*/

function openCreateModal() {
    editingProduct.value = null
    formErrors.value = {}
    showModal.value = true
}

function openEditModal(product) {
    editingProduct.value = product
    formErrors.value = {}
    showModal.value = true
}

function closeModal() {
    if (saving.value) {
        return
    }

    showModal.value = false
    editingProduct.value = null
    formErrors.value = {}
}

/*
|--------------------------------------------------------------------------
| Create / Update
|--------------------------------------------------------------------------
*/

async function handleSubmit(data) {
    saving.value = true
    formErrors.value = {}

    try {
        if (editingProduct.value) {
            await productStore.editProduct(
                editingProduct.value.id,
                data
            )

            showSuccess(
                'Product Updated',
                'The product was updated successfully.'
            )
        } else {
            await productStore.addProduct(data)

            showSuccess(
                'Product Created',
                'The product was created successfully.'
            )
        }

        closeModal()
    } catch (err) {
        console.error('Failed to save product:', err)

        handleValidationErrors(err)
    } finally {
        saving.value = false
    }
}

/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

async function deleteProductItem(product) {
    const confirmed = window.confirm(
        `Are you sure you want to delete "${product.name}"?`
    )

    if (!confirmed) {
        return
    }

    try {
        await productStore.removeProduct(product.id)

        showSuccess(
            'Product Deleted',
            `"${product.name}" was deleted successfully.`
        )
    } catch (err) {
        console.error('Failed to delete product:', err)
        if (err.response?.status === 409) {
            const errors = err.response.data.message ?? {}
            Object.assign(successSubmission, {
                isOpen: true,
                message: errors,
                title: 'Oops'
            })
            return
        }

        error.value = err
    }
}

/*
|--------------------------------------------------------------------------
| Validation errors
|--------------------------------------------------------------------------
*/

function handleValidationErrors(err) {
    if (err.response?.status === 422) {
        const errors = err.response.data.errors ?? {}
        formErrors.value = {
            name: errors.name?.[0] ?? '',
            category_id: errors.category_id?.[0] ?? '',
            description: errors.description?.[0] ?? '',
            image: errors.image?.[0] ?? '',
            is_active: errors.is_active?.[0] ?? '',
        }
        return
    }
    if (err.response?.status === 409) {
        const errors = err.response.data.message ?? {}
        Object.assign(successSubmission, {
            isOpen: true,
            message: errors,
            title: 'Oops'
        })
        return
    }
    error.value = err.response?.message
}

/*
|--------------------------------------------------------------------------
| Success
|--------------------------------------------------------------------------
*/

function showSuccess(title, message) {
    successSubmission.isOpen = true
    successSubmission.title = title
    successSubmission.message = message
}

function closeSuccessModal() {
    successSubmission.isOpen = false
    showModal.value = false
}

onMounted(() => {
    fetchData()
})
</script>