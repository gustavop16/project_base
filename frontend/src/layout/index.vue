<script setup>

import Header from './Header.vue'
import Sidebar from './Sidebar.vue'
import Footer from './Footer.vue'

</script>

<template>
 <Header />

  <div class="flex">
    <Sidebar />

    <main class="ml-0 md:ml-64 mt-16 h-[calc(100vh-4rem)] overflow-y-auto pl-6 pr-6 flex-1">
      <router-view />
    </main>
    
  </div>
   <Footer />
 
</template>




<style lang="scss" scoped>
.flex-grow-1 {
    -webkit-box-flex: 1!important;
    -ms-flex-positive: 1!important;
    flex-grow: 1!important;
}
    .app-admin-wrap-layout-2 {
        width: 100%;
        height: 100%;
        .main-content-wrap {
            width: calc(100% - 120px);
            margin-left: 90px;
            // min-height: 100vh;
            min-height: calc(100vh - 60px);
            padding-top: 50px;
            transition: all 0.24s ease-in-out;
            .main-content-body{
                min-height: calc(100vh - 80px);
            }
            &.full {
                width: 100%;
                margin-left: 0px;
                transition: all 0.24s ease-in-out;
            }
            @media screen and (max-width: 991px) {
                width: 100%;
                margin-left: 0px;
                padding-right: 16px;
                padding-left: 16px;
            }
        }
    }

    
</style>


