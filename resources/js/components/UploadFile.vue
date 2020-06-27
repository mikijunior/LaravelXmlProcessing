<template>
    <div class="uploadForm">
        <b-form-file
            v-model="file"
            :state="Boolean(file)"
            placeholder="Choose a Excel file or drop it here..."
            drop-placeholder="Drop Excel file here..."
            accept=".xls, .xlsx"
            required
        />
        <b-button
            @click.prevent="sendFile"
            class="ml-2 mr-2"
        >
            Upload
        </b-button>
    </div>
</template>

<script>
    export default {
        name: "UploadFile",
        data() {
            return {
                file: null,
            };
        },
        methods: {
            sendFile() {
                if (this.file) {
                    let formData = new FormData();
                    formData.append('file', this.file);

                    axios.post(
                        'api/send-file',
                        formData,
                        {
                            headers: {
                                'content-type':
                                    'multipart/form-data',
                            }
                        }
                    ).then(response => {
                        // test
                    })
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .uploadForm {
        height: 100vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
</style>
