<template>
    <div>
        <h2 class="text-xl font-semibold mb-4">Create Schedule Session</h2> 
        <form @submit.prevent="scheduleSession" class="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="studentId">
                  Select Student
                </label>
                <select v-model="studentId" id="studentId" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option v-for="student in students" :key="student.id" :value="student.id">
                        {{ student.first_name }} {{ student.last_name }}
                    </option>
                </select>
                <p v-if="errors.student_id" class="text-red-500 text-xs italic">{{ errors.student_id.join(', ') }}</p>
            </div>

            <!-- Arrange Start Time and End Time in one row -->
            <div class="mb-4 flex gap-2">
                <div class="w-1/3">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="sessionDate">
                    Session Date
                </label>
                <input v-model="sessionDate" id="sessionDate" type="date" placeholder="Session Date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p v-if="errors.session_date" class="text-red-500 text-xs italic">{{ errors.session_date.join(', ') }}</p>

                </div>

                <div class="w-1/3">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="startTime">
                        Start Time
                    </label>
                    <input v-model="startTime" id="startTime" type="time" placeholder="Start Time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p v-if="errors.start_time" class="text-red-500 text-xs italic">{{ errors.start_time.join(', ') }}</p>
                </div>

                <div class="w-1/3">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Duration
                    </label>
                    <p class="text-gray-700 text-sm font-semibold">{{ endTime }}</p>
                </div>

            </div>

            

            <div class="mb-4 flex items-center">
                <input type="checkbox" v-model="isRepeated" id="isRepeated" class="mr-2">
                <label class="text-gray-700 text-sm font-bold" for="isRepeated">Repeated</label>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Create Schedule Session
                </button>
                <router-link to="/schedule-sessions" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back
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
            sessionDate: '',
            startTime: '',
            isRepeated: false,
            errors: {}, 
        };
    },
    computed: {
        endTime() {
            if (!this.startTime) return '';

            const [hours, minutes] = this.startTime.split(':').map(Number);

            const endTime = new Date();
            endTime.setHours(hours, minutes + 15);
            
            return endTime.toTimeString().slice(0, 5)+ ' - (15 min)';
        }
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
        async scheduleSession() {
            try {
                await axios.post('/sessions', {
                    student_id: this.studentId,
                    session_date: this.sessionDate,
                    start_time: this.startTime,
                    is_repeated: this.isRepeated
                });

                // Reset form fields and errors after a successful submission
                this.studentId = '';
                this.sessionDate = '';
                this.startTime = '';
                this.isRepeated = false;
                this.errors = {};
                this.$router.push('/schedule-sessions');
            } catch (error) {
                if (error.response && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    console.error('Error scheduling session:', error);
                }
            }
        }
    }
};
</script>
