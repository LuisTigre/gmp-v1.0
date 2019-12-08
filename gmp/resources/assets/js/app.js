
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue'
import Topo from './components/Topo.vue'
import Painel from './components/Painel.vue'
import Caixa from './components/Caixa.vue'
import Pagina from './components/Pagina.vue'
import TabelaLista from './components/TabelaLista.vue'
import Migalhas from './components/Migalhas.vue'
import Modal from './components/modal/Modal.vue'
import ModalLink from './components/modal/ModalLink.vue'
import Formulario from './components/Formulario.vue'
import ArtigoCard from './components/ArtigoCard.vue'
import GrupoLista from './components/GrupoLista.vue'
import TabelaPauta from './components/TabelaPauta.vue'
import TabelaMiniPauta from './components/TabelaMiniPauta.vue'
import TabelaHorario from './components/TabelaHorario.vue'
import ButtonLink from './components/ButtonLink.vue'

require('./bootstrap');

window.Vue = require('vue');
import Vuex from 'vuex';
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

window.Vue.component('topo', Topo)

window.Vue.component('painel',Painel);
// window.Vue.component('topo',Topo);
window.Vue.component('caixa',Caixa);
window.Vue.component('pagina',Pagina);
window.Vue.component('tabela-lista',TabelaLista);
window.Vue.component('migalhas',Migalhas);
window.Vue.component('modal',Modal);
window.Vue.component('modallink',ModalLink);
window.Vue.component('formulario',Formulario);
window.Vue.component('artigocard',ArtigoCard);
window.Vue.component('grupo-lista',GrupoLista);
window.Vue.component('tabela-pauta',TabelaPauta);
window.Vue.component('tabela-mini-pauta',TabelaMiniPauta);
window.Vue.component('tabela-horario',TabelaHorario);
window.Vue.component('buttonlink',ButtonLink);

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
