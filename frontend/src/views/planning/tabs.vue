<template>
  <div>
    <breadcrumb class="mb-2"
      :title="t('labels.plannings')"
      :showAdd = true
      @add="openModalcreate"
    />
  </div>

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
        class="p-4 sm:p-6 rounded-md"
      />
      <template #fallback>
        <p class="text-gray-500">Carregando...</p>
      </template>
    </Suspense>
  </div>
</template>

<script setup>
import { ref, computed, defineAsyncComponent } from "vue";
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import breadcrumb from '../../layout/Breadcrumb.vue'
import { useModalStore } from '../../shared/store/modal'
import form from './_form.vue'

const modal   = useModalStore();
const router = useRouter()
const { t } = useI18n()

const tabs = [
  { key: "details",  label: t('labels.details') },
  { key: "history",  label: t('labels.history') },
];

const activeTab = ref("details");

// Lazy load dos componentes
const componentsMap = {
  details: defineAsyncComponent(() => import("./show.vue")),
  history: defineAsyncComponent(() => import("./history/index.vue")),
};

const currentTabComponent = computed(() => componentsMap[activeTab.value]);

function openModalcreate() {
  modal.open(
    form, 
    {}, 
    'large',
    t('labels.new')+' '+t('labels.planning'),
    () => {
        //router.push({ name: 'facility-station' });
    }
  )
}

</script>
