<template>
    <div class="row align-items-center bg-dark m-0" style="height: 100vh">
        <div class="col-12 p-3">
            <form @submit.prevent="SubmitLogin">
                <input type="text" ref="email" class="form-control mb-2" placeholder="Email">
                <input type="password" ref="password" class="form-control mb-2" placeholder="Password">
                <button type="button" @click="SubmitLogin" class="btn btn-primary w-100 mb-2">
                    <span class="fas fa-sm fa-right-to-bracket me-1"></span>
                    <strong>LOGIN</strong>
                </button>
                <br>
                <div v-show="logStatus == 'OK'" class="alert alert-success text-center py-2">
                    {{ logMessage }}
                </div>
                <div v-show="logStatus == 'Error'" class="alert alert-danger text-center py-2">
                    {{ logMessage }}
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            logStatus: '',
            logMessage: ''
        }
    },

    methods: {
        Initiate() {

        },

        SubmitLogin() {
            let data = {
                email: this.$refs.email.value,
                password: this.$refs.password.value
            }
            axios
            .post('/validateLogin', { data: JSON.stringify(data) })
            .then((res) => {
                this.logStatus = 'OK'
                this.logMessage = 'Login berhasil. Mengarahkan ke Dashboard...'
                setTimeout(() => {
                    window.location.href="/"
                }, 1500);
                // console.log(res.data)
            })
            .catch((err) => {
                this.logStatus = 'Error'
                this.logMessage = err.response.data
            })
        }
    },

    mounted() {
        this.Initiate()
    }
}
</script>
