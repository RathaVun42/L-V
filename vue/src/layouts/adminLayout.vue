<!-- layouts/AdminLayout.vue -->
<template>
  <div class="min-h-screen bg-emerald-50">

    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-screen w-64 bg-emerald-500 text-white shadow-lg">
      <!-- Logo -->
      <div class="flex h-16 items-center px-6 border-b border-emerald-400 justify-around">
        <h1 class="text-xl font-bold">
          My Admin
        </h1> 
        <RouterLink :to="{name: 'profile'}">
          <img :src="`http://localhost:8000/storage/${userImage}`" alt="" class="w-12 h-12 rounded-full object-cover">
        </RouterLink>
        
      </div>

      <!-- Navigation -->
      <nav class="mt-6 px-3 space-y-2">

        <RouterLink to="/dashboard/index" class="block rounded-lg px-4 py-3 transition hover:bg-emerald-600"
          active-class="bg-emerald-700">
          Dashboard
        </RouterLink>

        <RouterLink v-if="isAdimn" to="/register" class="block rounded-lg px-4 py-3 transition hover:bg-emerald-600"
          active-class="bg-emerald-700">
          Regist new staff
        </RouterLink>

        <RouterLink to="/products" class="block rounded-lg px-4 py-3 transition hover:bg-emerald-600"
          active-class="bg-emerald-700">
          Products
        </RouterLink>

        <RouterLink to="/users" class="block rounded-lg px-4 py-3 transition hover:bg-emerald-600"
          active-class="bg-emerald-700">
          Users
        </RouterLink>

      </nav>
    </aside>


    <!-- Main area -->
    <div class="ml-64">

      <!-- Navbar -->
      <header class="sticky top-0 z-10 flex h-16 items-center justify-between
               bg-white px-6 shadow-sm">
        <h2 class="text-lg font-semibold text-emerald-700">
          Admin Panel
        </h2>

        <!-- <button @click="logout" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm
                 font-medium text-white transition hover:bg-emerald-600">
          Logout
        </button> -->
        <RouterLink to="/logout" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm
                 font-medium text-white transition hover:bg-emerald-600">
          logout
        </RouterLink>
      </header>


      <!-- Changing content -->
      <main class="p-6">
        <RouterView />
      </main>

    </div>

  </div>
</template>
<script setup>
  import { useRouter } from 'vue-router';
  import { userStore } from '@/stores/user';
  const store = userStore();
  const Image = store.$state.profile_image
  const isAdimn = store.$state.is_admin
  const userImage = Image ?? 'images/users/emptyuser.png'
  const logout = () => {
    const router = useRouter();
    router.replace({ name: 'logout' })
  }
</script>