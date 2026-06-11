<template>
  <div class="w-full overflow-x-auto">
    <div class="flex items-center justify-between mb-2">
      <div v-if="searchable" class="relative w-full max-w-xs">
        <input
          type="text"
          v-model="search"
          placeholder="Search"
          class="border rounded w-full py-2 pl-10 pr-4 text-sm focus:outline-none focus:ring focus:border-blue-300"
        />
        <div
          class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
        >
          <svg
            class="w-4 h-4 text-gray-400"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1111.196 3.032l4.387 4.386a1 1 0 01-1.414 1.414l-4.386-4.387A6 6 0 012 8z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
      </div>
    </div>
    <div class="max-h-[calc(100vh-280px-48px)] overflow-y-auto border rounded relative">
      <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs sticky top-0 z-10">
          <tr>
            <th v-if="multiDelete" class="p-3">
              <input type="checkbox" />
            </th>
            <th
              v-for="col in columns"
              :key="col.key"
              class="p-3 font-medium whitespace-nowrap"
            >
              {{ col.label }}
            </th>
            <th class="p-3 text-right"></th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(row, idx) in paginatedRows"
            :key="row.id || idx"
            class="hover:bg-gray-50 border-b"
          >
            <td v-if="multiDelete" class="p-3">
              <input type="checkbox" />
            </td>

            <td
              v-for="col in columns"
              :key="col.key"
              class="p-3 whitespace-nowrap"
            >
                      
              <router-link
                v-if="col.isLink"
                class="text-blue-600 hover:underline"
                :to="{ name: col.isLink, params: { id: row.id } }"
              >
                {{ row[col.key] }}
              </router-link>

              <span v-else-if="row[col.key]?.[col.objectKey]">
                {{ row[col.key][col.objectKey] }}
              </span>

                <button v-else-if="(col.objectModal && row[col.key])"
                @click="emitModal(row)"
                class="w-full text-left px-4 py-2 text-blue-600 hover:underline"
                >
                     {{ row[col.key] }}
                </button>

              <span
                v-else-if="col.badge"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                :class="col.badgeClass ? col.badgeClass(row) : ''"
              >
                {{ row[col.key] }}
              </span>

              <span v-else>
                {{ row[col.key] }}
              </span>
            </td>

            <td v-if="actions" class="p-3 text-right relative">
              <button
                @click.stop="toggleActions(idx, $event)"
                class="text-gray-500 hover:text-gray-800"
                aria-label="Open actions menu"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M10 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"
                  />
                </svg>
              </button>

              <!-- Dropdown -->
              <teleport to="body">
                <div
                  v-if="activeDropdown === idx"
                  :style="dropdownPositions[idx]"
                  class="dropdown-menu fixed w-40 bg-gray-50 text-gray-500 border border-gray-200 rounded shadow-lg z-50"
                  @click.stop
                >
                  <ul class="text-sm text-gray-700">
                    
                    <!--
                    
                    <li>
                      <button
                        @click="$emit('edit', row); activeDropdown = null"
                        class="w-full text-left px-4 py-2 hover:bg-gray-100"
                      >
                        {{ t("labels.edit") }}
                      </button>
                    </li>

                    <li v-if="disable">
                      <button
                        @click="$emit('disable', row.id, row.status); activeDropdown = null"
                        class="w-full text-left px-4 py-2"
                        :class="labelActive(row.status).statusClass"
                      >
                        {{ labelActive(row.status).label }}
                      </button>
                    </li>

                    <li>
                      <button v-if="remove"
                        @click="$emit('remove', row.id); activeDropdown = null"
                        class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 hover:text-red-700"
                      >
                        {{ t("labels.delete") }}
                      </button>
                    </li>
                    -->

                    <template v-for="item in types_actions" :key="item.type">
                      <li v-if="!item.condition || item.condition(row)">
                        <button
                          @click="emitAction(item.type, row)"
                          class="w-full text-left px-4 py-2 hover:bg-gray-100"
                        >
                         {{ item.label }}
                        </button>
                      </li>
                    </template>                    
                    
                  </ul>
                </div>
              </teleport>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Rodapé da tabela -->
    <div class="mt-2 flex justify-between items-center text-sm text-gray-600">
      <div>
        Mostrando
        <strong>{{ paginatedRows.length }}</strong>
        de <strong>{{ filteredRows.length }}</strong> registros
      </div>

      <div class="flex gap-1">
        <button
          :disabled="currentPage === 1"
          @click="currentPage--"
          class="px-2 py-1 border rounded disabled:opacity-50"
        >
          Anterior
        </button>
        <span>Página {{ currentPage }}</span>
        <button
          :disabled="currentPage >= totalPages"
          @click="currentPage++"
          class="px-2 py-1 border rounded disabled:opacity-50"
        >
          Próxima
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const emit = defineEmits(["edit", "remove", "disable", "action", "modal"]);

const props = defineProps({
  columns: { type: Array, required: true },
  rows: { type: Array, required: true },
  searchable: { type: Boolean, default: true },
  multiDelete: { type: Boolean, default: false },
  disable: { type: Boolean, default: true },
  remove: { type: Boolean, default: true },
  types_actions: { type: Array, required: true },
  actions: { type: Boolean, default: true}
});

const activeDropdown = ref(null);
const dropdownPositions = ref({});
const search = ref("");
const currentPage = ref(1);
const perPage = ref(30);

function emitAction(type, row) {
  emit("action", { type, row });
  activeDropdown.value = null;
}

function emitModal(row) {
  emit("modal", row);
}


function toggleActions(index, event) {
  // Evitar que clique no botão feche e abra rápido
  if (activeDropdown.value === index) {
    activeDropdown.value = null;
  } else {
    // Calcula posição do dropdown baseado no botão clicado
    const rect = event.currentTarget.getBoundingClientRect();
    dropdownPositions.value[index] = {
      top: `${rect.bottom + window.scrollY}px`,
      left: `${rect.left + window.scrollX - 150}px`,
    };
    activeDropdown.value = index;
  }
}

function handleClickOutside(event) {
  // Se clicar fora do dropdown e do botão, fecha o dropdown
  if (
    activeDropdown.value !== null &&
    !event.target.closest(".dropdown-menu") &&
    !event.target.closest("button")
  ) {
    activeDropdown.value = null;
  }
}

onMounted(() => {
  window.addEventListener("click", handleClickOutside);
});
onUnmounted(() => {
  window.removeEventListener("click", handleClickOutside);
});

// Filtra os dados por string (superficial)
const filteredRows = computed(() => {
  if (!search.value) return props.rows;
  return props.rows.filter((row) =>
    Object.values(row).some((val) =>
      val?.toString().toLowerCase().includes(search.value.toLowerCase())
    )
  );
});

// Paginação
const totalPages = computed(() =>
  Math.ceil(filteredRows.value.length / perPage.value)
);

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredRows.value.slice(start, start + perPage.value);
});

// Resetar para a página 1 quando filtrar ou alterar itens por página
watch(search, () => {
  currentPage.value = 1;
});
watch(perPage, () => {
  currentPage.value = 1;
});

function labelActive(status) {
  if (status === "active") {
    return {
      label: "Desabilitar",
      statusClass: "hover:bg-red-50 text-red-600 hover:text-red-700",
    };
  } else {
    return {
      label: "Habilitar",
      statusClass: "hover:bg-blue-50 text-blue-600 hover:text-blue-700",
    };
  }
}
</script>
