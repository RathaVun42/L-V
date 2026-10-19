
<script setup>
import { computed, onMounted, ref } from 'vue'
import { getPickupSlots } from '@/services/pickupSlots'
import SlotInfo from '@/components/pickupSlots/SlotInfo.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppLoading from '@/components/ui/AppLoading.vue'
import AppEmptyState from '@/components/ui/AppEmptyState.vue'
import AppInput from '@/components/ui/AppInput.vue'

function getToday() {
    const date = new Date()
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')

    return `${year}-${month}-${day}`
}

const selectedDate = ref(getToday())
const slots = ref([])
const loading = ref(false)
const error = ref('')

const totalSlots = computed(() => slots.value.length)

const totalCapacity = computed(() =>
    slots.value.reduce((sum, slot) => sum + Number(slot.capacity || 0), 0),
)

const totalBooked = computed(() =>
    slots.value.reduce((sum, slot) => sum + Number(slot.booked || 0), 0),
)

const availableSpaces = computed(() =>
    slots.value.reduce(
        (sum, slot) =>
            sum + Math.max(0, Number(slot.capacity || 0) - Number(slot.booked || 0)),
        0,
    ),
)

function slotRemaining(slot) {
    return Math.max(
        0,
        Number(slot.capacity || 0) - Number(slot.booked || 0),
    )
}

function slotStatus(slot) {
    if (slot.is_active === false || slot.is_active === 0) {
        return 'Inactive'
    }

    if (slotRemaining(slot) === 0) {
        return 'Full'
    }

    return 'Available'
}

function statusVariant(status) {
    switch (status) {
        case 'Available':
            return 'success'
        case 'Full':
            return 'danger'
        case 'Inactive':
            return 'neutral'
        default:
            return 'neutral'
    }
}

async function loadSlots() {
    if (!selectedDate.value) {
        error.value = 'Please select a date.'
        return
    }

    loading.value = true
    error.value = ''

    try {
        const result = await getPickupSlots(selectedDate.value)

        // Supports either a direct array or an object containing slots.
        slots.value = Array.isArray(result)
            ? result
            : result?.slots ?? []
    } catch (err) {
        slots.value = []
        error.value =
            err.response?.data?.message ||
            'Unable to load pickup slots. Please try again.'
    } finally {
        loading.value = false
    }
}

function selectToday() {
    selectedDate.value = getToday()
    loadSlots()
}

function selectTomorrow() {
    const date = new Date()
    date.setDate(date.getDate() + 1)

    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')

    selectedDate.value = `${year}-${month}-${day}`
    loadSlots()
}

onMounted(loadSlots)
</script>

<template>
    <main class="min-w-0 space-y-6 p-4 sm:p-6 lg:p-8">
        <!-- Page heading -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="min-w-0">
                <h1 class="text-2xl font-bold text-gray-900">
                    Pickup Slots
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    View pickup times, capacity, and remaining spaces.
                </p>
            </div>

            <RouterLink
                :to="{name: 'pickup-settings'}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
                <i class="fa-solid fa-gear mr-2" aria-hidden="true"></i>
                Pickup Settings
            </RouterLink>
        </div>

        <!-- Date selection -->
        <section
            class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end"
        >
            <div class="min-w-0 flex-1">
                <label
                    for="pickup-date"
                    class="mb-2 block text-sm font-medium text-gray-700"
                >
                    Select pickup date
                </label>

                <AppInput id="pickup-date" v-model="selectedDate" type="date"></AppInput>
            </div>

            <div class="flex flex-wrap gap-2">
                <AppButton
                    type="button"
                    variant="secondary"
                    @click="selectToday"
                >
                    Today
                </AppButton>

                <AppButton
                    type="button"
                    variant="secondary"
                    @click="selectTomorrow"
                >
                    Tomorrow
                </AppButton>

                <AppButton
                    type="button"
                    :disabled="loading"
                    @click="loadSlots"
                >
                    <i
                        class="fa-solid fa-rotate mr-2"
                        aria-hidden="true"
                    ></i>
                    Load Slots
                </AppButton>
            </div>
        </section>

        <!-- Error message -->
        <div
            v-if="error"
            role="alert"
            class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
        >
            <i
                class="fa-solid fa-circle-exclamation mt-0.5"
                aria-hidden="true"
            ></i>

            <div class="min-w-0 flex-1">
                {{ error }}
            </div>

            <button
                type="button"
                class="font-medium underline"
                @click="loadSlots"
            >
                Retry
            </button>
        </div>

        <!-- Summary cards -->
        <section>
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    Daily Overview
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Summary for {{ selectedDate || 'the selected date' }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <SlotInfo
                    title="Total Slots"
                    :total="totalSlots"
                    description="Pickup time slots"
                    icon="fa-solid fa-calendar-check"
                />

                <SlotInfo
                    title="Total Capacity"
                    :total="totalCapacity"
                    description="Maximum orders accepted"
                    icon="fa-solid fa-users"
                />

                <SlotInfo
                    title="Total Booked"
                    :total="totalBooked"
                    description="Orders currently booked"
                    icon="fa-solid fa-bag-shopping"
                />

                <SlotInfo
                    title="Available Spaces"
                    :total="availableSpaces"
                    description="Remaining order capacity"
                    icon="fa-solid fa-clock"
                />
            </div>
        </section>

        <!-- Slot table -->
        <section
            class="min-w-0 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
        >
            <div
                class="flex flex-col gap-2 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Slot Schedule
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Pickup capacity and availability by time.
                    </p>
                </div>

                <span class="text-sm text-gray-500">
                    {{ totalSlots }} slots
                </span>
            </div>

            <div v-if="loading" class="p-10">
                <AppLoading />
            </div>

            <AppEmptyState
                v-else-if="!error && slots.length === 0"
                title="No pickup slots"
                description="No slots are available for this date. Check your pickup settings and try again."
            />

            <div v-else-if="slots.length > 0" class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-5 py-4 font-semibold">#</th>
                            <th class="px-5 py-4 font-semibold">Pickup Time</th>
                            <th class="px-5 py-4 font-semibold">Capacity</th>
                            <th class="px-5 py-4 font-semibold">Booked</th>
                            <th class="px-5 py-4 font-semibold">Remaining</th>
                            <th class="px-5 py-4 font-semibold">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="(slot, index) in slots"
                            :key="slot.id ?? `${slot.start_time}-${index}`"
                            class="transition hover:bg-gray-50"
                        >
                            <td class="px-5 py-4 text-gray-500">
                                {{ index + 1 }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 font-medium text-gray-900">
                                {{ slot.start_time }} – {{ slot.end_time }}
                            </td>

                            <td class="px-5 py-4 text-gray-700">
                                {{ slot.capacity }}
                            </td>

                            <td class="px-5 py-4 text-gray-700">
                                {{ slot.booked ?? 0 }}
                            </td>

                            <td class="px-5 py-4 font-medium text-gray-900">
                                {{ slotRemaining(slot) }}
                            </td>

                            <td class="px-5 py-4">
                                <AppBadge
                                    :label="slotStatus(slot)"
                                    :variant="statusVariant(slotStatus(slot))"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</template>
