<template>
    <div>
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

            <b-alert
                :show="dismissCountDown"
                dismissible
                variant="warning"
                @dismissed="dismissCountDown=0"
                @dismiss-count-down="countDownChanged"
            >
                <p>Imported {{ importedEntries }} rows. Duplicates: {{ duplicateEntries }}</p>
                <b-progress
                    variant="warning"
                    :max="dismissSecs"
                    :value="dismissCountDown"
                    height="4px"
                ></b-progress>
            </b-alert>
        </div>
    </div>
</template>

<script>
    export default {
        name: "UploadFile",
        data() {
            return {
                file: null,
                dismissSecs: 10,
                dismissCountDown: 0,
                importedEntries: 0,
                duplicateEntries: 0,
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
                        this.importedEntries = response.data.imported;
                        this.duplicateEntries = response.data.duplicatedRows;
                        this.dismissCountDown = this.dismissSecs;
                    })
                }
            },
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown;
            },
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
