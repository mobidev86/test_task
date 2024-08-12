<template>
    <div class="max-w-lg mx-auto mt-10">
      <h1 class="text-2xl font-bold text-gray-700 mb-6">Upload MS-Docx File</h1>
      <form @submit.prevent="submitFile">
  
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="studentId">
            Select Student
          </label>
          <select v-model="studentId" id="studentId" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="" disabled>Select a student</option>
            <option v-for="student in students" :key="student.id" :value="student.id">
              {{ student.first_name }} {{ student.last_name }}
            </option>
          </select>
          <p v-if="errors.student_id" class="text-red-500 text-xs italic">{{ errors.student_id.join(', ') }}</p>
        </div>
  
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="file">
            Choose File
          </label>
          <input 
            type="file" 
            id="file" 
            @change="handleFileUpload" 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
          />
          <p v-if="errors.file" class="text-red-500 text-xs italic">{{ errors.file.join(', ') }}</p>
        </div>
  
        <div class="flex items-center gap-2">
          <button 
            type="submit" 
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Upload
          </button>
          <router-link 
            to="/" 
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Cancel
          </router-link>
        </div>
      </form>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        students: [],
        studentId: '',
        file: null,
        errors: {},
      };
    },
    async created() {
      await this.fetchStudents();
    },
    methods: {
      async fetchStudents() {
        try {
          const response = await axios.get('/students');
          this.students = response.data;
        } catch (error) {
          console.error('Error fetching students:', error);
        }
      },
      handleFileUpload(event) {
        const selectedFile = event.target.files[0];
        if (selectedFile) {
          this.errors.file = null; // Clear previous errors
          this.file = selectedFile;
        }
      },
      async submitFile() {
        this.errors = {};
  
        let formData = new FormData();
        formData.append('file', this.file);
        formData.append('student_id', this.studentId);
  
        try {
          await axios.post('/upload-docx', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
          alert('File uploaded successfully!');
          this.studentId = '';
          this.file = null;
        } catch (error) {
                if (error.response && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    console.error('Error scheduling session:', error);
                }
            }
      },
    },
  };
  </script>
  