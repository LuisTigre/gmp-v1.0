
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import Vuex from 'Vuex';
Vue.use(Vuex);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
 const store = new Vuex.Store({
 	state:{
 		item:{}
 	},
 	mutations:{
 		setItem(state,obj){
 			state.item = obj;
 		}
 	}
 });

Vue.component('painel', require('./components/Painel.vue'));
Vue.component('topo', require('./components/Topo.vue'));
Vue.component('caixa', require('./components/Caixa.vue'));
Vue.component('pagina', require('./components/Pagina.vue'));
Vue.component('tabela-lista', require('./components/TabelaLista.vue'));
Vue.component('migalhas', require('./components/migalhas.vue'));
Vue.component('modal', require('./components/modal/Modal.vue'));
Vue.component('modallink', require('./components/modal/ModalLink.vue'));
Vue.component('formulario', require('./components/Formulario.vue'));
Vue.component('artigocard', require('./components/artigocard.vue'));
Vue.component('grupo-lista', require('./components/GrupoLista.vue'));
Vue.component('tabela-pauta', require('./components/TabelaPauta.vue'));
Vue.component('tabela-mini-pauta', require('./components/TabelaMiniPauta.vue'));
Vue.component('tabela-horario', require('./components/TabelaHorario.vue'));
Vue.component('buttonlink', require('./components/ButtonLink.vue'));

const app = new Vue({
    el: '#app',
    store,
    methods:{
    	printme(){
    		window.print();
    	}
    },
    mounted:function(){
    	document.getElementById('app').style.display = "block";
    }
});
