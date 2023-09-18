<template>
    <div class="row">
        <div class="col-md-9 px-2" style="height: 75vh; overflow: auto">
            <table class="table table-bordered">
                <thead class="text-center">
                    <th>ID</th>
                    <th>Domain</th>
                    <th v-if="idBO == 0">BO</th>
                    <th v-if="idBO == 0">Kontak</th>
                    <th>Status</th>
                    <th v-if="subbed"></th>
                </thead>
                <tbody class="text-center">
                    <tr v-for="list in listWebsites" :key="list.id">
                        <td class="px-1">{{ list.id }}</td>
                        <td class="text-start px-1">{{ list.domain }}</td>
                        <td v-if="idBO == 0" class="px-1">{{ list.nama_bo }}</td>
                        <td v-if="idBO == 0" class="px-1">{{ list.kontak }}</td>
                        <td style="width: 25%" v-if="subbed" class="px-1">
                            <!-- <span v-if="list.status == 'allowed'" class="fas fa-lg fa-check text-success"></span> -->
                            <strong v-if="list.status == 'allowed'" class="text-success">OK</strong>
                            <!-- <span v-else-if="list.status == 'blocked'" class="fas fa-lg fa-times text-danger"></span> -->
                            <strong v-else-if="list.status == 'blocked'" class="text-danger">Block</strong>
                            <strong v-else-if="list.status == 'Invalid domain format'" class="text-danger opacity-75">Format domain salah</strong>
                            <span v-else>{{ list.status }}</span>
                        </td>
                        <td v-else>
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        </td>

                        <td class="px-0" style="width: 25%" v-if="subbed">
                            <!-- SUBSCRIBED -->
                            <button class="btn btn-sm py-0 px-1">
                                <span class="fas fa-sm fa-edit text-dark" data-bs-toggle="modal" data-bs-target="#editDomain" @click="getDomainId(list.id, list.domain)"></span>
                            </button>
                            <button class="btn btn-sm py-0 px-1">
                                <span class="fas fa-sm fa-trash text-danger" data-bs-toggle="modal" data-bs-target="#deleteDomain" @click="getDomainId(list.id, list.domain)"></span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-3 p-1">
            <div class="row">
                <div class="col-6 col-md-12 mb-2">
                    <strong class="m-0">Terakhir Diperiksa:</strong>
                    <p v-if="subbed" class="m-0">{{ waktuCek }}</p>
                    <p v-else class="text-danger">Layanan Berakhir</p>
                </div>
                <div class="col-6 col-md-12 mb-2">
                    <strong class="m-0">Layanan Berakhir:</strong>
                    <h4 class="m-0" v-if="subbed">
                        <strong>{{ subscription }}</strong>
                    </h4>
                    <p v-else class="text-danger m-0">
                        Perpanjang akun melalui menu <i class="fas fa-user-circle"></i>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addDomain" tabindex="-1" aria-labelledby="addDomainLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-0 border-0">
                    <h5 class="modal-title" id="addDomainLabel">Tambah Domain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" ref="domainName" class="form-control m-0" placeholder="Contoh: namadomain.com">
                    <div v-if="errorAddDomain" class="alert alert-danger py-1 px-2 mt-1">
                        <small>{{ errMsg }}</small>
                    </div>
                    <div v-if="successAddDomain" class="alert alert-success py-1 px-2 mt-1">
                        <small>{{ successMsg }}</small>
                    </div>
                    <br>
                    <small class="text-dark m-0">
                        Pastikan:
                        <ul class="m-0 ps-4">
                            <li class="m-0">Tidak mencantumkan "http://"</li>
                            <li class="m-0">Alamat berupa nama domain, bukan IP</li>
                        </ul>
                    </small>
                </div>
                <div class="modal-footer py-0 border-0">
                    <button type="button" class="btn text-success px-0" @click="Tambah">
                        <strong>TAMBAHKAN</strong>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editDomain" tabindex="-1" aria-labelledby="editDomainLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-0 border-0">
                    <h5 class="modal-title" id="editDomainLabel">Ubah Domain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" ref="domainNameEdit" class="form-control m-0" placeholder="Contoh: namadomain.com" :value="domainName">
                    <div v-if="errorAddDomain" class="alert alert-danger py-1 px-2 mt-1">
                        <small>{{ errMsg }}</small>
                    </div>
                    <div v-if="successAddDomain" class="alert alert-success py-1 px-2 mt-1">
                        <small>{{ successMsg }}</small>
                    </div>
                    <br>
                    <small class="text-dark m-0">
                        Pastikan:
                        <ul class="m-0 ps-4">
                            <li class="m-0">Tidak mencantumkan "http://"</li>
                            <li class="m-0">Alamat berupa nama domain, bukan IP</li>
                        </ul>
                    </small>
                </div>
                <div class="modal-footer py-0 border-0">
                    <button type="button" class="btn text-success px-0" @click="Ubah">
                        <strong>UBAH</strong>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteDomain" tabindex="-1" aria-labelledby="deleteDomainLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content text-dark">
                <div class="modal-header py-0 border-0">
                    <h5 class="modal-title" id="deleteDomainLabel">Hapus Domain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Yakin ingin menghapus domain <strong>{{ domainName }}</strong> dari list?
                </div>
                <div class="modal-footer py-0 border-0">
                    <button type="button" class="btn text-danger px-0" @click="Hapus(idDomain)">
                        <strong>HAPUS</strong>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dalamPengembangan" tabindex="-1" aria-labelledby="dalamPengembanganLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content text-dark">
                <div class="modal-header py-0 border-0">
                    <h5 class="modal-title" id="dalamPengembanganLabel">Dalam Pengembangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Fitur masih dalam masa pengembangan.
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            idBO: null,
            namaBO: null,
            subscription: null,
            errorAddDomain: false,
            errMsg: '',
            successAddDomain: false,
            successMsg: '',
            idDomain: null,
            domainName: null,
            waktuCek: null,
            listWebsites: {},
            subbed: false,
        }
    },

    methods: {
        Initiate() {
            this.getSession()
            this.getWebsites()
            setTimeout(() => {
                this.checkWebsites(this.listWebsites)
            }, 5000)

            setInterval(() => {
                this.checkWebsites(this.listWebsites)
            }, 3600000);
        },

        Tambah() {
            this.errorAddDomain = false
            this.errMsg = ''
            this.successAddDomain = false
            this.successMsg = ''

            if (this.$refs.domainName.value == '' || this.$refs.domainName.value == null) {
                this.errorAddDomain = true
                this.errMsg = 'Nama domain wajib diisi.'
            } else {
                let data = {
                    domain: this.$refs.domainName.value,
                    namaBO: this.idBO
                }
                axios
                .post('/add_domain', { data: JSON.stringify(data) })
                .then((res) => {
                    this.errorAddDomain = false
                    this.errMsg = ''
                    this.successAddDomain = true
                    this.successMsg = 'Domain berhasil ditambahkan.'
                    this.getWebsites()
                    setTimeout(() => {
                        $('#addDomain').modal('hide')
                    }, 2000)
                })
                .catch((err) => {
                    console.log(err.response)
                    this.errorAddDomain = true
                    if (err.response.data == 'limit') {
                        this.errMsg = 'Anda melebihi batas domain untuk paket layanan yang Anda pilih.'
                    } else if (err.response.data == 'endsub') {
                        this.errMsg = 'Masa berlangganan Anda sudah berakhir. Harap perpanjang terlebih dahulu.'
                    } else {
                        this.errMsg = err.response.data
                    }
                })
            }

        },

        Ubah() {
            this.errorAddDomain = false
            this.errMsg = ''
            this.successAddDomain = false
            this.successMsg = ''

            if (this.$refs.domainNameEdit.value == '' || this.$refs.domainNameEdit.value == null) {
                this.errorAddDomain = true
                this.errMsg = 'Nama domain wajib diisi.'
            } else {
                let data = {
                    domain: this.$refs.domainNameEdit.value,
                    idDomain: this.idDomain,
                    namaBO: this.idBO
                }
                axios
                .post('/ubah_domain', { data: JSON.stringify(data) })
                .then((res) => {
                    this.errorAddDomain = false
                    this.errMsg = ''
                    this.successAddDomain = true
                    this.successMsg = 'Domain berhasil diubah.'
                    this.domainName = this.$refs.domainNameEdit.value
                    this.getWebsites()
                    setTimeout(() => {
                        $('#editDomain').modal('hide')
                    }, 2000)
                })
                .catch((err) => {
                    console.log(err.response)
                    this.errorAddDomain = true
                    if (err.response.data == 'endsub') {
                        this.errMsg = 'Masa berlangganan Anda sudah berakhir. Harap perpanjang terlebih dahulu.'
                    } else {
                        this.errMsg = err.response.data
                    }
                })
            }

        },

        getDomainId(id, domain) {
            this.domainName = domain
            this.idDomain = id
        },

        Hapus(id) {
            let data = {id: id}
            axios
            .post('/delete_domain', {data: JSON.stringify(data)})
            .then((res) => {
                if (res.data == 'ok') {
                    $('#deleteDomain').modal('hide')
                    this.getWebsites()
                }
            })
        },

        getSession() {
            axios
            .get('/get_session')
            .then((res) => {
                this.idBO = res.data.id_bo
                this.namaBO = res.data.nama_bo

                let date = new Date(res.data.subscription)
                let subEnd = new Date(date.setMonth(date.getMonth() + 1))
                let localEnd = subEnd.toLocaleString()
                this.subscription = localEnd.substring(0, 10)
                let today = new Date()
                this.checkSubs(today, subEnd)
            })
        },

        checkSubs(today, subEnd) {
            if(today.getTime() > subEnd.getTime())
                this.subbed = false
            else
                this.subbed = true
        },

        getWebsites() {
            axios
            .get('/get_websites')
            .then(res => {
                this.listWebsites = res.data
                this.listWebsites.forEach(data => {
                    const text = "" + data.id + ""
                    data.id = text.padStart(4, '0')
                    data.status = 'Check...'
                })
            })
            .finally(() => {
                this.checkWebsites(this.listWebsites)
                this.botTeleCheck(this.listWebsites)
            })
        },

        checkWebsites(list) {
            list.forEach((data, index) => {
                let domain = { domain: data.domain }
                axios
                .post('/fetch_data', domain)
                .then(res => {
                    if (res.data.status != undefined)
                        this.listWebsites[index].status = res.data.status
                    else
                        this.listWebsites[index].status = res.data.error
                })
                .catch(err => {
                    console.log(err.response)
                })
            })
            this.getLastUpdate()
        },

        getLastUpdate() {
            const fullDate = new Date()
            const date = fullDate.toLocaleString()
            this.waktuCek = date
        },

        botTeleCheck(list) {
            let domain = { domain: 'worldhistoryproject.org' }
            axios
            .post('/bot_check', domain)
            .then(res => {
                console.log(res.data)
            })
            .catch(err => {
                console.log(err.response)
            })
        },
    },

    mounted() {
        this.Initiate()
    }
}
</script>
