<template>
    <div>
        <slot></slot>

        <b-nav align="right">
            <b-nav-item>
                <b-button type="button" variant="success" v-b-modal.modal>Dodaj</b-button>
            </b-nav-item>
            <b-nav-item>
                <b-button type="bytton" variant="primary" @click="save">Zapisz</b-button>
            </b-nav-item>
        </b-nav>

        <div class="row item-container">
            <draggable class="list-group" ghost-class="ghost" :list="icons">
                <div class="list-group-item" v-for="(element, index) in icons" :key="index">
                    <div class="item file-item">
                        <span>{{ getName(element.id) }}</span>
                        <div class="gallery-form px-3">
                            <div v-for="lang in langs" class="row mt-3">
                                <b-input-group :prepend="'Tytuł (' + lang.name + ')'" class="col-md-6">
                                    <b-form-input v-model.lazy="element.titles[lang.key]"></b-form-input>
                                </b-input-group>
                                <b-input-group :prepend="'Opis (' + lang.name + ')'" class="col-md-6">
                                    <b-form-input v-model.lazy="element.descriptions[lang.key]"></b-form-input>
                                </b-input-group>
                            </div>
                        </div>
                        <button type="button" aria-label="Close" class="close" @click="remove(index)">×</button>
                    </div>
                </div>
            </draggable>
        </div>

        <b-modal id="modal" title="Dodawanie" hide-footer>
            <b-nav align="right">
                <b-nav-item>
                    <b-button type="button" variant="success" @click="add">Zapisz</b-button>
                </b-nav-item>
            </b-nav>
            <div class="row">

                <div class="col-md-12">
                    <div class="form-group">
                        <label>Ikonki</label>
                        <multiselect :options="options" track-by="id" label="name" placeholder="Wybierz ikonkę" v-model.lazy="icon"></multiselect>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
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
                options: [],
                icon: {},
                langs: [],
                icons: []
            };
        },

        created() {
            this.getLangs()
            this.getIcons()
        },

        methods: {
            getIcons: function() {
                axios.get('/api/icons')
                .then(res => {
                    this.options = res.data
                }).catch(err => {
                    console.log(err)
                })
            },

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
                    if (res.data.icons != null) {
                        this.icons = res.data.icons
                        this.icons.forEach(icon => {
                            this.checkLangs(icon.titles)
                            this.checkLangs(icon.descriptions)
                        });
                    }
                }).catch(err => {
                    console.log(err)
                })
                this.checkLangs(this.conference_link)
            },

            checkLangs: function(field) {
                this.langs.forEach(lang => {
                    if (!(lang.key in field)) {
                        field[lang.key] = ''
                    }
                })
            },

            remove(index) {
                this.icons.splice(index, 1)
            },

            add() {
                this.icons.push({id: this.icon.id, titles: {}, descriptions: {}})
            },

            save: function() {
                let formData = new FormData()
                formData.append('_method', 'PUT')
                formData.append('icons', JSON.stringify(this.icons))

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
