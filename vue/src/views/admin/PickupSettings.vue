<template>
    <div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6">
        <!-- Page heading -->
        <div>
            <h1 >
                Pickup Settings
            </h1>
            <p >
                Configure pickup hours, slot capacity, and operating days.
            </p>
        </div>

        <!-- Loading -->
         <AppLoading v-if="loading">
            
        </AppLoading>
        <!-- <div v-if="loading" class="rounded-xl border border-gray-200 bg-white p-8 text-center text-gray-500">
            Loading pickup settings...
        </div> -->
        
        <form v-else class="space-y-6">
            <!-- Pickup time and capacity -->
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-5">
                    <h2 >
                        Pickup Schedule
                    </h2>
                    <p>
                        Define when customers can collect their orders.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="min-w-0">
                        <AppInput id="pickup_start_time" v-model="form.pickup_start_time" type="time"
                            label="Pickup start time" :required="true" />
                    </div >
                    <div class="min-w-0">
                        <AppInput id="pickup_end_time" v-model="form.pickup_end_time" type="time"
                            label="Pickup end time" :required="true" />
                    </div>
                    <div>
                
                        <AppInput id="slot_duration" v-model="form.slot_duration" type="number" 
                            label="Slot duration (minutes)" :required="true" />

                        <p>
                            How long each pickup window lasts.
                        </p>
                    </div>
                    <div>
                        <AppInput id="slot_capacity" v-model="form.slot_capacity" type="number" 
                            label="Orders per slot" :required="true" />
                        <p>
                            Maximum orders allowed in each slot.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Operating days -->
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-5">
                    <h2 >
                        Operating Days
                    </h2>
                    <p >
                        Select the days when customers can collect orders.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <label v-for="day in weekdays" :key="day.value"
                        class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition" :class="form[day.value]
                            ? 'border-emerald-500 bg-emerald-50'
                            : 'border-gray-200 hover:bg-gray-50'">
                        <input v-model="form[day.value]" type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 accent-emerald-600" />

                        <span class="text-sm font-medium text-gray-700">
                            {{ day.label }}
                        </span>
                    </label>
                </div>
            </section>
            
            <AppModal title="Oops" :open="isError" @close="errorMessage = ''">
                <p>{{ errorMessage }}</p>
            </AppModal>
            <AppModal title="Success" :open="!!successMessage" @close="successMessage = ''">
                <p>{{ successMessage }}</p>
            </AppModal>

            <!-- Save button -->
            <div class="flex justify-end">
                <!-- <button type="submit" :disabled="saving"
                    class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                    {{ saving ? 'Saving...' : 'Save Settings' }}
                </button> -->
                <AppButton type="submit" :disabled="saving" :loading="saving" @click="saveSettings" >
                    Save
                </AppButton>
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { usePickupSettingsStore } from '@/stores/pickupSettings'
import AppInput from '@/components/ui/AppInput.vue'
import AppModal from '@/components/ui/AppModal.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppLoading from '@/components/ui/AppLoading.vue'

const store = usePickupSettingsStore()

const loading = ref(true)
const saving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const isError = computed(()=>!!errorMessage.value)

const weekdays = [
    { label: 'Monday', value: 'monday' },
    { label: 'Tuesday', value: 'tuesday' },
    { label: 'Wednesday', value: 'wednesday' },
    { label: 'Thursday', value: 'thursday' },
    { label: 'Friday', value: 'friday' },
    { label: 'Saturday', value: 'saturday' },
    { label: 'Sunday', value: 'sunday' },
]

const form = reactive({
    pickup_start_time: '08:00',
    pickup_end_time: '17:00',
    slot_duration: 15,
    slot_capacity: 10,
    monday: true,
    tuesday: true,
    wednesday: true,
    thursday: true,
    friday: true,
    saturday: false,
    sunday: false,
})

function fillForm(settings) {
    form.pickup_start_time =
        settings.pickup_start_time?.slice(0, 5) ?? '08:00'

    form.pickup_end_time =
        settings.pickup_end_time?.slice(0, 5) ?? '17:00'

    form.slot_duration = Number(settings.slot_duration ?? 15)
    form.slot_capacity = Number(settings.slot_capacity ?? 10)

    for (const day of weekdays) {
        form[day.value] = Boolean(Number(settings[day.value]))
    }
}

async function loadSettings() {
    loading.value = true
    errorMessage.value = ''

    try {
        const settings = await store.fetchSettings()
        fillForm(settings)
    } catch (error) {
        errorMessage.value =
            store.error || 'Unable to load pickup settings.'
    } finally {
        loading.value = false
    }
}

async function saveSettings(event) {
    event.preventDefault()
    successMessage.value = ''
    errorMessage.value = ''

    if (form.pickup_start_time >= form.pickup_end_time) {
        errorMessage.value =
            'Pickup end time must be later than pickup start time.'
        return
    }

    if (
        !Number.isInteger(form.slot_duration) ||
        form.slot_duration < 1 ||
        !Number.isInteger(form.slot_capacity) ||
        form.slot_capacity < 1
    ) {
        errorMessage.value =
            'Slot duration and capacity must be positive whole numbers.'
        return
    }

    if (!weekdays.some(day => form[day.value])) {
        errorMessage.value =
            'Select at least one operating day.'
        return
    }

    saving.value = true

    try {
        await store.saveSettings({ ...form })

        successMessage.value = 'Pickup settings saved successfully.'
    } catch (error) {
        errorMessage.value =
            store.error || 'Unable to save pickup settings.'
    } finally {
        saving.value = false
    }
}

onMounted(loadSettings)
</script>
