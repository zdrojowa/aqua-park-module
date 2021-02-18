<template>
    <div>
        <slot></slot>
        <b-nav align="right">
            <b-nav-item>
                <b-button type="bytton" variant="primary" @click="save">Zapisz</b-button>
            </b-nav-item>
        </b-nav>

        <div class="row item-container">
            <div class="gallery-form px-3">
                <div v-for="lang in langs" class="row mt-3">
                    <div class="form-group">
                        <div class="form-group">
                            <label>{{ lang.name }}</label>
                            <ckeditor :editor="editor" v-model="disclaimer[lang.key]" :config="editorConfig"></ckeditor>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
    import MyCustomUploadAdapterPlugin from "./UploadAdapterPlugin";

    export default {
        name: 'legend-info',
        props: {
            id: {
                required: true,
                type: String
            }
        },

        data() {
            return {
                editor: ClassicEditor,
                editorConfig: {
                    extraPlugins: [ MyCustomUploadAdapterPlugin ]
                },
                langs: [],
                disclaimer: {}
            };
        },

        created() {
            this.getLangs()
        },

        methods: {
            getName: function(id) {
                let name = ''
                this.options.forEach(item => {
                    if (item.id === id) {
                        name = item.name
                    }
                })
                return name
            },

            getLangs: function() {
                axios.get('/dashboard/languages/get')
                .then(res => {
                    this.langs = res.data
                    this.getItem()
                }).catch(err => {
                    console.log(err)
                })
            },

            getItem() {
                axios.get('/api/aqua-parks?id=' + this.id)
                .then(res => {
                    if (res.data.disclaimer != null) {
                        this.disclaimer = JSON.parse(res.data.disclaimer)
                    }

                    this.checkLangs(this.disclaimer)
                }).catch(err => {
                    console.log(err)
                })
            },

            checkLangs: function(disclaimer) {
                this.langs.forEach(lang => {
                    if (!(lang.key in disclaimer)) {
                        disclaimer[lang.key] = ''
                    }
                })
            },

            save: function() {
                let formData = new FormData()
                formData.append('_method', 'PUT')
                formData.append('disclaimer', JSON.stringify({...this.disclaimer}))

                axios.post('/dashboard/aqua-parks/' + this.id, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(res => {
                    this.$bvToast.toast('Legenda zaktualizowana', {
                        title: `Legenda`,
                        variant: 'success',
                        solid: true
                    })
                }).catch(err => {
                    this.$bvToast.toast(err, {
                        title: `Błąd`,
                        variant: 'danger',
                        solid: true
                    })
                })
            }
        }
    }
</script>
