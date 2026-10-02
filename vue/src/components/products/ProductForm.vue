<template>
    <form @submit.prevent="submit">
        <div class="space-y-5">

            <!-- Name -->
            <AppInput
                v-model="form.name"
                label="Product Name"
                placeholder="Enter product name"
                required
                :error="errors.name"
            />

            <!-- Category -->
            <AppSelect
                v-model="form.category_id"
                label="Category"
                placeholder="Select a category"
                :options="categories"
                required
                :error="errors.category_id"
            />

            <!-- Description -->
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Description
                </label>

                <textarea
                    v-model="form.description"
                    rows="4"
                    placeholder="Enter product description"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-100"
                ></textarea>

                <p
                    v-if="errors.description"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ errors.description }}
                </p>
            </div>

            <!-- Image -->
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Image
                </label>

                <!-- Preview -->
                <div
                    v-if="imagePreview"
                    class="mb-3 overflow-hidden rounded-lg border border-gray-200"
                >
                    <img
                        :src="imagePreview"
                        alt="Product preview"
                        class="h-48 w-full object-cover"
                    />
                </div>

                <input
                    id="product-image"
                    type="file"
                    accept="image/*"
                    class="block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-600 file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200"
                    @change="handleImageChange"
                />

                <p
                    v-if="errors.image"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ errors.image }}
                </p>
            </div>

            <!-- Active -->
            <div class="flex items-center gap-3">
                <input
                    id="product-is-active"
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-green-700 focus:ring-green-500"
                />

                <label
                    for="product-is-active"
                    class="text-sm font-medium text-gray-700"
                >
                    Active
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end gap-3">
            <AppButton
                type="button"
                variant="secondary"
                @click="emit('cancel')"
            >
                Cancel
            </AppButton>

            <AppButton
                type="submit"
                :loading="loading"
            >
                {{ editing ? 'Update Product' : 'Create Product' }}
            </AppButton>
        </div>
    </form>
</template>

<script setup>
import { computed, onBeforeUnmount, reactive, ref, watch } from 'vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppSelect from '@/components/ui/AppSelect.vue'
import AppButton from '@/components/ui/AppButton.vue'

const props = defineProps({
    product: {
        type: Object,
        default: null,
    },

    categories: {
        type: Array,
        default: () => [],
    },

    loading: {
        type: Boolean,
        default: false,
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

const emit = defineEmits(['submit', 'cancel'])

const editing = computed(() => !!props.product)

const form = reactive({
    name: '',
    category_id: '',
    description: '',
    image: null,
    is_active: true,
})

const imagePreview = ref(null)

let previewUrl = null

watch(
    () => props.product,
    (product) => {
        cleanupPreview()

        if (product) {
            form.name = product.name ?? ''
            form.category_id = product.category_id ?? ''
            form.description = product.description ?? ''
            form.image = null
            form.is_active = product.is_active ?? true

            imagePreview.value = product.image
                ?`${product.image}`
                : null
        } else {
            form.name = ''
            form.category_id = ''
            form.description = ''
            form.image = null
            form.is_active = true
            imagePreview.value = null
        }
    },
    { immediate: true }
)

function handleImageChange(event) {
    const file = event.target.files?.[0]

    if (!file) {
        return
    }

    form.image = file

    cleanupPreview()

    previewUrl = URL.createObjectURL(file)
    imagePreview.value = previewUrl
}

function cleanupPreview() {
    if (previewUrl) {
        URL.revokeObjectURL(previewUrl)
        previewUrl = null
    }
}

function submit() {
    emit('submit', {
        name: form.name,
        category_id: form.category_id,
        description: form.description,
        image: form.image,
        is_active: form.is_active,
    })
}

onBeforeUnmount(() => {
    cleanupPreview()
})
</script>