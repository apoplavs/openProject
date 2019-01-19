<template>
    <transition name="modal-fade">
        <div class="modal-backdrop">
            <div class="modal" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDescription">
                <header class="modal-header" id="modalTitle">
                    <slot name="header"></slot>
                    <button type="button" class="btn-close" @click="close" aria-label="Close modal">x</button>
                </header>
                <section class="modal-body" id="modalDescription">
                    <slot name="body"></slot>
                </section>
                <footer class="modal-footer">
                    <slot name="footer">
                        <button type="button" class="btn-grey" @click="close">
                            Відмінити
                        </button> 
                        <button type="button" class="btn-green" @click="confirm">
                            Видалити
                        </button>                        
                    </slot>
                </footer>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        name: "Modal",
        props: {
            modalConfirm: {
                default: false,
                type: Boolean
            }
        },
        methods: {
            close() {
                this.$emit('close');
            },
            confirm() {
                this.$emit('confirm');
            },
        },
    }
</script>

<style scoped lang="scss">
    @import "../../../sass/modal.scss";
</style>