import './mainCtrl.css';
import Swal from 'sweetalert2';
const $ = require('jquery');
global.$ = global.jQuery = $;
const angular = require('angular');

const baseURL = '/admin/acc_mgmt';

let app =
    angular.module("adminApp", [])

        .config(["$interpolateProvider", function ($interpolateProvider) {
            //On change les symboles d'interpolation par défaut d'AngularJs
            $interpolateProvider.startSymbol('//').endSymbol('//');

        }])

        .controller("mainCtrl", ["$scope", "$http", "$timeout", "$log", function($scope, $http, $timeout, $log) {
            $scope.Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
            });

            $scope.roles = [
                {id:0, lib:'ROLE_ADMIN'}, {id:1, lib:'ROLE_JURIDIQUE'}, {id:2, lib:'ROLE_USER_MANAGER'}, {id:3, lib:'ROLE_USER_BOSS_JURIDIQUE'}, {id:4, lib:'ROLE_USER'}
            ];
            $scope.dataReady = true;
            $scope.beginPosTable = 0;
            $scope.dep = [];
            $scope.filters = [];
            $scope.panelUpdate = false;
            $scope.panelCreate = false;
            $scope.actualUser = {};
            $scope.newPassword = "";
            $scope.repeatedNewPassword = "";

            $scope.init = function(){
                //Envoi des données au serveur
                $http.post(baseURL + '/list/departement', $scope.chosenFilters).then(
                    //En cas de succès
                    function(response) {
                        //Données
                        $scope.dep = response.data;
                    },
                    //Erreur
                    function(response) {
                        $scope.data = response.data || 'Request failed';
                        $scope.status = response.status;
                    });
            }
            //Récupérer les données depuis le serveur
            $scope.fetchData = function(){
                $scope.chosenFilters = {
                  'departement' : $scope.filters.departement
                };
                //Envoi des données au serveur
                $http.post(baseURL + '/list', $scope.chosenFilters).then(
                    //En cas de succès
                    function(response) {
                        //Code de statut
                        $scope.status = response.status;
                        //Données
                        $scope.data = response.data;
                        let result = $scope.data;
                        console.log(response.data);
                    },
                    //Erreur
                    function(response) {
                        $scope.data = response.data || 'Request failed';
                        $scope.status = response.status;
                    });
            }

            $scope.changeUser = function(id){
                $scope.panelUpdate = true;
                $scope.panelCreate = false;
                $scope.actualUser = angular.copy($scope.data[id]);
            }

            $scope.updateUser = function(){
                $http.post(baseURL + '/update_user', $scope.actualUser).then(
                    //En cas de succès
                    function(response) {
                        //Code de statut
                        $scope.status = response.status;
                        //Données
                        $scope.data = response.data;
                        let result = $scope.data;
                        console.log(response.data);
                        $scope.Toast.fire({
                            icon: 'success',
                            title: 'Le compte ' + $scope.actualUser.email + ' a été mis à jour.'
                        });
                        $scope.actualUser = {};
                    },
                    //Erreur
                    function(response) {
                        $scope.data = response.data || 'Request failed';
                        $scope.status = response.status;
                        $scope.Toast.fire({
                            icon: 'error',
                            title: 'Erreur serveur !.'
                        });
                        $scope.actualUser = {};
                    });
                $scope.fetchData();
                $scope.panelUpdate = false;
            }

            $scope.createUser = function(){
                $http.post(baseURL + '/create_user', $scope.actualUser).then(
                    //En cas de succès
                    function(response) {
                        //Code de statut
                        $scope.status = response.status;
                        //Données
                        $scope.data = response.data;
                        let result = $scope.data;
                        console.log(response.data);
                        $scope.Toast.fire({
                            icon: 'success',
                            title: 'Compte créé avec succés.'
                        });
                    },
                    //Erreur
                    function(response) {
                        $scope.data = response.data || 'Request failed';
                        $scope.status = response.status;
                        $scope.Toast.fire({
                            icon: 'error',
                            title: 'Erreur serveur !.'
                        });
                    });
                $scope.panelCreate = false;
                $scope.actualUser = {};
                $scope.fetchData();
            }

            $scope.checkPassword = function(){
                return ($scope.newPassword.length > 7) && ($scope.newPassword === $scope.repeatedNewPassword);
            }

            $scope.changePassword = function(){
                $http.post(
                    baseURL + '/update_password',
                    {
                        'i': $scope.actualUser.email,
                        'p': $scope.newPassword
                    }
                ).then(
                    //En cas de succès
                    function(response) {
                        //Code de statut
                        $scope.status = response.status;
                        //Données
                        $scope.data = response.data;
                        let result = $scope.data;
                        console.log(response.data);
                        $scope.Toast.fire({
                            icon: 'success',
                            title: 'Le mot du passe du compte ' + $scope.actualUser.email + ' a été mis à jour.'
                        });
                    },
                    //Erreur
                    function(response) {
                        $scope.data = response.data || 'Request failed';
                        $scope.status = response.status;
                        $scope.Toast.fire({
                            icon: 'error',
                            title: 'Erreur serveur !.'
                        });
                    });
                $scope.newPassword = null;
                $scope.repeatedNewPassword = null;
                $scope.fetchData();
                $scope.panelUpdate = false;
            }

            $scope.moveTable = function (direction){
                if(direction){
                    $scope.beginPosTable += ($scope.beginPosTable > ($scope.data.length - 15) ) ? 0 : 15;
                    return;
                }
                $scope.beginPosTable -= ($scope.beginPosTable > 14) ? 15 : 0;
            }

            $scope.resetFilters = function(){
                delete $scope.mailSearch;
                $scope.filters = {};
                $scope.fetchData();
            }

            $scope.fetchData();
            $scope.init();







            //Lien de démarrage des tableaux par défaut
            $scope.tableUrl = baseURL + "table/init";
            //Lien de démarraage des filtres par défaut
            $scope.filterUrl = baseURL + "filter/init";

            //Champs en-tête du tableau
            $scope.headers = [];
            //Valeurs de remplissaage dans le tableau
            $scope.filtersData = [];

            //Affichage d'un loader si les données n'ont pas encore été reçues
            $scope.dataReady = true;

            //Filtres choisies
            $scope.chosenFilters = {};
            //Filters sur le côté
            $scope.filters = [
               /* {
                    id: 1,
                    lib: "Demande de contrats",
                    slug: 'demande_contrat',
                    active: false
                },*/
                {
                    id: 1,
                    lib: "Demande d'avis et consultations",
                    slug: 'avis',
                    active: false
                },
                {
                    id: 2,
                    lib: "Demande d'autorisations",
                    slug: 'autorisation',
                    active: false
                },
                {
                    id: 3,
                    lib: "Contrats",
                    slug: 'contrat',
                    active: false
                },
                {
                    id: 4,
                    lib: "Affaires contentieuses et litiges",
                    slug: 'litige',
                    active: false
                },
                {
                    id: 5,
                    lib: "Obligations",
                    slug: 'obligations',
                    active: false
                },
                {
                    id: 6,
                    lib: "Collaborateurs service juridique",
                    slug: 'collaborateurs_juridique',
                    active: false
                }
            ];

            //Afficher les filtres ?
            $scope.displayFilters = false;

            //Fonction pour nettoyer les filtres choisis
            $scope.clearFilters = function(){
                delete $scope.chosenFilters;
                $scope.chosenFilters = {};
            }

            $scope.launchFilter = function(filterId){
                //Activation de notre bouton et désactivation des autres
                for (let i = 0; i < $scope.filters.length; i++){
                    $scope.filters[i].active = false;
                }
                $scope.filters[filterId].active = true;


                $scope.filterUrl = baseURL + "filter/" + $scope.filters[filterId].slug;
                $scope.chosenFilters = {};
            }


            //Récupérer les données depuis le serveur
            $scope.reportToMail = function(val){
                //On prend le nom de l'objet recherché
                $scope.chosenFilters['processStr'] = false;
                $scope.filters.map(x => {
                   $scope.chosenFilters['processStr'] = x.active ? x.slug : $scope.chosenFilters['processStr'];
                });
                //Si aucun élément choisi, on ne fait rien
                if(!$scope.chosenFilters['processStr']){
                    return;
                }
                //On ajoute le format de fichier
                $scope.chosenFilters['format'] = val;

                //On affiche le loader
                $scope.dataReady = false;

                //Envoi des données au serveur
                $http.post(baseURL + 'report-to-mail', $scope.chosenFilters).then(
                    //En cas de succès
                    function(response) {
                        //Code de statut
                        $scope.status = response.status;
                        //Données
                        $scope.data = response.data;
                        let result = $scope.data;

                        //On affiche le loader
                        $scope.dataReady = true;
                        alert('Mail envoyé')
                    },
                    //Erreur
                    function(response) {
                        $scope.data = response.data || 'Request failed';
                        $scope.status = response.status;
                    });
            }
        }]);
