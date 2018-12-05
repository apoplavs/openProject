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
                        <button type="button" class="btn-grey" @click="close" aria-label="Close modal">
                            Закрити
                        </button> 
                        <button type="button" class="btn-green" @click="save" aria-label="Close modal">
                            Змінити сатус
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
        methods: {
            close() {
                this.$emit('close');
            },
            save() {
                this.$emit('save');
            }
        },
    }
</script>

<style scoped lang="scss">
    .modal-backdrop {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.3);
        display: flex;
        justify-content: center;
        // align-items: center;
    }
    
    .modal {
        width: 500px;
        height: 400px;
        position: relative;
        border-radius: 4px;
        margin-top: 100px;
        background: #FFFFFF;
        box-shadow: 2px 2px 20px 1px;
        overflow: visible;
        display: flex;
        flex-direction: column;
    }
    
    .modal-header,
    .modal-footer {
        padding: 15px;
        display: flex;
    }
    
    .modal-header {
        border-bottom: 1px solid #eeeeee;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-footer {
        border-top: 1px solid #eeeeee;
        justify-content: flex-end;
    }
    
    .modal-body {
        position: relative;
        padding: 20px 10px;
    }
    
    .btn-close {
        border: none;
        font-size: 20px;
        cursor: pointer;
        font-weight: bold;
        color: #4AAE9B;
        background: transparent;
    }
    
    .btn-green {
        color: white;
        background: #4AAE9B;
        border: 1px solid #4AAE9B;
        border-radius: 4px;
    }
    .btn-grey {
        color: white;
        background: #6a6a6a;
        border: 1px solid #6a6a6a;
        border-radius: 4px;
    }
    
    .modal-fade-enter,
    .modal-fade-leave-active {
        opacity: 0;
    }
    
    .modal-fade-enter-active,
    .modal-fade-leave-active {
        transition: opacity .5s ease
    }
</style>