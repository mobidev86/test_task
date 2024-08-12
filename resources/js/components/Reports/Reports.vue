<template>
    <div>
        <h2 class="text-xl font-semibold mb-4">Generate Reports</h2> 
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
                <div class="w-1/2">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="fromDate">
                    From Date
                </label>
                <input v-model="fromDate" id="fromDate" type="date" placeholder="From Date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p v-if="errors.from_date" class="text-red-500 text-xs italic">{{ errors.from_date.join(', ') }}</p>

                </div>


                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="toDate">
                        To Date
                    </label>
                    <input v-model="toDate" id="toDate" type="date" placeholder="To Date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p v-if="errors.to_date" class="text-red-500 text-xs italic">{{ errors.to_date.join(', ') }}</p>
                </div>
            </div>


            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="splitSession">
                    Split Session in Minute
                </label>
                <select v-model="splitSession" id="splitSession" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option v-for="option in splitSessionOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
                <p v-if="errors.split_session" class="text-red-500 text-xs italic">{{ errors.split_session.join(', ') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Generate report
                </button>
                <router-link to="/" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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
            fromDate: '',
            toDate: '',
            splitSession: '15',
            splitSessionOptions: [
                { value: '15', label: '15 min' },
                { value: '10', label: '10 min' },
                { value: '5', label: '5 min' },
                { value: '2', label: '2 min' },
            ],
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
       
        async scheduleSession() {
            try {
                await axios.post('/generate-report', {
                    student_id: this.studentId,
                    from_date: this.fromDate,
                    to_date: this.toDate,
                    split_session: this.splitSession,
                }).then(response => {
                    const zipFilePath = response.data.zip_file_path;
                    window.location.href = zipFilePath;
                    
                });

                this.studentId = '';
                this.fromDate = '';
                this.toDate = '';
                this.splitSession = '15';
                this.errors = {};
                
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
