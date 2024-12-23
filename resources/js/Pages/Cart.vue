<script setup>
import FrontLayout from "@/Layouts/FrontLayout.vue";
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { notify } from "notiwind";
import Container from "@/Components/Container.vue";
import Footer from "@/Pages/Web/Footer.vue";
import Modal from "@/Components/Modal.vue";
import { inject, ref } from "vue";
import ProductDetails from "@/Components/ProductDetails.vue";

const swal = inject('$swal');

const props = defineProps({
  cartItems: {
    type: Object,
    default: null,
  },
});


const getTotal = () => {
  if (props.cartItems && props.cartItems.length > 0) {
    return props.cartItems.reduce((total, item) => {
      const itemTotal = item.itemable.price - item.itemable.discount;
      return total + itemTotal;
    }, 0);
  }
  return 0;
}

const form = useForm({
    _method: "POST",
    processing: false,
    tot_price: getTotal(),
    payment_method: 'stripe',
});

const showProductDetailModal = ref(false);
const productObj = ref(null);

const showProdcutDetails = (product) => {
    showProductDetailModal.value = true;
    productObj.value = product;
};

const hideProductDetailModal = () => {
    showProductDetailModal.value = false;
    productObj.value = null;

};

const removeProductFromCartConfirmation = (id) => {
  swal({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    cancelButtonText: "Cancel",
    confirmButtonText: "Yes, delete it!",
    customClass: {
      confirmButton: "inline-block mr-5 mb-5 px-6 py-3 m-0 ml-2 text-xs font-bold text-center text-red-500 uppercase align-middle transition-all border-0 rounded-lg cursor-pointer ease-soft-in leading-pro tracking-tight-soft bg-primary shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85",
      cancelButton: "inline-block px-6 mb-5 py-3 m-0 font-bold text-center uppercase align-middle transition-all bg-gray-200 border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 text-slate-800",
    },
    buttonsStyling: false,
  }).then((result) => {
    if (result.isConfirmed) {
      removeProductFromCart(id);
    } else if (
        result.dismiss === swal.DismissReason.cancel
    ) {
      swal.dismiss;
    }
  });
}

const removeProductFromCart = async(id) => {
    try {
      const response = await axios.post(route('remove-from-cart', id));
      router.visit(route('view-cart'), {
          onSuccess: () => {
              notify({
                  group: "success",
                  title: "Success",
                  text: 'Product removed from cart successfully',
              }, 4000)
          },
      });
    } catch (error) {
      notify({
        group: 'error',
        title: 'Error',
        text: 'Something went wrong!'
    }, 4000)
    }
}

const placeOrder = () => {
  if (form.payment_method == 'stripe') {
      stripePayment();
  } else if (form.payment_method == 'paypal') {
      paypalPayment();
  }
}
const stripePayment = async () => {
    try {
        form.processing = true;
        const response = await axios.post(route('stripe.payment'), form);
        form.processing = false;
        window.location.href = response.data.sessionURL;
    } catch (error) {
        notify({
            group: "error",
            title: "Error",
            text: "Something went wrong!"
        }, 4000)
    } finally {
      form.processing = false;
    }
}
const paypalPayment = async () => {
    try {
        form.processing = true;
        const response = await axios.post(route('paypal.payment'), form);
        form.processing = false;
        window.location.href = response.data.sessionURL;
    } catch (error) {
        notify({
            group: "error",
            title: "Error",
            text: "Something went wrong!"
        }, 4000)
    } finally {
      form.processing = false;
    }
}

</script>
<template>
  <FrontLayout title="Cart">

    <Head title="Cart" />
    <Modal :show="showProductDetailModal" @close="hideProductDetailModal">
        <ProductDetails :product="productObj" @close-modal="hideProductDetailModal" />
    </Modal>
    <Container>
      <div class="bg-ash_web-7000 px-6 order-first lg:order-last">
        <div class="text-center lg:text-left text-base sm:text-2xl text-black-900 font-bold mt-8">Your order
        </div>
        <div>
          <div v-if="cartItems && cartItems.length > 0" v-for="cart  in cartItems" :key="cart.id"
            class="flex mt-10 gap-3 border-b border-indigo-100 pb-5">
            <img loading="lazy" width="100"
              :src="cart.itemable.product_photo_url"
              alt="checkout-image" class="hidden sm:block w-16 h-16 rounded-md object-cover">
            <div class="grid w-full">
              <div class="flex justify-between">
                <div class="text-base sm:text-lg text-black-700 font-normal">
                  {{ cart.itemable.name  }}
                </div>
                <button @click="removeProductFromCartConfirmation(cart.id)">
                  <svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M6 19C6 20.1 6.9 21 8 21H16C17.1 21 18 20.1 18 19V7H6V19ZM19 4H15.5L14.5 3H9.5L8.5 4H5V6H19V4Z"
                      fill="#474747"></path>
                  </svg>
                </button>
              </div>
              <div class="flex justify-between w-full mt-1">
                <div>
                  <label class="underline text-primary" @click="showProdcutDetails(cart.itemable)">Click to view details</label>
                </div>
                <div class="text-base sm:text-xl text-black-700 font-bold">
                  <span v-if="cart.itemable.discount > 0" class="line-through">${{ cart.itemable.price }}</span>
                  <span>&nbsp; ${{ cart.itemable.price - cart.itemable.discount }}</span>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="flex justify-center items-center h-20 text-base sm:text-lg text-black-700 font-normal">
            No product in your cart
          </div>
          <!--End Apply now-->
          <div v-if="cartItems && cartItems.length > 0" class="flex justify-between mt-10 pb-2 lg:pb-5">
            <div class="text-base sm:text-lg text-black-700 font-normal">Total</div>
            <div class="text-base sm:text-xl text-black-700 font-bold">${{ getTotal() }}</div>
          </div>
          <div v-if="cartItems && cartItems.length > 0" class="flex gap-4 mt-10 pb-2 lg:pb-5">
            <div class="flex w-1/2 items-center p-4 bg-white rounded-lg shadow border hover:border-blue-500 cursor-pointer">
              <input
                type="radio"
                id="stripe"
                name="paymentMethod"
                v-model="form.payment_method"
                value="stripe"
                class="w-5 h-5 text-blue-500 focus:ring-blue-500"
              />
              <label for="stripe" class="ml-3 text-gray-800 font-medium cursor-pointer">
                <span class="flex items-center">
                  <img src="/img/stripe-large-logo.png" alt="Stripe" class="w-6 h-6 mr-2" />
                  Stripe
                </span>
              </label>
            </div>

            <div class="flex w-1/2 items-center p-4 bg-white rounded-lg shadow border hover:border-blue-500 cursor-pointer">
              <input
                type="radio"
                id="paypal"
                name="paymentMethod"
                v-model="form.payment_method"
                value="paypal"
                class="w-5 h-5 text-blue-500 focus:ring-blue-500"
              />
              <label for="paypal" class="ml-3 text-gray-800 font-medium cursor-pointer">
                <span class="flex items-center">
                  <img src="/img/paypal-symbol.png" alt="PayPal" class="w-6 h-6 mr-2" />
                  PayPal
                </span>
              </label>
            </div>
          </div>

          <div v-if="cartItems && cartItems.length > 0" class="flex justify-between mt-10 pb-10 lg:pb-40">
            <button
                class="text-left text-sm sm:text-xl text-white font-normal bg-primary rounded-full px-10 py-3 my-3"
                :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click.prevent="placeOrder()"> {{ form.processing ?
                    'Processing...' : 'Place Order' }}
            </button>
          </div>
        </div>
      </div>
    </Container>

    <Footer />

  </FrontLayout>
</template>