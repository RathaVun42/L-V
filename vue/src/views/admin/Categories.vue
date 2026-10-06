<template>
    <div class="p-4 sm:p-6">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Categories
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage food categories for your canteen.
                </p>
            </div>

            <AppButton @click="openCreateModal">
                + Add Category
            </AppButton>
        </div>

        <!-- Loading -->
        <AppLoading v-if="loading" text="Loading categories..." />

        <!-- Empty -->
        <AppEmptyState v-else-if="categories.length === 0" title="No categories found"
            description="Create your first food category.">
            <template #action>
                <AppButton @click="openCreateModal">
                    + Add Category
                </AppButton>
            </template>
        </AppEmptyState>

        <!-- Categories -->
        <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <CategoryCard v-for="category in categories" :key="category.id" :category="category" @edit="openEditModal"
                @delete="deleteCategoryItem" />

        </div>

        <!-- Create / Edit Modal -->
        <AppModal :open="showModal" :title="editingCategory ? 'Edit Category' : 'Create Category'" @close="closeModal">
            <CategoryForm :category="editingCategory" :loading="saving" :errors="formErrors" @submit="handleSubmit"
                @cancel="closeModal" />
        </AppModal>
        <AppModal :open="submissionResult.isOpen" :title="submissionResult.title" @close="closeSubmission">
            {{ submissionResult.message }}
        </AppModal>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'

import AppButton from '@/components/ui/AppButton.vue'
import AppModal from '@/components/ui/AppModal.vue'
import AppLoading from '@/components/ui/AppLoading.vue'
import AppEmptyState from '@/components/ui/AppEmptyState.vue'
import CategoryCard from '@/components/categories/CategoryCard.vue'

import CategoryForm from '@/components/categories/CategoryForm.vue'

import {
    getCategories,
    createCategory,
    updateCategory,
    deleteCategory,
} from '@/services/category'

const categories = ref([])

const loading = ref(false)
const saving = ref(false)

const showModal = ref(false)

const editingCategory = ref(null)

const formErrors = ref({})

const submissionResult = reactive({
    isOpen: false,
    title: '',
    message: ''
})


async function fetchCategories() {
    loading.value = true

    try {
        const response = await getCategories()
        categories.value = response
    } catch (error) {
        console.error('Failed to load categories:', error)
    } finally {
        loading.value = false
    }
}

function closeSubmission() {
    Object.assign(submissionResult, {
        isOpen: false,
        title: '',
        message: '',
    })
    showModal.value = false
}


function openCreateModal() {
    editingCategory.value = null
    formErrors.value = {}
    showModal.value = true
}


function openEditModal(category) {
    console.log('EDIT CATEGORY:', category)
    editingCategory.value = category
    formErrors.value = {}
    showModal.value = true
}


function closeModal() {
    if (saving.value) {
        return
    }

    showModal.value = false
    editingCategory.value = null
    formErrors.value = {}
}


async function handleSubmit(data) {
    saving.value = true
    formErrors.value = {}

    try {
        if (editingCategory.value) {
            const update = await updateCategory(
                editingCategory.value.id,
                data
            )
            if (update.status == 200) {
                Object.assign(submissionResult, {
                    isOpen: true,
                    title: 'Update',
                    message: 'Category updated successfully',
                })
            }
        } else {
            const create = await createCategory(data)
            if (create.status = 201) {
                Object.assign(submissionResult, {
                    isOpen: true,
                    title: 'Create',
                    message: 'Category created successfully',
                })
            }
        }

        closeModal()

        await fetchCategories()
    } catch (error) {
        console.error('Failed to save category:', error)

        if (error.response?.status === 422) {
            formErrors.value = error.response.data.errors ?? {}
        }
    } finally {
        saving.value = false
    }
}


async function deleteCategoryItem(category) {
    const confirmed = window.confirm(
        `Are you sure you want to delete "${category.name}"?`
    )

    if (!confirmed) {
        return
    }

    try {
        await deleteCategory(category.id)

        categories.value = categories.value.filter(
            item => item.id !== category.id
        )
    } catch (error) {
        if (error.response?.status == 404) {
            Object.assign(submissionResult, {
                isOpen: true,
                title: 'Submition',
                message: 'Item not found'
            })
        }
        if (error.response?.status == 409) {
            Object.assign(submissionResult, {
                isOpen: true,
                title: 'Submition',
                message: error.response?.data?.message
            })
        }


        console.error('Failed to delete category:', error)
    }
}


onMounted(() => {
    fetchCategories()

})
</script>