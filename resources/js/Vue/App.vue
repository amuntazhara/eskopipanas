<template>
    <nav class="navbar navbar-dark bg-dark shadow-sm fixed-top">
        <button type="button" class="btn btn-lg p-0 ms-2" data-bs-toggle="modal" data-bs-target="#addDomain">
            <span class="fas fa-lg fa-plus-circle text-light"></span>
        </button>
        <button type="button" class="btn btn-lg p-0 ms-2" data-bs-toggle="modal" data-bs-target="#dalamPengembangan">
            <span class="fab fa-lg fa-telegram text-light"></span>
        </button>
        <button type="button" class="btn btn-lg p-0 ms-2" data-bs-toggle="modal" data-bs-target="#dalamPengembangan">
            <span class="fas fa-headset text-light"></span>
        </button>
        <button type="button" class="btn btn-lg p-0 ms-auto me-2" @click="Profile">
            <span class="fas fa-lg fa-user-circle text-light"></span>
        </button>
        <button type="button" class="btn btn-lg py-0 px-1 me-2" @click="Logout">
            <span class="fas fa-right-from-bracket text-light"></span>
        </button>
    </nav>
    <div class="clear"></div>
    <Main />
</template>

<script>
export default {
    methods: {
        Profile() {
            window.location.href="/profile"
        },

        Logout() {
            window.location.href="/logout"
        },
    },
}
</script>
