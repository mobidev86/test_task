<template>
    <div>
        <form @submit.prevent="addStudent">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="firstName">
                    First Name
                </label>
                <input v-model="firstName" id="firstName" type="text" placeholder="First Name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p v-if="errors.first_name" class="text-red-500 text-xs italic">{{ errors.first_name.join(', ') }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="middleName">
                    Middle Name
                </label>
                <input v-model="middleName" id="middleName" type="text" placeholder="Middle Name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <!-- Optional: Display errors for middle name if needed -->
                <p v-if="errors.middle_name" class="text-red-500 text-xs italic">{{ errors.middle_name.join(', ') }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="lastName">
                    Last Name
                </label>
                <input v-model="lastName" id="lastName" type="text" placeholder="Last Name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p v-if="errors.last_name" class="text-red-500 text-xs italic">{{ errors.last_name.join(', ') }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input v-model="email" id="email" type="email" placeholder="Email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p v-if="errors.email" class="text-red-500 text-xs italic">{{ errors.email.join(', ') }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="dateOfBirth">
                    Date of Birth
                </label>
                <input v-model="dateOfBirth" id="dateOfBirth" type="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p v-if="errors.date_of_birth" class="text-red-500 text-xs italic">{{ errors.date_of_birth.join(', ') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Add Student
                </button>
                <router-link to="/" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back
                </router-link>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            firstName: '',
            middleName: '',
            lastName: '',
            dateOfBirth: '',
            email: '',
            errors: {}
        };
    },
    methods: {
        async addStudent() {
            this.errors = {}; // Clear previous errors

            try {
                await axios.post('/students', {
                    first_name: this.firstName,
                    middle_name: this.middleName,
                    last_name: this.lastName,
                    date_of_birth: this.dateOfBirth,
                    email: this.email
                });
                
                // Clear form fields
                this.firstName = '';
                this.middleName = '';
                this.lastName = '';
                this.dateOfBirth = '';
                this.email = '';

                this.$router.push('/');
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Assuming 422 Unprocessable Entity for validation errors
                    const responseData = error.response.data;
                    this.errors = responseData.messages || {};
                } else {
                    // Handle other errors (e.g., network issues, server errors)
                    console.error('Error adding student:', error);
                }
            }
        }
    }
};
</script>
