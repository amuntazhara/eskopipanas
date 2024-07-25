<template>
    <div class="row">
        <div class="col-md-9 px-2" style="height: 75vh; overflow: auto">
            <table class="table table-bordered">
                <thead class="text-center">
                    <th>ID</th>
                    <th>Domain</th>
                    <th v-if="idBO == 0">BO</th>
                    <th v-if="idBO == 0">Kontak</th>
                    <th>Jenis Domain</th>
                    <th>Status</th>
                    <th v-if="subbed"></th>
                </thead>
                <tbody class="text-center">
                    <tr v-for="list in listWebsites" :key="list.id">
                        <td class="px-1" style="width: 10% !important">{{ list.id }}</td>
                        <td class="text-start px-1" style="width: 5% !important">{{ list.domain }}</td>
                        <td v-if="idBO == 0" class="px-1">{{ list.nama_bo }}</td>
                        <td v-if="idBO == 0" class="px-1">{{ list.kontak }}</td>
                        <td class="px-1" style="width: 3% !important">
                            <strong v-if="list.jenis == 1" class="text-success">MS Utama</strong>
                            <strong v-else-if="list.jenis == 2" class="text-primary">Redirector</strong>
                            <strong v-else-if="list.jenis == 3" class="text-secondary">MS Cadangan</strong>
                            <strong v-else-if="list.jenis == 4" class="text-secondary">MS Nawala</strong>
                            <strong v-else class="text-danger">Tidak Diketahui</strong>
                        </td>
                        <td v-if="subbed" class="px-1" style="width: 5% !important">
                            <strong v-if="list.status == 'allowed'" class="text-success">AKTIF</strong>
                            <strong v-else-if="list.status == 'blocked'" class="text-danger">NAWALA</strong>
                            <strong v-else-if="list.status == 'Invalid domain format'" class="text-danger opacity-75">Format domain salah</strong>
                            <strong v-else-if="list.status == 'Error resolving IP address for ' + list.domain" class="text-danger opacity-75">Domain tidak ditemukan</strong>
                            <strong v-else-if="list.status == 'backup'" class="text-secondary">BACKUP</strong>
                            <span v-else>{{ list.status }}</span>
                        </td>
                        <td v-else style="width: 5%">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        </td>

                        <td class="px-0" style="width: 5% !important" v-if="subbed">
                            <!-- SUBSCRIBED -->
                            <button class="btn btn-sm py-0 px-1">
                                <span class="fas fa-sm fa-edit text-dark" data-bs-toggle="modal" data-bs-target="#editDomain" @click="getDomainId(list.id, list.domain, list.jenis)"></span>
                            </button>
                            <button class="btn btn-sm py-0 px-1">
                                <span class="fas fa-sm fa-trash text-danger" data-bs-toggle="modal" data-bs-target="#deleteDomain" @click="getDomainId(list.id, list.domain, list.jenis)"></span>
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
                    <br>
                    <select ref="jenisDomain" class="form-select">
                        <option value="0" selected>Jenis Domain</option>
                        <option value="1">MS Utama</option>
                        <option value="2">Redirector</option>
                        <option value="3">MS Cadangan</option>
                    </select>
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
                            <li class="m-0">Tidak ada kesalahan input domain.</li>
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
                    <br>
                    <select ref="jenisDomainEdit" class="form-select" :value="jenisDomain">
                        <option value="0" selected>Jenis Domain</option>
                        <option value="1">MS Utama</option>
                        <option value="2">Redirector</option>
                        <option value="3">MS Cadangan</option>
                    </select>
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
                            <li class="m-0">Tidak ada kesalahan input domain.</li>
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
            id_telegram: null,
            telegram: null,
            subscription: null,
            errorAddDomain: false,
            errMsg: '',
            successAddDomain: false,
            successMsg: '',
            idDomain: null,
            domainName: null,
            jenisDomain: null,
            waktuCek: null,
            listWebsites: {},
            subbed: false,
            domainStatus: '',
            botContent: [],
        }
    },

    methods: {
        Initiate() {
            this.getSession()

            setInterval(() => {
                this.getSession(this.listWebsites)
            }, 600000);
        },

        Tambah() {
            this.errorAddDomain = false
            this.errMsg = ''
            this.successAddDomain = false
            this.successMsg = ''

            if (this.$refs.domainName.value == '' || this.$refs.domainName.value == null) {
                this.errorAddDomain = true
                this.errMsg = 'Nama domain wajib diisi.'
            } else if (this.$refs.jenisDomain.value == '' || this.$refs.jenisDomain.value == null) {
                this.errorAddDomain = true
                this.errMsg = 'Jenis domain wajib diisi.'
            } else {
                let data = {
                    domain: this.$refs.domainName.value,
                    jenis: this.$refs.jenisDomain.value,
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
            } else if (this.$refs.jenisDomainEdit.value == '' || this.$refs.jenisDomainEdit.value == null) {
                this.errorAddDomain = true
                this.errMsg = 'Jenis domain wajib diisi.'
            } else {
                let data = {
                    domain: this.$refs.domainNameEdit.value,
                    jenis: this.$refs.jenisDomainEdit.value,
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

        getDomainId(id, domain, jenis) {
            this.domainName = domain
            this.idDomain = id
            this.jenisDomain = jenis
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
                this.id_telegram = res.data.id_telegram
                this.telegram = res.data.telegram

                let date = new Date(res.data.subscription)
                let subEnd = new Date(date.setMonth(date.getMonth() + 1))
                let localEnd = subEnd.toLocaleString()
                this.subscription = localEnd.substring(0, 10)
                let today = new Date()
                this.checkSubs(today, subEnd)
            })
            .finally(() => {
                this.getWebsites()
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
                this.listWebsites.forEach((data, index) => {
                    const text = "" + data.id + ""
                    data.id = text.padStart(4, '0')
                    data.status = 'Check...'
                })
            })
            .catch((err) => {
                console.log(err.response.data)
            })
            .finally(() => {
                this.checkWebsites(this.listWebsites)
            })
        },

        checkWebsites(list) {
            // this.botContent = []
            var i = list.length
            list.forEach((data, index) => {
                let domain = { domain: data }
                axios
                .post('/fetch_data', {data: JSON.stringify(domain)})
                .then(res => {
                    console.log(res.data)
                    console.log(res.data.status)
                    if (res.data.status != undefined)
                        this.listWebsites[index].status = res.data.status
                    else
                        this.listWebsites[index].status = res.data.error
                })
                .catch(err => {
                    console.log(err.response)
                })
                .finally(() => {
                    this.getLastUpdate()

                    // if (data.status == 'allowed')
                    //     this.botContent.push({
                    //         'domain': data.domain,
                    //         'status': 'aktif'
                    //     }),
                    //     this.domainStatus = 'aktif'
                    // else if (data.status == 'blocked')
                    //     this.botContent.push({
                    //         'domain': data.domain,
                    //         'status': 'nawala'
                    //     }),
                    //     this.domainStatus = 'nawala',
                    //     this.redirectWeb(data)
                    // else
                    //     this.domainStatus = 'error'

                    i -= 1
                    if (i == 0) {
                        console.log(list)
                        if (this.id_telegram != null && this.telegram != 0 && this.subbed == true)
                            this.botTeleCheck(list)
                    }
                })
            })
        },

        getLastUpdate() {
            const fullDate = new Date()
            const date = fullDate.toLocaleString()
            this.waktuCek = date
        },

        botTeleCheck(list) {
            let content = list
            axios
            .post('/bot_check', { content })
            .then(res => {
                console.log(res.data)
            })
            .catch(err => {
                console.log(err.response)
            })
        },

        // botTeleRedirect(botContent) {
        //     let content = botContent
        //     axios
        //     .post('/bot_check_redirect', { content })
        //     .then(res => {
        //         console.log(res.data)
        //     })
        //     .catch(err => {
        //         console.log(err.response)
        //     })
        // },

        // redirectWeb(data) {
        //     let domain = { data: data }
        //     axios
        //     .post('/redirect', {data: JSON.stringify(domain)})
        //     .then(res => {
        //         console.log(res)
        //         if (this.id_telegram != null && this.telegram != 0 && this.subbed == true)
        //             this.botTeleRedirect(res.data)
        //     })
        //     .catch(err => {
        //         console.log(err.response)
        //         return err.response.data
        //     })
        // },
    },

    mounted() {
        this.Initiate()
    }
}
</script>
