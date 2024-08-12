<template>
    <div class="p-6">
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Session Details</h3>
            <p><strong>Student Name:</strong> <span class="text-gray-700">{{ sessionDetails.studentName }}</span></p>
            <p><strong>Session Date:</strong> <span class="text-gray-700">{{ sessionDetails.sessionDate }}</span></p>
            <p><strong>Start Time:</strong> <span class="text-gray-700">{{ sessionDetails.startTime }}</span></p>
            <p><strong>End Time:</strong> <span class="text-gray-700">{{ sessionDetails.endTime }}</span></p>
        </div>

        <h2 class="text-2xl font-bold mb-4">Rate monitor improvement</h2>
        <form @submit.prevent="submitRating" class="space-y-4">
            <div class="flex flex-col items-center">
                <div class="font-semibold mb-2">Rating (0-10)</div>
                <input
                    type="range"
                    min="0"
                    max="10"
                    step="1"
                    v-model="rating"
                    class="w-full"
                />
                <div class="text-center mt-2 text-lg">{{ rating }}</div>
            </div>

            <div class="flex items-center gap-2 mt-4">
                <button
                    type="submit"
                    class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600"
                >
                    Submit Rating
                </button>
                <router-link to="/schedule-sessions" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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
            rating: 0, 
            sessionDetails: {
                studentName: '',
                sessionDate: '',
                startTime: '',
                endTime: '',
            },
            errors: {}
        };
    },
    created() {
        this.sessionId = this.$route.params.id;
        this.fetchSessionDetails();
    },
    methods: {
        async fetchSessionDetails() {
            try {
                const response = await axios.get(`/session-details/${this.sessionId}`);
                const sessionData = response.data; 
                this.sessionDetails = {
                    studentName: `${sessionData.student.first_name} ${sessionData.student.last_name}`,
                    sessionDate: sessionData.session_date,
                    startTime: sessionData.start_time,
                    endTime: sessionData.end_time
                };
            } catch (error) {
                console.error('Error fetching session details:', error);
            }
        },
        async submitRating() {
            this.errors = {}; 

            try {
                await axios.post('/session/submit-rating', {
                    sessionId: this.sessionId,
                    rating: this.rating
                });

                this.rating = 0;
                this.$router.push('/schedule-sessions');
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    const responseData = error.response.data;
                    this.errors = responseData.messages || {};
                } else {
                    console.error('Error submitting rating:', error);
                }
            }
        }
    }
};
</script>

