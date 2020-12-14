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
                    <label>Cennik</label>
                    <multiselect
                        v-model.lazy="aqua_park_price"
                        :options="prices_list"
                        track-by="_id"
                        label="name"
                        placeholder="Wybierz cennik"
                    ></multiselect>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <b-form-group label="Icon">
                        <media-selector extensions="svg" @media-selected="selectIcon"></media-selector>
                    </b-form-group>

                    <b-img v-if="icon" thumbnail fluid :src="icon"></b-img>

                    <b-button v-if="icon" type="button" variant="danger" @click="removeIcon">Usuń</b-button>
                </div>
            </div>
        </div>

        <div v-for="lang in langs" class="row mt-3">
            <b-input-group :prepend="'Tytuł (' + lang.name + ')'" class="col-md-6">
                <b-form-input v-model.lazy="titles[lang.key]"></b-form-input>
            </b-input-group>
            <b-input-group :prepend="'Opis (' + lang.name + ')'" class="col-md-6">
                <b-form-input v-model.lazy="descriptions[lang.key]"></b-form-input>
            </b-input-group>
        </div>

        <div class="container-grid mt-3">
            <div class="title">
                <div>Grupa</div>
                <div>Sezon niski</div>
                <div>Sezon wysoki</div>
            </div>
            <div v-for="group in aqua_park_price.groups">
                <div v-for="lang in langs" class="body mt-3">
                    <div>
                        <span v-if="group === 'adult'">Dorośli</span>
                        <span v-if="group === 'kid'">Dzieci, młodzież i studenci</span>
                        <span v-if="group === 'senior'">Seniorzy i osoby niepełnosprawne z opiekunem</span>
                        ({{ lang.name }})
                    </div>
                    <b-form-input
                        v-model.lazy="prices[group]['season_low'][lang.key]"
                    ></b-form-input>
                    <b-form-input
                        v-model.lazy="prices[group]['season_high'][lang.key]"
                    ></b-form-input>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: 'pass',
        props : ['_id', 'price'],

        data() {
            return {
                langs: [],
                prices_list: [],
                id: '',
                name: '',
                icon: '',
                titles: {},
                descriptions: {},
                prices: {},
                aqua_park_price: {groups:[]}
            };
        },

        created() {
            this.getLangs();
        },

        computed: {

            url() {
                return this.id ? ('/dashboard/aqua-parks/prices/passes/' + this.id) : '/dashboard/aqua-parks/prices/passes/store'
            }
        },

        methods: {

            selectIcon: function(args) {
                this.icon = args.url
            },

            removeIcon: function() {
                this.icon = ''
            },

            getLangs: function() {
                axios.get('/dashboard/languages/get')
                    .then(res => {
                        this.langs = res.data
                        this.getPrices()
                    }).catch(err => {
                    console.log(err)
                })
            },

            getPrices: function() {
                axios.get('/api/aqua-parks-prices')
                .then(res => {
                    this.prices_list = res.data
                    this.getPass()
                }).catch(err => {
                    console.log(err)
                })
            },

            getPass: function() {
                if (this._id !== '0') {
                    axios.get('/api/aqua-parks/prices/passes?id=' + this._id)
                    .then(res => {
                        this.id   = res.data._id
                        this.name = res.data.name
                        this.icon = res.data.icon

                        this.aqua_park_price = this.getItem(this.prices_list, '_id', res.data.aqua_park_price)

                        if (res.data.titles != null) {
                            this.titles = res.data.titles
                        }
                        if (res.data.descriptions != null) {
                            this.descriptions = res.data.descriptions
                        }
                        if (res.data.prices != null) {
                            this.prices = res.data.prices
                        }
                        this.checkLangs()

                    }).catch(err => {
                        console.log(err)
                    })
                } else {
                    this.aqua_park_price = this.getItem(this.prices_list, '_id', this.price)
                    this.checkLangs()
                }
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

            checkLangs: function() {
                this.langs.forEach(lang => {
                    if (!(lang.key in this.titles)) {
                        this.titles[lang.key] = ''
                    }
                    if (!(lang.key in this.descriptions)) {
                        this.descriptions[lang.key] = ''
                    }
                    this.aqua_park_price.groups.forEach(group => {
                        if (!(group in this.prices)) {
                            this.prices[group] = {'season_low': {}, 'season_high': {}}
                        }
                        if (!(lang.key in this.prices[group]['season_low'])) {
                            this.prices[group]['season_low'][lang.key] = ''
                        }
                        if (!(lang.key in this.prices[group]['season_high'])) {
                            this.prices[group]['season_high'][lang.key] = ''
                        }
                    })
                })
            },

            save: function(e) {
                e.preventDefault()
                let formData = new FormData()
                formData.append('_method', this.id ? 'PUT' : 'POST')
                formData.append('name', this.name)
                formData.append('aqua_park_price', this.aqua_park_price._id)
                formData.append('icon', this.icon)
                formData.append('titles', JSON.stringify(this.titles))
                formData.append('descriptions', JSON.stringify(this.descriptions))
                formData.append('prices', JSON.stringify(this.prices))

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
            }
        }
    }
</script>
