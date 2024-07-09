<template>
    <Transition>
        <div class="row bg-light" v-if="loaded">
            <div class="col-md-12 p-0 d-flex align-items-center border-bottom">
                <button class="btn btn-lg px-2" @click="Kembali">
                    <span class="fas fa-lg fa-circle-chevron-left text-primary"></span>
                </button>
                <h4 class="ms-auto me-2 my-auto text-secondary">PROFIL</h4>
            </div>
            <div class="col-md-6 mb-3">
                <h4 class="mt-2 text-secondary">Info Personal</h4>

                <label>Nama</label>
                <input type="text" class="form-control mb-2" :value="namaBO" ref="namaBO">
                <div v-if="errMsgNamaBO" class="alert alert-danger text-center py-2 mb-2">
                    {{ errMsgNamaBO }}
                </div>
                <div v-if="successNamaBO" class="alert alert-success text-center py-2 mb-2">
                    {{ successNamaBO }}
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-sm btn-primary" @click="simpanNamaBO">
                        <strong>SIMPAN</strong>
                    </button>
                </div>

                <label>Password Lama</label>
                <input type="password" class="form-control mb-2" ref="passwordLama">
                <label>Password Baru</label>
                <input type="password" class="form-control mb-2" ref="passwordBaru">
                <label>Konfirmasi Password Baru</label>
                <input type="password" class="form-control mb-2" ref="passwordBaruKonf">
                <div v-if="errMsgPassword" class="alert alert-danger text-center py-2 mb-2">
                    {{ errMsgPassword }}
                </div>
                <div v-if="successPassword" class="alert alert-success text-center py-2 mb-2">
                    {{ successPassword }}
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-sm btn-primary" @click="cekPassword">
                        <strong>SIMPAN</strong>
                    </button>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <h4 class="mt-2 text-secondary">Status Berlangganan</h4>
                <h6 class="m-0">Jenis Paket</h6>
                <div class="mb-2">
                    <div class="accordion" id="jenisPaket">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed p-2 bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    {{ packageName }}
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#jenisPaket">
                                <div class="accordion-body p-1">
                                    <div class="row">
                                        <div class="col-6 p-0">Maximum Domain</div>
                                        <div class="col-6 p-0">{{ limitDomain }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 p-0">Durasi Langganan</div>
                                        <div class="col-6 p-0">{{ durasi }} Bulan</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 p-0">Notifikasi Telegram</div>
                                        <div class="col-6 p-0">{{ telegram }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h6 class="m-0">Batas Akhir Langganan</h6>
                <div class="mb-2">
                    <p class="mb-0">
                        {{ subscription }}
                        <strong v-if="subbed" class="ms-1 text-success">Aktif</strong>
                        <strong v-else class="ms-1 text-danger">Tidak Aktif</strong>
                    </p>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script>
export default {
    data() {
        return {
            loaded: false,
            idBO: null,
            namaBO: null,
            subscription: null,
            subbed: false,
            packageName: null,
            limitDomain: null,
            durasi: null,
            telegram: null,
            errMsgNamaBO: null,
            successNamaBO: null,
            errMsgPassword: null,
            successPassword: null,
        }
    },

    methods: {
        Initiate() {
            this.getPackage()
        },

        getPackage() {
            axios
            .get('/get_package')
            .then((res) => {
                this.idBO = res.data.id
                this.namaBO = res.data.nama_bo

                this.packageName = res.data.nama_paket

                let date = new Date(res.data.subscribe)
                let subEnd = new Date(date.setMonth(date.getMonth() + 1))
                let localEnd = subEnd.toLocaleString()
                this.subscription = localEnd.substring(0, 10)

                let today = new Date()
                this.checkSubs(today, subEnd)

                this.limitDomain = res.data.limit_domain
                this.durasi = res.data.durasi
                if (res.data.telegram == 1)
                    this.telegram = res.data.kontak
                else
                    this.telegram = 'Tidak'
            })
            .catch((err) => {
                console.log(err.response.data.message)
            })
            .finally(() => {
                this.loaded = true
            })
        },

        checkSubs(today, subEnd) {
            if(today.getTime() > subEnd.getTime())
                this.subbed = false
            else
                this.subbed = true
        },

        simpanNamaBO() {
            if ( this.$refs.namaBO.value == '' )
                this.errMsgNamaBO = 'Nama tidak boleh kosong'
            else if ( this.$refs.namaBO.value.match(/[`!@#$%^&*()_\-+=\[\]{};':"\\|,.<>\/?~ ]/g) )
                this.errMsgNamaBO = 'Tidak boleh ada karakter spesial'
            else {
                const data = this.$refs.namaBO.value
                axios
                .post('ubah_nama_bo', { data: JSON.stringify(data) })
                .then((res) => {
                    this.successNamaBO = 'Nama berhasil diubah'
                    this.namaBO = data
                })
            }
            setTimeout(() => {
                this.errMsgNamaBO = null
                this.successNamaBO = null
            }, 2000);
        },

        cekPassword() {
            if ( this.$refs.passwordLama.value == '' || this.$refs.passwordBaru.value == '' || this.$refs.passwordBaruKonf.value == '' )
                this.errMsgPassword = 'Semua input harus diisi'
            else if ( this.$refs.passwordLama.value == this.$refs.passwordBaru.value )
                this.errMsgPassword = 'Password lama dan password baru sama'
            else if ( this.$refs.passwordBaru.value != this.$refs.passwordBaruKonf.value )
                this.errMsgPassword = 'Konfirmasi password tidak sama'
            else if ( this.$refs.passwordBaru.value.length < 8 )
                this.errMsgPassword = 'Minimal 8 karakter'
            else {
                const data = this.$refs.passwordLama.value
                axios
                .post('cek_password_bo', { data: JSON.stringify(data) })
                .then((res) => {
                    this.simpanPassword()
                })
                .catch((err) => {
                    this.errMsgPassword = 'Password lama salah'
                })
            }
            setTimeout(() => {
                this.errMsgPassword = null
                this.successPassword = null
            }, 2000);
        },

        simpanPassword() {
            const data = this.$refs.passwordBaru.value
            axios
            .post('ubah_password_bo', { data: JSON.stringify(data) })
            .then((res) => {
                this.successPassword = 'Password berhasil diubah'
            })
        },

        Kembali() {
            history.back()
        },
    },

    mounted() {
        this.Initiate()
    }
}
</script>
