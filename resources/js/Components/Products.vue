<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { notify } from 'notiwind';
import { ref } from 'vue';

const props = defineProps({
    products: {
      type: Object,
      default: null,
    },

});

const showProduct = ref(null);

const toggleShowDetails = (id) => {
  if(showProduct.value == id) return showProduct.value = null;
  return showProduct.value = id;
}

const addToCart = async(id) => {
  if (!usePage().props.auth.user) {
    router.get(route('login', {'customURL' : 'home'}));
    return;
  }
  try {
    const response = await axios.post(route('add-to-cart'), {'product_id': id});
    notify({
        group: response.data.type,
        title: response.data.type,
        text: response.data.message
    }, 4000)
  } catch (error) {
    notify({
        group: 'error',
        title: 'Error',
        text: 'Something went wrong!'
    }, 4000)
  }
}

const productDetailPage = (product) => {
    notify({
        group: "success",
        title: "Success",
        text: "Coming soon"
    }, 4000)
}

</script>
<template>
    <section id="" class="relative flex flex-col items-center justify-center productBsd">
    <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">

      <div class="grid gap-6 lg:grid-cols-1 lg:gap-8">
        <h2 class="sectionTitle">Incredible Products.</h2>

        <div v-if="props.products && props.products.length > 0" v-for="product in products" class="priceTble flex flex-col md:flex-row">
          <img class="md:me-8 rounded-circle" :src="product.product_photo_url" />
          <div>
            <h3>{{  product.name }}</h3>
            <div v-if="showProduct == product.id" v-html="product.description"></div>
            <h6>${{ product.price }}</h6>
            <button type="button" @click="toggleShowDetails(product.id)" class="btn btnprice">{{ showProduct == product.id ? 'Hide' : 'View' }} details</button>
            <button v-if="!product.is_free" type="button" @click="addToCart(product.id)" class="btn mx-2 btnprice">Add to cart</button>
            <button v-else type="button" @click="productDetailPage(product)" class="btn mx-2 btnprice">Visit Product detail</button>
          </div>
        </div>

      </div>

    </div>
  </section>
</template>