<template>
  <div>
    <breadcrumb
      title="Users"
      linkText="Work Schedule"
      linkHref="/work-schedule"
      :showFilter = true
      :showExport = true
      :showAdd = true
      @filter="abrirFiltro"
      @export="exportarDados"
      @add="adicionarItem"
    />
    
   <BaseInput
    v-model="input"
    label="Input"
    name="name"
    placeholder="componet input"
    type="text"
    :required="true"
  />

  <BaseInput
    v-model="cpf"
    label="CPF"
    name="cpf"
    mask="cpf"
    placeholder="input cpf"
    type="text"
    :required="true"
  />

  <BaseTextarea v-model="bio" label="Biografia" />
  <BaseSelect v-model="pais" label="País" :options="paises" />
  <BaseRadio v-model="genero" label="Gênero" :options="opcoesGenero" />
  <BaseCheckbox v-model="aceitaTermos" label="Aceito os termos e condições" />

    <BaseDateInput
    v-model="dataNascimento"
    label="Data de nascimento"
    name="nascimento"
    :required="true"
  />

  <BaseMultiSelect
    v-model="tecnologias"
    label="Tecnologias"
    name="tecnologias"
    :options="opcoestecnologias"
    placeholder="Selecione..."
    :required="true"
  />
  <p class="mt-4 text-sm">Selecionado: {{ tecnologias }}</p>

  <BaseMultiSelectTags
    v-model="frameworks"
    :options="optionsframeworks"
    label="Frameworks"
    placeholder="Escolha os frameworks"
  />
  <div class="mt-4 text-sm">
    Selecionados: {{ frameworks }}
  </div>

    <button @click="showModal" class="bg-blue-600 text-white px-4 py-2 rounded mt-4">Abrir Modal</button>
  </div>
</template>

<script setup>
import { useModalStore } from '../shared/store/modal'
import Home from './Home.vue'
import breadcrumb from '../layout/Breadcrumb.vue' 

import BaseInput    from '../components/Base/BaseInput.vue'
import BaseTextarea from '../components/Base/BaseTextarea.vue'
import BaseSelect   from '../components/Base/BaseSelect.vue'
import BaseCheckbox from '../components/Base/BaseCheckbox.vue'
import BaseRadio    from '../components/Base/BaseRadio.vue'
import BaseDateInput from '../components/Base/BaseDateInput.vue'
import BaseMultiSelect from '../components/Base/BaseMultiSelect.vue'
import BaseMultiSelectTags from '../components/Base/BaseMultiSelectTags.vue'

import { ref } from 'vue'

const nome = ref('')
const bio = ref('')
const pais = ref('')
const aceitaTermos = ref(false)
const genero = ref('')
const tecnologias = ref([])
const frameworks = ref([])

const optionsframeworks = [
  { label: 'Vue 3', value: 'vue' },
  { label: 'React', value: 'react' },
  { label: 'Angular', value: 'angular' },
  { label: 'Svelte', value: 'svelte' }
]

const opcoestecnologias = [
  { label: 'Vue 3', value: 'vue' },
  { label: 'React', value: 'react' },
  { label: 'Angular', value: 'angular' },
  { label: 'Svelte', value: 'svelte' }
]

const paises = [
  { label: 'Brasil', value: 'BR' },
  { label: 'EUA', value: 'US' }
]

const opcoesGenero = [
  { label: 'Masculino', value: 'M' },
  { label: 'Feminino', value: 'F' }
]

const modal = useModalStore()

function showModal() {
  modal.open(
    Home, 
    { name: 'Gustavo' }, 
    'small',
    'New/Edit Profiles',
    () => callbackSave()
  )
}

function callbackSave(){
  alert('teste')
}

function abrirFiltro() {
  alert('Abrir filtros')
}

function exportarDados() {
  alert('Exportar dados')
}

function adicionarItem() {
  alert('Adicionar novo')
}

</script>

<style scoped>

</style>
