<script setup>
import FrontLayout from "@/Layouts/FrontLayout.vue";
import { Head } from '@inertiajs/vue3';
import Footer from "@/Pages/Web/Footer.vue";
import ChatBox from "@/Components/ChatBox.vue";
import Products from "@/Components/Products.vue";
import Modal from "@/Components/Modal.vue";
import ShowThankYouModal from '@/Components/ShowThankYouModal.vue';
import { onMounted, ref } from "vue";

const props = defineProps({
    show_thankyou_modal: {
      type: Boolean,
    },
    products: {
      type: Object,
      default: null,
    }
});

const showModalView = ref(false);
onMounted ( () => {
    if (props.show_thankyou_modal) {
        showModalView.value = true;
    }
    setTimeout ( () => {
        removeUniqueIdFromURL();
    }, 500)
});

const removeUniqueIdFromURL = () => {
    const url = new URL(window.location.href);
    if (url.searchParams.has('message')) {
        url.searchParams.delete('message');
        window.history.replaceState({}, document.title, url.toString());
    }
};

</script>
<style>
#main_header.innerHeader {
  border-bottom:none;
}
#main_header.innerHeader .align-items-center{
  align-items:start !important;
}
@media (max-width:768px) {
  #main_header.innerHeader .align-items-center {align-items: center !important;}
}
</style>
<template>
<FrontLayout title="Cart">

  <Head title="Welcome" />
  <Modal :show="showModalView" :max-width="maxWidth" @close="showModalView = false" class="w-full">
    <ShowThankYouModal
        @close-modal="showModalView = false"
    />
  </Modal>
  <div class="flex h-screen">
    <div class="w-1/2 max-h-screen overflow-auto bg-gray-200 p-4 common-scroll-bar">
      <ChatBox></ChatBox>
    </div>
    <div class="w-1/2 max-h-screen overflow-auto bg-gray-200 p-4 common-scroll-bar">
      <Products :products="products"></Products>
    </div>
  </div>
  <Footer />

</FrontLayout>
</template>