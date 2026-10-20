<template>
  <div class="min-h-screen bg-emerald-50">

    <!-- Mobile overlay -->
    <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0"
      enter-to-class="opacity-100" leave-active-class="transition-opacity duration-200" leave-from-class="opacity-100"
      leave-to-class="opacity-0">
      <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/40 lg:hidden" @click="sidebarOpen = false"></div>
    </Transition>


    <!-- Sidebar -->
    <aside class="
        fixed left-0 top-0 z-50
        h-screen w-64
        bg-emerald-500 text-white shadow-lg
        transform transition-transform duration-300 ease-in-out
        lg:translate-x-0
      " :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

      <!-- Logo / Header -->
      <div class="
          flex h-16 items-center justify-between
          border-b border-emerald-400
          px-5
        ">
        <h1 class="text-xl font-bold">
          My Admin
        </h1>

        <!-- Close button - mobile only -->
        <button type="button" class="
            rounded-lg p-2
            text-emerald-100
            hover:bg-emerald-600
            lg:hidden
          " @click="sidebarOpen = false">
          ✕
        </button>
      </div>


      <!-- User profile -->
      <div class="border-b border-emerald-400 px-5 py-4">

        <RouterLink :to="{ name: 'profile' }" class="flex items-center gap-3" @click="closeSidebarOnMobile">

          <img :src="userImage" alt="Profile" class="
              h-11 w-11
              rounded-full
              object-cover
              ring-2 ring-white/40
            ">

          <div class="min-w-0">
            <p class="truncate text-sm font-semibold">
              {{ store.name || 'User' }}
            </p>

            <p class="truncate text-xs text-emerald-100">
              {{ isAdmin ? 'Administrator' : store.role }}
            </p>
          </div>

        </RouterLink>

      </div>


      <!-- Navigation -->
      <nav class="mt-5 space-y-1 px-3">

        <RouterLink v-for="item in navigationItems" :key="item.label" :to="item.to" class="nav-link"
          active-class="nav-link-active" @click="closeSidebarOnMobile">
          {{ item.label }}
        </RouterLink>

      </nav>

    </aside>


    <!-- Main area -->
    <div class="lg:ml-64">

      <!-- Navbar -->
      <header class="
          sticky top-0 z-30
          flex h-16 items-center justify-between
          border-b border-emerald-100
          bg-white
          px-4 sm:px-6
          shadow-sm
        ">

        <!-- Left side -->
        <div class="flex items-center gap-3">

          <!-- Hamburger -->
          <button type="button" class="
              rounded-lg p-2
              text-emerald-700
              hover:bg-emerald-50
              lg:hidden
            " @click="sidebarOpen = true">
            ☰
          </button>

          <h2 class="text-lg font-semibold text-emerald-700">
            Admin Panel
          </h2>

        </div>


        <!-- Logout -->
        <button type="button" class="
        rounded-lg
        bg-emerald-500
        px-3 py-2
        text-sm font-medium text-white
        transition
        hover:bg-emerald-600
        sm:px-4
    " @click="showLogoutModal = true">
          <span class="hidden sm:inline">Logout</span>
          <span class="sm:hidden">↪</span>
        </button>

      </header>


      <!-- Changing content -->
      <main class="p-4 sm:p-6">
        <RouterView />
      </main>

    </div>

  </div>
  <AppModal :open="showLogoutModal" title="Log out" @close="showLogoutModal = false">
    <p>Do you want to log out?</p>
    <template #footer>
      <AppButton @click="confirmLogOut">
        OK
      </AppButton>
    </template>
  </AppModal>

</template>


<script setup>
import { userStore } from '@/stores/user';
import { storeToRefs } from 'pinia';
import { computed, ref } from 'vue';
import AppModal from '@/components/ui/AppModal.vue';
import { useRouter } from 'vue-router';
import AppButton from '@/components/ui/AppButton.vue';
const route = useRouter();
const store = userStore();
// Keep using your existing isAdmin value.
// This assumes isAdmin is already available in this component.
// Temporary — we'll replace this with Spatie-based user data later.
const isAdmin = store.$state.is_admin;

const commonLinks = [
  
]

const adminLinks = [
  { label: 'Dashboard', to: '/dashboard/index' },
  { label: 'Products', to: { name: 'product' } },
  { label: 'Register New Staff', to: '/register' },
  { label: 'Category', to: { name: 'category' } },
  { label: 'Menus', to: { name: 'menus' } },
  { label: 'Users', to: '/users' },
  { label: 'Pickup Settings', to: '/dashboard/pickup/settings' },
  {label: 'Pickup slots', to: '/dashboard/pickup/slots'}
]

const navigationItems = computed(() => [
  ...commonLinks,
  ...(isAdmin ? adminLinks : []),
])


const showLogoutModal = ref(false)

const { profile_image } = storeToRefs(store);

const sidebarOpen = ref(false);

const userImage = computed(() => {
  return profile_image.value || '/images/users/emptyuser.png';
});
function confirmLogOut() {
  showLogoutModal.value = false
  route.push('/logout')
}

const closeSidebarOnMobile = () => {
  sidebarOpen.value = false;
};
</script>