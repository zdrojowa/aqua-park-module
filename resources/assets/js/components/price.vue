<template>
    <div>
        <slot></slot>

        <b-nav align="right">
            <b-nav-item>
                <b-button type="button" variant="primary" @click="save">Zapisz</b-button>
            </b-nav-item>
        </b-nav>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label>Nazwa</label>
                    <b-form-input type="text" v-model.lazy="name" :state="name.length > 0"></b-form-input>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Aquapark</label>
                    <multiselect
                        v-model.lazy="aqua_park"
                        :options="aqua_parks"
                        track-by="_id"
                        label="name"
                        placeholder="Wybierz aquapark"></multiselect>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">
                <div class="form-group">
                    <b-form-group label="Logo">
                        <media-selector extensions="svg" @media-selected="selectIcon"></media-selector>
                    </b-form-group>

                    <b-img v-if="icon" thumbnail fluid :src="icon"></b-img>

                    <b-button v-if="icon" type="button" variant="danger" @click="removeIcon">Usuń</b-button>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">
                <b-form-group label="Dostępny dla">
                    <b-form-checkbox-group
                        v-model="groups"
                        :options="options"
                        name="flavour-1"
                    ></b-form-checkbox-group>
                </b-form-group>
            </div>
        </div>

        <div v-for="lang in langs">
            <div class="row mt-3">
                <b-input-group :prepend="'Tytuł (' + lang.name + ')'" class="col-md-12">
                    <b-form-input v-model.lazy="titles[lang.key]"></b-form-input>
                </b-input-group>
            </div>
            <div class="row mt-3">
                <b-input-group :prepend="'Opis (' + lang.name + ')'" class="col-md-12">
                    <b-form-input v-model.lazy="descriptions[lang.key]"></b-form-input>
                </b-input-group>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: 'price',
        props : ['_id', 'park'],

        data() {
            return {
                aqua_parks: [],
                options: [
                    {text: 'Dorośli', value: 'adult'},
                    {text: 'Dzieci, młodzież i studenci', value: 'kid'},
                    {text: 'Seniorzy i osoby niepełnosprawne z opiekunem', value: 'senior'},
                ],
                langs: [],
                id: 0,
                name: '',
                aqua_park: {},
                icon: '',
                titles: {},
                descriptions: {},
                groups: []
            };
        },

        created() {
            this.getAquaparks();
        },

        computed: {

            url() {
                return this.id ? ('/dashboard/aqua-parks/prices/' + this.id) : '/dashboard/aqua-parks/prices/store'
            }
        },

        methods: {

            selectIcon: function(args) {
                this.icon = args.url
            },

            removeIcon: function() {
                this.icon = ''
            },

            getAquaparks: function() {
                axios.get('/api/aqua-parks')
                .then(res => {
                    this.aqua_parks = res.data
                    if (this.park) {
                        this.aqua_park = this.getItem(this.aqua_parks, '_id', this.park)
                    }
                    this.getLangs()
                }).catch(err => {
                    console.log(err)
                })
            },

            getLangs: function() {
                axios.get('/dashboard/languages/get')
                    .then(res => {
                        this.langs = res.data
                        this.getPrice()
                    }).catch(err => {
                    console.log(err)
                })
            },

            getItem: function(arr, key, val) {

                let item = val

                arr.forEach(it => {
                    if (it[key] === val) {
                        item = it
                    }
                })

                return item
            },

            getPrice: function() {
                if (this._id !== '0') {
                    axios.get('/api/aqua-parks-prices?id=' + this._id)
                    .then(res => {
                        this.id   = res.data._id
                        this.name = res.data.name
                        this.icon = res.data.icon

                        this.aqua_park = this.getItem(this.aqua_parks, '_id', res.data.aqua_park)

                        if (res.data.titles != null) {
                            this.titles = res.data.titles
                        }
                        if (res.data.descriptions != null) {
                            this.descriptions = res.data.descriptions
                        }
                        if (res.data.groups != null) {
                            this.groups = res.data.groups
                        }
                        this.checkLangs()

                    }).catch(err => {
                        console.log(err)
                        this.checkLangs()
                    })
                }
            },

            checkLangs: function() {
                this.langs.forEach(lang => {
                    if (!(lang.key in this.titles)) {
                        this.titles[lang.key] = ''
                    }
                    if (!(lang.key in this.descriptions)) {
                        this.descriptions[lang.key] = ''
                    }
                })
            },

            validate: function(e) {
                return !!this.name

            },

            save: function(e) {
                e.preventDefault()

                if (this.validate) {
                    let formData = new FormData()
                    formData.append('_method', this.id ? 'PUT' : 'POST')
                    formData.append('name', this.name)
                    formData.append('aqua_park', this.aqua_park._id)
                    formData.append('icon', this.icon)
                    formData.append('titles', JSON.stringify(this.titles))
                    formData.append('descriptions', JSON.stringify(this.descriptions))
                    formData.append('groups', JSON.stringify(this.groups))

                    axios.post(this.url, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(res => {
                        window.location = res.data.redirect
                    }).catch(err => {
                        console.log(err)
                    });
                } else {
                    return false
                }
            }
        },

        watch: {
            name() {
                this.validate()
            }
        }
    }
</script>
