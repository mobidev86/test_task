<template>
    <div class="p-4">
        <!-- Button Container -->
        <div class="flex justify-end mb-4 gap-2">
            <router-link to="/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Student
            </router-link>

            <router-link to="/schedule-sessions" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Schedule Sessions
            </router-link>

            <router-link to="/report-template" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Report Template
            </router-link>

            <router-link to="/target-improvement" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Target Improvement
            </router-link>
            
            <router-link to="/generate-reports" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Generate Reports
            </router-link>
            
        </div>
        
        <!-- Table Section -->
        <h2 class="text-xl font-semibold mb-4">Student List</h2>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">First Name</th>
                    <th class="border px-4 py-2">Middle Name</th>
                    <th class="border px-4 py-2">Last Name</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Date of Birth</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>

                <tr v-if="students.length === 0">
                    <td colspan="5" class="border px-4 py-2 text-center text-gray-500">
                        No records found
                    </td>
                </tr>

                <tr v-for="student in students" :key="student.id">
                    <td class="border px-4 py-2">{{ student.first_name }}</td>
                    <td class="border px-4 py-2">{{ student.middle_name }}</td>
                    <td class="border px-4 py-2">{{ student.last_name }}</td>
                    <td class="border px-4 py-2">{{ student.email }}</td>
                    <td class="border px-4 py-2">{{ student.date_of_birth }}</td>
                    <td class="border px-4 py-2">
                        <router-link 
                            :to="`/student-availability/${student.id}`" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            View Availability
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
            students: []
        };
    },
    async created() {
        this.fetchStudents();
    },
    methods: {
        async fetchStudents() {
            const response = await axios.get('/students');
            this.students = response.data;
        },
    }
};
</script>
