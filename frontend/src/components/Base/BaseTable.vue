<template>
  <div class="w-full overflow-x-auto">
    <table class="min-w-full border border-gray-300 text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th
            v-for="col in columns"
            :key="columns.length+'_'+col.key"
            class="border border-gray-300 px-4 py-2 text-left"
          >
            {{ col.label }}
          </th>
          <th v-if="actions" class="border border-gray-300 px-4 py-2">Ação</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(row, rowIndex) in records"
          :key="rowIndex"
          class="odd:bg-white even:bg-gray-50 hover:bg-gray-100"
        >
          <td
            v-for="col in columns"
            :key="col.key"
            class="border border-gray-300 px-4 py-2"
          >

            <span v-if="row[col.key]?.[col.objectKey]">
                {{ row[col.key][col.objectKey] }}
             </span>
              <span v-else>
                {{ row[col.key] }}
              </span>

            
          </td>
          <td v-if="actions" class="border border-gray-300 px-4 py-2">
            <button
              @click.stop="openMenu($event, rowIndex)"
              class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
            >
              ⋮
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Menu Dropdown flutuante -->
  <teleport to="body">
    <div
      v-if="activeDropdown !== null"
      ref="dropdownRef"
      :style="{ top: menuPosition.top + 'px', left: menuPosition.left + 'px' }"
      class="fixed w-32 bg-white border border-gray-300 rounded shadow-md z-50"
    >
      <button
        class="block w-full px-4 py-2 text-left hover:bg-gray-100"
        @click="emitAction('edit', records[activeDropdown])"
      >
        Editar
      </button>
      <button
        class="block w-full px-4 py-2 text-left hover:bg-gray-100"
        @click="emitAction('delete', records[activeDropdown])"
      >
        Deletar
      </button>
    </div>
  </teleport>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
  columns: { type: Array, required: true }, 
  records: { type: Array, required: true }, 
  actions: { type: Boolean, default: true}
});
const emit = defineEmits(["action"]);

const activeDropdown = ref(null);
const menuPosition = ref({ top: 0, left: 0 });
const dropdownRef = ref(null);

function openMenu(event, index) {
  const rect = event.currentTarget.getBoundingClientRect();
  menuPosition.value = {
    top: rect.bottom + window.scrollY,
    left: rect.left + window.scrollX,
  };
  activeDropdown.value = index;
}

function closeMenu(event) {
  if (
    activeDropdown.value !== null &&
    dropdownRef.value &&
    !dropdownRef.value.contains(event.target)
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
