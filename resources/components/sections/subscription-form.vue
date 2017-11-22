<template>
    <div class="subscription-form-section-component">
        <form action="#" method="POST" accept-charset="UTF-8" @submit.prevent="submit">
            <div class="notification" :class="[ notifyStatus, { visible: notifyStatus !== '' } ]">{{ notifyText }}</div>
            <input type="text" v-model="form.email" name="email" placeholder="Email" />
            <input type="text" v-model="form.name" name="name" placeholder="Name" />
            <input type="submit" name="submit" value="Subscribe" />
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                submitting: false,
                notifyStatus: "",
                notifyText: "",

                form: {
                    email: "",
                    name: ""
                }
            };
        },

        methods: {
            submit() {
                if (!this.submitting) {
                    this.submitting = true;
                    this.notifyStatus = "";
                    $(this.$el).find(":input.error").removeClass("error");

                    this.$http.post("/api/subscription-submit" + env.apiToken, JSON.stringify(this.form)).then((response) => {
                        // Success
                        $(this.$el).find(":input").fadeOut(150);
                        this.notifyText = "Thanks for subscribing!";
                        this.notifyStatus = "success";
                        this.submitting = false;
                    }, (response) => {
                        // Error
                        let errors = 0;

                        for (let errorName in response.body.errors) {
                            if ($(this.$el).find(`[name='${errorName}']`).length) {
                                $(this.$el).find(`[name='${errorName}']`).addClass("error");
                                errors++;
                            }
                        }

                        if (errors === 0) {
                            this.notifyText = "An error occurred. Are you already subscribed?";
                            this.notifyStatus = "error";
                        }

                        this.submitting = false;
                    });
                }
            }
        }
    };
</script>
