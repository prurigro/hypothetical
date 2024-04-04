<template>
    <div class="contact-page-component">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-8 offset-md-2">
                    <h1>Contact</h1>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-8 offset-md-2">
                    <div id="contact-form">
                        <form action="#" method="POST" accept-charset="UTF-8" @submit.prevent="submit">
                            <input
                                v-model="form.name"
                                name="name"
                                type="text"
                                placeholder="Name"
                            />

                            <input
                                v-model="form.email"
                                name="email"
                                type="text"
                                placeholder="Email"
                            />

                            <textarea
                                v-model="form.message"
                                name="message"
                                placeholder="Message">
                            </textarea>

                            <input
                                class="submit"
                                :class="{ disabled: submitSuccess }"
                                type="submit"
                                name="submit"
                                value="Submit"
                            />
                        </form>

                        <div
                            class="notification"
                            :class="{ success: submitSuccess, visible: errorCount > 0 || submitSuccess }">

                            <template v-if="submitSuccess">
                                Thanks for your message!
                            </template>

                            <template v-else>
                                <strong>Error:</strong> There were problems with the <span>{{ errorCount }}</span> fields highlighted above
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import BasePageMixin from "mixins/base-page.js";

    export default {
        mixins: [
            BasePageMixin
        ],

        data() {
            return {
                submitting: false,
                errorCount: 0,
                submitSuccess: false,

                form: {
                    name: "",
                    email: "",
                    message: ""
                }
            };
        },

        methods: {
            submit() {
                if (!this.submitting) {
                    this.submitting = true;
                    $(this.$el).find(":input.error").removeClass("error");

                    this.$http.post("/api/contact-submit" + env.apiToken, this.form).then((response) => {
                        // Success
                        $(this.$el).find(":input").attr("disabled", true);
                        this.errorCount = 0;
                        this.submitSuccess = true;
                        this.submitting = false;
                    }).catch((error) => {
                        // Error
                        let errors = 0;

                        for (let errorName in error.response.data.errors) {
                            if ($(this.$el).find(`[name='${errorName}']`).length) {
                                $(this.$el).find(`[name='${errorName}']`).addClass("error");
                                errors++;
                            }
                        }

                        this.errorCount = errors;
                        this.submitting = false;
                    });
                }
            }
        }
    };
</script>
