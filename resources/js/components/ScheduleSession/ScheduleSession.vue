<template>
    <div class="p-4">
        
        <div class="flex justify-end mb-4 gap-2">
            <router-link to="/schedule-sessions/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Schedule Sessions
            </router-link>

            <router-link to="/" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back
            </router-link>
        </div>
        
        <h2 class="text-xl font-semibold mb-4">Schedule Session List</h2>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">Student Name</th>
                    <th class="border px-4 py-2">Session Date</th>
                    <th class="border px-4 py-2">Start Time</th>
                    <th class="border px-4 py-2">End Time</th>
                    <th class="border px-4 py-2">Repeated</th>
                    <th class="border px-4 py-2">Rating</th>
                </tr>
            </thead>
            <tbody>

                <tr v-if="sessions.length === 0">
                    <td colspan="6" class="border px-4 py-2 text-center text-gray-500">
                        No records found
                    </td>
                </tr>

                <tr v-for="session in sessions" :key="session.id">
                    <td class="border px-4 py-2">{{ session.student.first_name }} {{ session.student.last_name }}</td>
                    <td class="border px-4 py-2">{{ session.session_date }}</td>
                    <td class="border px-4 py-2">{{ session.start_time }}</td>
                    <td class="border px-4 py-2">{{ session.end_time }}</td>
                    <td class="border px-4 py-2"> <input type="checkbox" :checked="session.is_repeated" disabled></td>
                    
                    <td class="border px-4 py-2">
                        <div v-if="session.rating !== null">
                            {{ session.rating }}
                        </div>
                        <button v-else-if="!isActionButtonVisible(session)" class="bg-blue-200 text-white font-bold py-2 px-4 rounded cursor-not-allowed" disabled>
                           Add Rating
                        </button>
                        <router-link v-else :to="`/schedule-sessions/rate/${session.id}`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Rating
                        </router-link>
                        
                    </td>
                    
                    
                </tr>
            </tbody>
        </table>        
    </div>
</template>

<script>
export default {
    data() {
        return {
            students: [],
            sessions: [],
            errors: {}, 
        };
    },
    async created() {
        await this.fetchSessions();
    },
    methods: {
    async fetchSessions() {
        try {
            const response = await axios.get('/sessions');
            this.sessions = response.data;
        } catch (error) {
            console.error('Error fetching sessions:', error);
        }
    },
    isActionButtonVisible(session) {
        const currentDateTime = new Date();
        const sessionDateTime = new Date(`${session.session_date}T${session.end_time}`);
        return currentDateTime > sessionDateTime;
    }, 
}
};
</script>
