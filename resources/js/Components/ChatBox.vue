<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import TextArea from "@/Components/TextArea.vue";
import { markdown } from 'markdown';
import html2pdf from "html2pdf.js";

const form = useForm({
    question: ''
});

const logs = ref([]);
const isLoading = ref(false);

const submitQuery = async () => {
  if (!form.question.trim()) {
    alert('Please enter a query.');
    return;
  }

  const query = form.question;
  isLoading.value = true;
  try {
    const res = await fetch('/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ messages: [{ role: 'user', content: query }] }),
    });

    if (!res.ok) throw new Error('Failed to fetch response');

    const data = await res.json();
    logs.value.push({
      query,
      response: markdown.toHTML(data.response) || 'No response received',
    });
  } catch (error) {
    console.error('Error:', error);
    logs.value.push({
      query,
      response: 'An error occurred. Please try again.',
    });
  } finally {
    form.question = '';
    isLoading.value = false;
  }
};

const downloadItem = (item) => {
  const element = document.createElement('div');
  element.innerHTML = `
    <h3 class="font-bold">Question: ${item.query}</h3>
    <div>${item.response}</div>
  `;

  const options = {
    margin: [10, 20, 0, 20],
    filename: `${item.query.slice(0, 35)}.pdf`,
    html2canvas: { scale: 2 },
    jsPDF: { 
      unit: 'mm', 
      format: 'a4', 
      orientation: 'portrait',
      autoPaging: true,
      autoSize: true,
      compress: true,
     }
  };

  html2pdf()
    .from(element)
    .set(options) 
    .save();
};
</script>

<template>
  <div class="p-4 mx-auto">
    <!-- Response -->
    <div class="mt-4 mb-3">
      <h2 class="text-lg font-bold">Conversation Logs:</h2>
      <ul class="mt-2 space-y-3">
        <li
          v-for="(log, index) in logs"
          :key="index"
          class="p-3 border border-gray-300 rounded-md"
        >
          <p class="font-semibold">Q: {{ log.query }}</p>
          <div class="mt-1" v-html="log.response"></div>
          <button @click="downloadItem(log)" class="btn-download">Download Q&A</button>
        </li>
      </ul>
    </div>
    <!-- User Input -->
    <TextArea
        id="question"
      v-model="form.question"
      placeholder="Type your query here..."
      class="w-full p-2 border border-gray-300 rounded-md"
      rows="4"
      :disabled="isLoading"
    ></TextArea>

    <!-- Submit Button -->
    <button
      @click="submitQuery"
      class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md"
      :disabled="isLoading"
    >
      {{ isLoading ? 'Loading...' : 'Submit' }}
    </button>
  </div>
</template>

<style>
.btn-download {
  padding: 8px 15px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 10px;
}

.btn-download:hover {
  background-color: #45a049;
}
</style>
