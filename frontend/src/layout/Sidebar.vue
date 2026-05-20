<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useRoute } from 'vue-router'
import { usePermissions } from '../shared/composables/usePermissions'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faHouse, faUsers, faDatabase, faList, faBoxesPacking,faCircleUser, faListCheck, faMapLocationDot, faChartSimple, faNewspaper, faWineBottle} from '@fortawesome/free-solid-svg-icons'
import { library } from '@fortawesome/fontawesome-svg-core'

library.add([
  faHouse,
  faUsers,
  faDatabase,
  faList,
  faBoxesPacking,
  faCircleUser,
  faListCheck,
  faMapLocationDot,
  faChartSimple,
  faNewspaper,
  faWineBottle
]);

const route   = useRoute();
const isOpen  = ref(false);
const openSubmenus = ref([]);
const { t } = useI18n();
const { can } = usePermissions();

// Verifica se filho está ativo de forma precisa
function isChildActive(child) {
  return (
    route.name === child.route ||
    route.name?.startsWith(child.route + '-') ||
    route.name?.startsWith(child.route + '/')
  );
}

// Pai ativo se algum filho realmente estiver ativo
function isParentActive(item) {
  return item.children?.some(child => isChildActive(child));
}

const toggleSubmenu = (index) => {
  if (openSubmenus.value.includes(index)) {
    openSubmenus.value = openSubmenus.value.filter(i => i !== index);
  } else {
    openSubmenus.value.push(index);
  }
};
const isSubmenuOpen = (index) => openSubmenus.value.includes(index);

const closeOnOutsideClick = (e) => {
  if (!e.target.closest("aside") && !e.target.closest("button")) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener("click", closeOnOutsideClick);

  menuItems.value.forEach((item, index) => {
    if (isParentActive(item)) {
      if (!openSubmenus.value.includes(index)) {
        openSubmenus.value.push(index);
      }
    }
  });
});

onBeforeUnmount(() => {
  document.removeEventListener("click", closeOnOutsideClick);
});

// Reabre automaticamente o submenu certo ao trocar de rota
watch(() => route.name, () => {
  menuItems.value.forEach((item, index) => {
    if (isParentActive(item)) {
      if (!openSubmenus.value.includes(index)) {
        openSubmenus.value.push(index);
      }
    } else {
      openSubmenus.value = openSubmenus.value.filter(i => i !== index);
    }
  });
});

const allMenuItems = [
  {
    label: 'Dashboard',
    route: 'Home',
    icon: 'fa-solid fa-chart-simple',
    permission: null,
  },
  {
    label: 'Clientes',
    route: 'client',
    icon: 'fa-solid fa-users',
    permission: 'client.view',
  },
  {
    label: 'Locais',
    route: 'place',
    icon: 'fa-solid fa-map-location-dot',
    permission: 'place.view',
  },
  {
    label: 'Produtos',
    route: 'product',
    icon: 'fa-solid fa-wine-bottle',
    permission: 'product.view',
  },
  {
    label: 'Tarefas',
    route: 'task',
    icon: 'fa-solid fa-list-check',
    permission: 'task.view',
  },
  {
    label: 'Planejamentos',
    route: 'planning',
    icon: 'fa-solid fa-newspaper',
    permission: 'planning.view',
  },
  {
    label: t("labels.users"),
    route: 'user-staff',
    icon: 'fa-solid fa-circle-user',
    permission: 'users.view',
  },
]

const menuItems = computed(() => allMenuItems.filter(item => can(item.permission)))
</script>

<template>
  <!-- Sidebar -->
  <aside
    class="fixed top-16 left-0 w-64 h-[calc(100vh-4rem)] bg-gray-100 text-gray-500 z-40 p-4 overflow-y-auto transition-transform duration-300 md:translate-x-0"
    :class="{ '-translate-x-full': !isOpen }"
  >
    <nav class="space-y-2">
      <div v-for="(item, index) in menuItems" :key="index" class="group">
        <!-- Menu principal sem filhos -->
        <div
          v-if="!item.children"
          class="hover:bg-gray-300 px-3 py-2 rounded cursor-pointer flex items-center space-x-2"
          :class="{ 'bg-gray-300 text-gray font-medium': route.name?.startsWith(item.route) }"
        >
          <font-awesome-icon :icon="item.icon" />
          <router-link :to="{name: item.route}" class="block px-3 py-1 rounded cursor-pointer hover:bg-gray-300">
            {{ item.label }}
          </router-link>
        </div>

        <!-- Menu com submenu -->
        <div v-else>
          <button
            @click="toggleSubmenu(index)"
            class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-300"
            :class="{
              'bg-gray-300 text-gray-600 font-medium': isParentActive(item),
              'hover:bg-gray-300': !isParentActive(item)
            }"
          >
            <div class="flex items-center space-x-2">
              <font-awesome-icon :icon="item.icon" />
              <span class="flex items-center gap-2">{{ item.label }}
                <svg xmlns="http://www.w3.org/2000/svg"
                  class="w-4 h-4 transform transition-transform duration-200"
                  :class="{ 'rotate-90': isSubmenuOpen(index) }"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
              </span>
            </div>
          </button>

          <!-- Submenu -->
          <ul v-show="isSubmenuOpen(index)" class="mt-1 space-y-1 text-sm bg-gray-200">
            <li
              v-for="(child, idx) in item.children"
              :key="idx"
              class="hover:bg-gray-300 px-3 py-1 rounded cursor-pointer flex items-center space-x-2"
              :class="{ 'bg-gray-300 text-gray font-medium': isChildActive(child) }"
            >
              <font-awesome-icon :icon="child.icon || item.icon" /> 
              <router-link :to="{name: child.route}" class="block px-3 py-1 rounded cursor-pointer hover:bg-gray-300">
                {{ child.label }}  
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </aside>

  <!-- Botão hamburguer -->
  <button
    class="md:hidden fixed top-4 left-4 z-50 bg-gray-800 text-white p-2 rounded"
    @click="isOpen = !isOpen"
  >
    ☰
  </button>
</template>
