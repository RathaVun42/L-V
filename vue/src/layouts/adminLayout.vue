<!-- layouts/AdminLayout.vue -->
<template>
  <div class="min-h-screen bg-emerald-50">

    <!-- Sidebar -->
    <aside
      class="fixed left-0 top-0 h-screen w-64 bg-emerald-500 text-white shadow-lg"
    >
      <!-- Logo -->
      <div class="flex h-16 items-center px-6 border-b border-emerald-400">
        <h1 class="text-xl font-bold">
          My Admin
        </h1>
      </div>

      <!-- Navigation -->
      <nav class="mt-6 px-3 space-y-2">

        <RouterLink
          to="/dashboard/index"
          class="block rounded-lg px-4 py-3 transition hover:bg-emerald-600"
          active-class="bg-emerald-700"
        >
          Dashboard
        </RouterLink>

        <RouterLink
          to="/products"
          class="block rounded-lg px-4 py-3 transition hover:bg-emerald-600"
          active-class="bg-emerald-700"
        >
          Products
        </RouterLink>

        <RouterLink
          to="/users"
          class="block rounded-lg px-4 py-3 transition hover:bg-emerald-600"
          active-class="bg-emerald-700"
        >
          Users
        </RouterLink>

      </nav>
    </aside>


    <!-- Main area -->
    <div class="ml-64">

      <!-- Navbar -->
      <header
        class="sticky top-0 z-10 flex h-16 items-center justify-between
               bg-white px-6 shadow-sm"
      >
        <h2 class="text-lg font-semibold text-emerald-700">
          Admin Panel
        </h2>

        <button
          class="rounded-lg bg-emerald-500 px-4 py-2 text-sm
                 font-medium text-white transition hover:bg-emerald-600"
        >
          Logout
        </button>
      </header>


      <!-- Changing content -->
      <main class="p-6">
        <RouterView />
      </main>

    </div>

  </div>
</template>