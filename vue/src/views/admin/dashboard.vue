<template>
    <div class="space-y-6">
        <AppInput v-model="name" label="Test Input" placeholder="Type something..." />

        <div class="flex gap-3">
            <AppButton>
                Save
            </AppButton>

            <AppButton variant="secondary">
                Cancel
            </AppButton>

            <AppButton variant="danger">
                Delete
            </AppButton>
            <AppBadge dot size="md">
            </AppBadge>

        </div>
        <AppLoading>

        </AppLoading>
        <AppEmptyState title="No categories found" description="Create your first category to get started.">
            <template #action>
                <AppButton @click="openCreateModal">
                    Add Category
                </AppButton>
            </template>
        </AppEmptyState>


        <p>
            Value: {{ name }}
        </p>
    </div>
    <AppModal :open="isOpen" title="test" @close="onClose">
        <template #footer>
            <h1>
                This is footer
            </h1>
        </template>
        This is App model
    </AppModal>
    <AppSelect v-model="status" label="Status" placeholder="Select status" :options="statusOptions" required="true" />
</template>
<script setup>
import AppButton from '@/components/ui/AppButton.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppModal from '@/components/ui/AppModal.vue'
import AppSelect from '@/components/ui/AppSelect.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppLoading from '@/components/ui/AppLoading.vue'
import AppEmptyState from '@/components/ui/AppEmptyState.vue'
import { ref } from 'vue'

const name = ref('')
const loading = ref(false)
const isOpen = ref(true)
const onClose = () => {
    isOpen.value = false
}
const status = ref('')

const statusOptions = [
    {
        value: 'active',
        label: 'Active',
    },
    {
        value: 'inactive',
        label: 'Inactive',
    },
]
</script>