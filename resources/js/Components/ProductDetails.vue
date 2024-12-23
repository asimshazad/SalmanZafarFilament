<script setup>
import { defineEmits, defineProps } from "vue";


const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    modalClose: {
        type: Function,
        default: () => {
        },
    },
    product: {
        type: Object,
        default: null,
    }

});

const emit = defineEmits(["close-modal"]);

</script>
<template>
    <div class="bg-white rounded-lg shadow-lg p-8 common-scroll-bar" style="max-height: 81vh; overflow: scroll">
        <h2 class="text-gray-800 text-xl font-bold mb-3 uppercase flex justify-center pt-2">{{ product.name }}</h2>

        <div
            class="priceTble flex flex-col md:flex-row">
            <img class="md:me-8 rounded-circle" :src="product.product_photo_url" />
            <div>
                <div v-html="product.description"></div>
                <h6>${{ product.price }}</h6>
            </div>
        </div>
    </div>
</template>