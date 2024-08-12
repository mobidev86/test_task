<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">Template</h2>
        <form @submit.prevent="submitRating" class="space-y-4">
            <div class="flex flex-col mt-4">

                <ckeditor
                    v-model="editorData"
                    :editor="editor"
                    :config="editorConfig"
                />
            </div>

            <div class="flex items-center gap-2 mt-4">
                <button
                    type="submit"
                    class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600"
                >
                    Save Report Template
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
import { ClassicEditor,	Autoformat,	Bold, Undo,	Italic,	Essentials,	Mention,	Paragraph,	Table,	TableColumnResize,	TableToolbar, FontBackgroundColor, FontColor, FontFamily, FontSize, Link, List } from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';
import 'ckeditor5-premium-features/ckeditor5-premium-features.css';

export default {
    data() {
        return {
            templateId: null,
            editorData: '',
            sessionId: null,
            editor: ClassicEditor,
            editorConfig: {
                plugins: [ Bold, Essentials, Italic, Mention, Paragraph, Undo,Table, TableColumnResize, TableToolbar,Autoformat, FontBackgroundColor, FontColor,FontFamily, FontSize, Link, List ],
                toolbar: [ 'undo', 'redo', '|', 'bold', 'italic', '|', 'insertTable','fontSize','fontColor', 'fontBackgroundColor', 'fontFamily', 'link', 'bulletedList', 'numberedList'],
               
                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
                },
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
                const response = await axios.get(`/report-template`);
                const sessionData = response.data; // Store the fetched data
                if (sessionData.template) {
                    this.editorData = sessionData.template;
                    this.templateId = sessionData.id;
                    
                }
            } catch (error) {
                console.error('Error fetching session details:', error);
            }
        },
        async submitRating() {
            this.errors = {}; 

            try {
                await axios.post('/report-template', {
                    templateId: this.templateId,
                    template: this.editorData
                });
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

<style scoped>
/* Add custom styles here if needed */
.ckeditor {
    width: 100%;
    min-height: 200px;
}
</style>
