// import PostsList from "./Pages/Post/posts.vue";
// import CreatePost from "./Pages/Post/createpost.vue";
// import EditPost from "./Pages/Post/editpost.vue";

import AddStudent from './components/Students/AddStudent.vue';
import Students from './components/Students/Students.vue';
import StudentAvailability from './components/StudentAvailability/StudentAvailability.vue';
import ScheduleSession from './components/ScheduleSession/ScheduleSession.vue';
import AddScheduleSession from './components/ScheduleSession/AddScheduleSession.vue';
import RateScheduleSession from './components/ScheduleSession/RateScheduleSession.vue';
import ReportTemplate from './components/Reports/ReportTemplate.vue';
import UploadReport from './components/Reports/UploadReport.vue';
import Reports from './components/Reports/Reports.vue'



export const routes = [
    {
        path: "/",
        name: "Students",
        component: Students,
    },
    {
        path: "/create",
        name: "AddStudent",
        component: AddStudent,
    },
    {
        path: "/student-availability/:id",
        name: "StudentAvailability",
        component: StudentAvailability,
        props: true
    },
    {
        path: "/schedule-sessions",
        name: "ScheduleSession",
        component: ScheduleSession,
    },
    {
        path: "/schedule-sessions/create",
        name: "AddScheduleSession",
        component: AddScheduleSession,
    },
    {
        path: "/schedule-sessions/rate/:id",
        name: "RateScheduleSession",
        component: RateScheduleSession,
        props: true
    },
    {
        path: "/report-template",
        name: "ReportTemplate",
        component: ReportTemplate,
    },
    {
        path: "/target-improvement",
        name: "UploadReport",
        component: UploadReport,
    },
    {
        path: "/generate-reports",
        name: "Reports",
        component: Reports,
    }
];
