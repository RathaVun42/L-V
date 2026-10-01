<template>
    <form @submit.prevent="submit">
        <div class="space-y-5">

            <!-- Name -->
            <AppInput
                v-model="form.name"
                label="Name"
                placeholder="Enter category name"
                required
                :error="errors.name"
            />

            <!-- Description -->
            <div>
                <label
                    for="description"
                    class="mb-2 block text-sm font-medium text-gray-700"
                >
                    Description
                </label>

                <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    placeholder="Enter category description"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition placeholder:text-gray-400 focus:border-green-600 focus:ring-2 focus:ring-green-100"
                ></textarea>

                <p
                    v-if="errors.description"
                    class="mt-1 text-sm text-red-500"
                >
                    {{ errors.description }}
                </p>
            </div>

            <!-- Image -->
            <div>
                <label
                    for="image"
                    class="mb-2 block text-sm font-medium text-gray-700"
                >
                    Image
                </label>

                <!-- Preview -->
                <div
                    v-if="imagePreview"
                    class="mb-3"
                >
                    <img
                        :src="imagePreview"
                        alt="Category preview"
                        class="h-32 w-32 rounded-xl border border-gray-200 object-cover"
                    />
                </div>

                <input
                    id="image"
                    type="file"
                    accept="image/*"
                    class="block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-600 file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200"
                    @change="handleImageChange"
                />

                <p
                    v-if="errors.image"
                    class="mt-1 text-sm text-red-500"
                >
                    {{ errors.image }}
                </p>
            </div>

            <!-- Active -->
            <div class="flex items-center gap-3">
                <input
                    id="is_active"
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                />

                <label
                    for="is_active"
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
                {{ editing ? 'Update Category' : 'Create Category' }}
            </AppButton>
        </div>
    </form>
</template>

<script setup>
import { computed, reactive, ref, watch, onBeforeUnmount } from 'vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppButton from '@/components/ui/AppButton.vue'
const img_url = import.meta.env.VITE_IMG_URL
const props = defineProps({
    category: {
        type: Object,
        default: null,
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

const emit = defineEmits([
    'submit',
    'cancel',
])

const editing = computed(() => {
    return !!props.category
})

const form = reactive({
    name: '',
    description: '',
    image: null,
    is_active: true,
})

const imagePreview = ref(null)

let previewUrl = null

watch(
    () => props.category,
    (category) => {
        cleanupPreview()

        if (category) {
            form.name = category.name ?? ''
            form.description = category.description ?? ''
            form.image = img_url + category.image
            form.is_active = category.is_active ?? true

            imagePreview.value = img_url + category.image ?? null
        } else {
            form.name = ''
            form.description = ''
            form.image = null
            form.is_active = true
            imagePreview.value = null
        }
    },
    {
        immediate: true,
    }
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
        description: form.description,
        image: form.image,
        is_active: form.is_active,
    })
}

onBeforeUnmount(() => {
    cleanupPreview()
})
</script>