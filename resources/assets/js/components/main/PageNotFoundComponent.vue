
<template>
    <div class="page-wrap d-flex flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 text-center">
                    <span class="display-1 d-block">404</span>
                    <div class="mb-4 lead">Сторінку, яку ви шукаєте, не знайдено.</div>
                    <router-link to="/" class="btn btn-link">На головну</router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'PageNotFound'
    }
</script>
