<template>
    <!--class="mb-2 relative max-h-[calc(100vh-240px-48px)]"-->
  <div
    v-for="(record, index) in records"
    :key="index"
    :class="['mb-2', 'relative', dynamicMaxHeight]" 
  >
    <div class="absolute top-2 right-2" data-dropdown>
      <button
        @click.stop="toggleMenu(index)"
        class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
        :ref="el => btnRefs[index] = el"
      >
        ⋮ 
      </button>
    </div>

    <div class="space-y-1">
      <div
        v-for="col in columns"
        :key="col.key"
        class="py-1"
      >
        <span class="font-bold">{{ col.label }}: </span>

        <!-- Se for array de objetos -->
        <template v-if="Array.isArray(record[col.key])">
            <ul class="list-disc ml-4">
            <li v-for="(item, i) in record[col.key]" :key="i">
                {{ item.name ?? '' }}
            </li>
            </ul>
        </template>

        <span v-else-if="record[col.key]?.[col.objectKey]" class="">
            {{ record[col.key][col.objectKey] ?? '' }}
        </span>
       
        <span v-else class=""> {{ record[col.key] ?? '' }} </span>
      </div>

     

    </div>

    <div
      v-if="activeDropdown === index"
      :ref="el => dropdownRefs[index] = el"
      :style="{ top: menuPosition.top + 'px',  right: menuPosition.right + 'px'}"
      class="absolute bg-white border border-gray-300 rounded shadow-md z-50"
    >
      <button
        v-for="item in types_actions"
        :key="item.type"
        class="block w-full px-4 py-2 text-left hover:bg-gray-100"
        @click="emitAction(item.type, records[activeDropdown])"
      >
        {{ item.label }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({
  columns: { type: Array, required: true },
  records: { type: Array, required: true },
  types_actions: { type: Array, required: true },
  dynamicMaxHeight: { type: String, default: 'max-h-[calc(100vh-240px-48px)]' }
});
const emit = defineEmits(["action"]);

const { t } = useI18n();
const activeDropdown = ref(null);
const menuPosition = ref({ top: 0, left: 0 });

const btnRefs = ref([]);
const dropdownRefs = ref([]);

function toggleMenu(index) {
  if (activeDropdown.value === index) {
    // Se clicar no botão do menu aberto, fecha ele
    activeDropdown.value = null;
    return;
  }

  // Abre o menu para o índice clicado
  const btn = btnRefs.value[index];

  if (!btn) return;

  // Posiciona ao lado direito do botão, alinhado no topo
  // Como o container pai tem position: relative, offsetTop/Left funcionam
  const top = 35//btn.offsetTop;
  //const left = btn.offsetLeft + btn.offsetWidth + 8; // 8px de espaçamento à direita
    const right = 10 // 8px de espaçamento à direita
  menuPosition.value = { top, right };
  activeDropdown.value = index;
}

function closeMenu(event) {
  if (activeDropdown.value === null) return;

  const dropdownEl = dropdownRefs.value[activeDropdown.value];
  const btnEl = btnRefs.value[activeDropdown.value];

  if (
    dropdownEl &&
    !dropdownEl.contains(event.target) &&
    btnEl &&
    !btnEl.contains(event.target)
  ) {
    activeDropdown.value = null;
  }
}

function emitAction(type, row) {
  emit("action", { type, row });
  activeDropdown.value = null;
}



onMounted(() => {
  document.addEventListener("click", closeMenu);
});
onBeforeUnmount(() => {
  document.removeEventListener("click", closeMenu);
});
</script>
