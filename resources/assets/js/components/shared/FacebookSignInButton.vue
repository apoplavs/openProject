<template>
	<fb-signin-button
			:params="fbSignInParams"
			@success="onSignInSuccess"
			@error="onSignInError">
			<img width="30" src="../../../images/facebook.png"/>
	</fb-signin-button>
</template>

<script>
	import Vue from 'vue';
	import FBSignInButton from 'vue-facebook-signin-button';

	Vue.use(FBSignInButton);

	window.fbAsyncInit = function() {
		FB.init({
			appId      : '297048331072736',
			cookie     : true,  // enable cookies to allow the server to access the session
			xfbml      : true,  // parse social plugins on this page
			version    : 'v2.8' // use graph api version 2.8
		});
	};
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	export default {
		name: "login-facebook",
		data() {
			return {
				fbSignInParams: {
					scope: 'email, public_profile',
					return_scopes: true
				}
			}
		},
		methods: {
			getUserData(callback) {
				axios
					.get('/api/v1/user', {
						headers: {
							"Content-Type": "application/json",
							"X-Requested-With": "XMLHttpRequest",
							"Authorization": localStorage.getItem('token')
						}

					})
					.then(response => {
						let user = {
                            name: response.data.name,
                            email: response.data.email
                        }                      
                        localStorage.setItem('user', JSON.stringify(user));     
                        this.$store.commit('auth_success', response.data.email);
						callback();
					})
					.catch(error => {
						console.log('ERR ', error);
					});
			},
			onSignInSuccess (response) {
				const _this = this;
				FB.api('/me', 'GET', {fields: 'id,email,first_name,last_name'}, user_data => {
					user_data.name = user_data.first_name;
					user_data.surname = user_data.last_name;

					axios.post('/api/v1/login/facebook', user_data, {
						headers: {
							'Content-Type': 'application/json',
							'X-Requested-With': 'XMLHttpRequest'
						}
					}).then(response => {
						if (response) {
							let token =
								response.data.token_type + " " + response.data.access_token;
							localStorage.setItem("token", token);
							_this.getUserData( () => {
								_this.$router.push("/user-profile");
							});
						}
					}).catch(error => {
						if (error.response && error.response) {
							if (error.response.data && error.response.data.message) {
								this.$toasted.error(error.response.data.message, {
									theme: "primary",
									position: "top-right",
									duration: 15000
								});
							}
						} else {
							this.$toasted.error("Щось пішло не так :(  Спробуйте ввійти через google", {
								theme: "primary",
								position: "top-right",
								duration: 15000
							});
							alert(error);
						}
					});
				});
			},
			onSignInError (error) {
				this.$toasted.error('Щось пішло не так :(  Спробуйте ввійти іншим способом', {
					theme: "primary",
					position: "top-right",
					duration : 10000
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	@import "../../../sass/_variables.scss";
	@import "../../../sass/_mixins.scss";

	.fb-signin-button {
		display: inline-block;
		padding: 4px 8px;
		border-radius: 3px;
		background-color: #4267b2;
		color: #fff;
		cursor: pointer;
	}

</style>