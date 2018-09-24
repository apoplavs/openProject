<template>
    <div class="courtSessions">
        <div class="container-component">
            <div class="row header text-muted">
                <div class="col-1 pl-0">Дата розгляду</div>
                <div class="col-1">Номер справи</div>
                <div class="col-2">Назва та склад суду</div>
                <div class="col-4">Сторони у справі</div>
                <div class="col-2">Суть справи</div>
                <div class="col-2 pr-0">Примітки</div>
            </div>
            <div class="row">
                <div class="col-1 pl-0">
                    <div>25.09.2018</div>
                    <div>14:15</div>
                </div>
                <div class="col-1">923/623.10</div>
                <div class="col-2">
                    <div>Господарський суд Херсонської області</div>
                    <div>Немченко Л.M.</div>
                </div>
                <div class="col-4">Позивач: Головня Максим Феодосієвич, відповідач: Публічне Акціонерне Товариство Херсонський Суднобудівний завод</div>
                <div class="col-2">Стягнення заборгованості </div>
                <div class="col-2 pr-0">Підготувати документи</div>
            </div>
            <div class="row">
                <div class="col-1 pl-0">
                    <div>25.09.2018</div>
                    <div>14:15</div>
                </div>
                <div class="col-1">923/623.10</div>
                <div class="col-2">
                    <div>Господарський суд Херсонської області</div>
                    <div>Немченко Л.M.</div>
                </div>
                <div class="col-4">Позивач: Головня Максим Феодосієвич, відповідач: Публічне Акціонерне Товариство Херсонський Суднобудівний завод</div>
                <div class="col-2">Стягнення заборгованості </div>
                <div class="col-2 pr-0">Підготувати документи</div>
            </div>
            <div class="row">
                <div class="col-1 pl-0">
                    <div>25.09.2018</div>
                    <div>14:15</div>
                </div>
                <div class="col-1">923/623.10</div>
                <div class="col-2">
                    <div>Господарський суд Херсонської області</div>
                    <div>Немченко Л.M.</div>
                </div>
                <div class="col-4">Позивач: Головня Максим Феодосієвич, відповідач: Публічне Акціонерне Товариство Херсонський Суднобудівний завод</div>
                <div class="col-2">Стягнення заборгованості </div>
                <div class="col-2 pr-0">Підготувати документи</div>
            </div>
            <div class="row">
                <div class="col-1 pl-0">
                    <div>25.09.2018</div>
                    <div>14:15</div>
                </div>
                <div class="col-1">923/623.10</div>
                <div class="col-2">
                    <div>Господарський суд Херсонської області</div>
                    <div>Немченко Л.M.</div>
                </div>
                <div class="col-4">Позивач: Головня Максим Феодосієвич, відповідач: Публічне Акціонерне Товариство Херсонський Суднобудівний завод,
                     Позивач: Головня Максим Феодосієвич, відповідач: Публічне Акціонерне Товариство Херсонський Суднобудівний завод</div>
                <div class="col-2">Стягнення заборгованості </div>
                <div class="col-2 pr-0">Підготувати документи</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CourtSessions"
    }
</script>

<style scoped lang="scss">
.courtSessions {
    width: 100%;
    height: auto;
    margin-top: 50px;
    background-color: #ffffff;
    .container-component {
        padding: 20px;
        font-size: .9rem;
    }
    
    .row {
        margin: 0;
        padding: 10px 0;
        &:not(:last-child){
            border-bottom: 1px solid lightgrey;
        }
    }
}

</style>