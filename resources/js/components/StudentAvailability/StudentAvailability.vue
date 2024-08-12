<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">Student Availability</h2>
        <form @submit.prevent="updateAvailability" class="space-y-4">
            <div class="grid grid-cols-2 gap-4 items-center">
                <!-- Days Column -->
                <div>
                    <div class="font-semibold mb-2">Days</div>
                    <div v-for="day in daysOfWeek" :key="day" class="text-sm mb-2">
                        {{ day }}
                    </div>
                </div>

                <!-- Availability Column -->
                <div>
                    <div class="font-semibold mb-2">Availability</div>
                    <div v-for="day in daysOfWeek" :key="'checkbox-' + day" class="flex items-center">
                        <input
                            type="checkbox"
                            :id="day"
                            :value="day"
                            v-model="selectedDays"
                            class="form-checkbox h-5 w-5 text-blue-600 mb-2"
                        />
                    </div>
                </div>
            </div> 

            <div class="flex items-center gap-2 mt-4">
                <button
                    type="submit"
                    class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600"
                >
                    Update Availability
                </button>
                <router-link to="/" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back
                </router-link>
            </div>

            <div class="flex flex-col gap-2 mt-4">
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        disabled
                        checked
                        class="form-checkbox h-5 w-5 text-blue-600"
                    />
                    <label class="ml-2 text-sm">This indicates available</label>
                </div>
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        disabled
                        class="form-checkbox h-5 w-5 text-gray-600"
                    />
                    <label class="ml-2 text-sm">This indicates not-available</label>
                </div>
            </div>
            
        </form>
    </div>
</template>



<script>
import axios from 'axios';

export default {
    data() {
        return {
            daysOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            selectedDays: [],
            errors: {}
        };
    },
    async created() {
        this.studentId = this.$route.params.id; // Get student ID from route params
        await this.fetchAvailabilities();
    },
    methods: {
        async fetchAvailabilities() {
            try {
                const response = await axios.get(`/student-availabilities/${this.studentId}`);
                // Extract the days_of_week from the response
                const daysOfWeekFromResponse = response.data.map(item => item.day_of_week);
                this.selectedDays = daysOfWeekFromResponse;
            } catch (error) {
                console.error('Error fetching student availability:', error);
            }
        },
        async updateAvailability() {
            this.errors = {}; // Clear previous errors

            try {
                await axios.post('/student-availabilities', {
                    student_id: this.studentId,
                    days_of_week: this.selectedDays
                });

                // Clear form fields
                this.selectedDays = [];

                await this.fetchAvailabilities();

            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Assuming 422 Unprocessable Entity for validation errors
                    const responseData = error.response.data;
                    this.errors = responseData.messages || {};
                } else {
                    // Handle other errors (e.g., network issues, server errors)
                    console.error('Error updating student availability:', error);
                }
            }
        }
    }
};
</script>

<style scoped>
/* Add custom styles here if needed */
</style>
