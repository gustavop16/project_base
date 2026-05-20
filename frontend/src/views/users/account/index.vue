<template>
<div class="flex flex-col h-screen">
    <breadcrumb
      class="mb-2"
      :title="t('labels.users')"
      :linkText="t('labels.my_account')"
      linkHref="/usuarios/minha-conta"
    />
  
  <div class="">
    <!-- Tabs Responsivas -->
    <div class="overflow-x-auto">
      <div
        class="flex border-b border-gray-300 mb-4 text-sm sm:text-base whitespace-nowrap min-w-max space-x-4"
      >
        <button
          v-for="tab in tabs"
          :key="tab.key"
          @click="activeTab = tab.key"
          :class="[
            'pb-2 px-2',
            activeTab === tab.key
              ? 'border-b-2 border-blue-600 text-blue-600 font-semibold'
              : 'text-gray-600 hover:text-blue-600',
          ]"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- Conteúdo da Aba -->
    <Suspense>
      <component
        :is="currentTabComponent"
        class="bg-gray-50 p-4 sm:p-6 rounded-md"
      />
      <template #fallback>
        <p class="text-gray-500">Carregando...</p>
      </template>
    </Suspense>
  </div>
</div>
</template>

<script setup>
import { ref, computed, defineAsyncComponent } from "vue";
import { useI18n } from 'vue-i18n'
import breadcrumb from '../../../layout/Breadcrumb.vue'

const { t } = useI18n()

const tabs = [
  { key: "userDetails", label: t('labels.user') },
];

const activeTab = ref("userDetails");

// Lazy load dos componentes
const componentsMap = {
  userDetails: defineAsyncComponent(() => import("./myAccount.vue")),
  acknowledgment: defineAsyncComponent(() => import("./tab2.vue")),
  //courses: defineAsyncComponent(() => import('./Tabs/CoursesTab.vue')),
  //certificates: defineAsyncComponent(() => import('./Tabs/CertificateManagementTab.vue')),
};

const currentTabComponent = computed(() => componentsMap[activeTab.value]);
</script>
